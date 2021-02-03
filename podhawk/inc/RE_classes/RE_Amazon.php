<?php

class RE_Amazon
{
	private $amazon; // instance of S3
	private $fileName; // name of file to be up- or downloaded
	private $reg; //instance of Registry
	private $bucket; // name of S3 'bucket'
	private $audioFolder = AUDIOPATH; // the location in the PodHawk site where the audio file is loaded from or to


	public function __construct()
	{
		$this->reg = Registry::instance();

		require_once PATH_TO_ROOT . '/podhawk/lib/S3.php';

		$this->amazon = new S3 ($this->reg->findSetting('amazon_access'), $this->reg->findSetting('amazon_secret'));

		$this->amazon->setExceptions(true); // we want Exceptions instead of error messages

		$this->bucket = $this->reg->findSetting('amazon_bucket');
	}

	public function setBucket($bucket)
	{
		$this->bucket = $bucket;
	}

	public function setAudioFolder($path) //NB with trailing slash
	{
		$this->audioFolder = $path;
	}

	public static function isAmazon ($file) // tests if a file is a file in your Amazon bucket
	{
		$reg = Registry::instance();

		$bucket = $reg->findSetting('amazon_bucket');

		if (empty($bucket)) return false;		
		
		$regex = "@^(http://|https://)?" . $bucket . "\.s3\.amazonaws\.com@";
		$return = preg_match($regex, $file);

		return $return;		
	}

	public function upload($file, $id)
	{
		try
		{
			$buckets = $this->amazon->listBuckets();

			if (!in_array($this->bucket, $buckets))
			{
				$this->amazon->putBucket($this->bucket, S3::ACL_PUBLIC_READ);
			}

			$fileToUpload = $this->audioFolder . $file;

			$success = $this->amazon->putObject($this->amazon->inputFile($fileToUpload, false), $this->bucket, $file, S3::ACL_PUBLIC_READ);
		}
		catch (S3Exception $e)
		{
			throw new PodhawkException("Upload of file $file to Amazon S3 failed. Error message is " . $e->getMessage());
		}

		$preparedStatementArray = array	(	':filelocal' 	=> 0,
											':audio_file' 	=> "http://{$this->bucket}.s3.amazonaws.com/$file",
											':id' 			=> $id);

		$this->amendPosting($preparedStatementArray);

		if (RETAIN_ON_AMAZON_UPLOAD == false)
		{
			$permissions = new Permissions(array('audio'));
			$permissions->make_writable('audio');	
			unlink ($fileToUpload);
			$permissions->make_not_writable('audio');
		}

		return true;
	}

	public function download($file, $id)
	{
		$file = basename($file);

		if (!file_exists(AUDIOPATH . $file)) // download the file if a copy does not already exist in the audio directory
		{
			try
			{
				$permissions = new Permissions(array('audio'));
				$permissions->make_writable('audio');
				$fp = fopen(AUDIOPATH . $file, 'wb');	
				$success = $this->amazon->getObject($this->bucket, $file, $fp);
				@fclose ($fp);
				$permissions->make_not_writable('audio');
			}
			catch (S3Exception $e)
			{
				@fclose ($fp);
				$permissions->make_not_writable('audio');
				throw new PodhawkException("Download of file $file from Amazon S3 failed. Error message is " . $e->getMessage());
			}
		}

		$preparedStatementArray = array (':filelocal' 	=> 1,
											':audio_file' 	=> $file,
											':id' 			=> $id);

		$this->amendPosting($preparedStatementArray);			
		
		$this->deleteAmazonObject ($file);
	}

	public function deleteAmazonObject($file)
	{
		try
		{
			$file = basename($file);

			if (RETAIN_ON_AMAZON_UPLOAD == false)
			{	
				$success = $this->amazon->deleteObject($this->bucket, $file);
			}
		}
		catch (S3Exception $e)
		{
			throw new PodhawkException ("Could not delete $file in Amazon S3. Error message is {$e->getMessage()}");
		}
		return $success;
	}

	private function amendPosting($preparedStatementArray)
	{
		try
		{
			$dosql = "UPDATE " . DB_PREFIX . "lb_postings SET
						filelocal = :filelocal,
						audio_file = :audio_file
						WHERE
						id = :id";

			$GLOBALS['lbdata']->prepareStatement($dosql);

			$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);
		}
		catch (Exception $e)
		{
			throw new PodhawkException("Error in updating database following upload/download of {$preparedStatementArray[':audio_file']}. Error message is " . $e->getMessage());
		}
		
	}

		
}

?>
