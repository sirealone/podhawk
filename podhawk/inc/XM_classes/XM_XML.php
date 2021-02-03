<?php

abstract class XM_XML
{
	protected $file = ''; // the SimpleXML object containing details of the xml file
	protected $xmlPath = ''; // the location (from server root) or URL of the XML file.

	public function __construct($xmlfile)
	{
		$this->log = LO_ErrorLog::instance();

		try
		{
			if (!extension_loaded('SimpleXML'))
			{
				throw new Exception("The SimpleXML Extension is not loaded.");
			}

			libxml_use_internal_errors(true);

			// if we have the address of an xml file
			if (getExtension($xmlfile) == 'xml')
			{
				// if we have the url of an external xml file
				if (substr($xmlfile, 0, 7) == 'http://')
				{
					@$this->file = simplexml_load_file($xmlfile);

					if (empty($this->file) && function_exists('curl_setopt'))
					{
				
						$temp = AUDIOPATH . "temp.xml";
						$ch = curl_init($xmlfile);
						$fp = fopen($temp, "w");
						curl_setopt($ch, CURLOPT_FILE, $fp);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_exec($ch);
						curl_close($ch);
						fclose($fp);
		
						@$this->file = simplexml_load_file($temp);
						unlink ($temp);
					}			

					$errors = libxml_get_errors();
	
					if (!empty($errors))
					{
						$this->log->writeArray($errors);
						throw new Exception ("There are errors in the XML file $xmlfile.");
					}
				}
				
				// if we have the internal address of the file
				elseif (file_exists($xmlfile))
				{

					$this->file = simplexml_load_file($xmlfile);

					if (!$this->file)
					{
						$errors = libxml_get_errors();
						$this->log->writeArray($errors);
						throw new Exception ("There are errors in the XML file $xmlfile");
					}
				}
				
				$this->xmlPath = $xmlfile;

			}
			
			else // perhaps we have xml as a string
			{
				@$this->file = simplexml_load_string($xmlfile);

				if (!$this->file)
				{
					$errors = libxml_get_errors();
					$this->log->writeArray($errors);
					throw new Exception ("There are errors in the XML file.");
				}
			}
		}
		catch (Exception $e)
		{
			$class = get_class($this);
			$this->log->write("Cannot create object of class $class. " . $e->getMessage());
		}
	}
		
}
?>
