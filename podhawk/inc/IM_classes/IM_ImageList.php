<?php

class IM_ImageList
{
	private $imageList = array(); // an alphabetically sorted array of file names
	private $log; // instance of errorLog
	private $ipage = '';
	private $message = '';
	private $smartyData = array(); // array of values to pass to Smarty

	public function __construct()
	{
		$this->log = LO_ErrorLog::instance();

		$this->createImageList();	
	}

	public function getList()
	{
		return $this->imageList;
	}

	public function getImageData()
	{
		$imageData = array();

		foreach ($this->imageList as $image)
		{
			$imageManager = new IM_Image($image);

			$imageData[] = $imageManager->getImageData();
		}

		return $imageData;
	}			

	public function countList()
	{
		return count($this->imageList);
	}

	public function getTimeOrderedList()
	{
		return $this->sortList('time');
	}

	public function deleteFromList($image)
	{
		foreach ($this->imageList as $key=>$value)
		{
			if ($value == $image)
			{
				unset ($this->imageList[$key]);
			}
		}

		$_SESSION['imageList'] = $this->imageList;
	}

	public function amendList($oldname, $newname)
	{
		foreach ($this->imageList as $key=>$value)
		{
			if ($value == $oldname)
			{
				$this->imageList[$key] = $newname;
			}
		}

		$this->imageList = $this->sortList('alpha');

		$_SESSION['imageList'] = $this->imageList;
	}

	public function add ($image)
	{
		$this->imageList[] = $image;

		$this->imageList = array_unique($this->imageList); // in case $image is already in the list

		$this->imageList = $this->sortList('alpha');

		$_SESSION['imageList'] = $this->imageList;
	}		 

	private function createImageList()
	{
		// if we have just uploaded some images
		if (!empty($_REQUEST['choose_3']))
		{
			// turn space-separated list into an array
			$images = explode(' ', $_REQUEST['choose_3']);

			foreach ($images as $image)
			{
				if (IM_Image::isValidImage($image))
				{
					$this->imageList[] = $image;
				}
			}
		}
		elseif (!empty($_REQUEST['choose_1']) || !empty($_REQUEST['choose_2'])) // we have some search terms
		{
			// some default values
			$alpha 	= (!empty($_REQUEST['choose_1'])) ? $_REQUEST['choose_1'] : '';
			$time 	= (!empty($_REQUEST['choose_2'])) ? $_REQUEST['choose_2'] : 1;

			$allImages = $this->findImagesInImagesDir();

			foreach ($allImages as $image)
			{

				if ($this->alphaSearch($alpha, $image) && $this->timeSearch($time, $image))
				{
					$this->imageList[] = $image;
				}
			}

		}
		elseif (!empty($_SESSION['imageList'])) // we retrieve an earlier list from session variables
		{
			$this->imageList = $_SESSION['imageList'];			
		}

		$this->imageList = $this->sortList('alpha');

		$_SESSION['imageList'] = $this->imageList;
	}
	
	private function sortList($criterion='alpha')
	{
		if ($criterion == 'alpha')
		{
			$list = $this->imageList;
			natcasesort($list);
		}
		elseif ($criterion == 'time')
		{
			$list = $this->imageList;

			usort($list, array(get_class($this), 'sortByChangeTime'));

		}
		
		return $list;
	}

	public static function sortByChangeTime($file1, $file2)
	{
    	return (filectime($file1) < filectime($file2)); 
	}


	private function findImagesInImagesDir()
	{
		$return = array();

		$dh = opendir(IMAGES_PATH); // read through the images directory to find the images matching the search criteria

		while ($image = readdir($dh))
		{
	  		if (IM_Image::isValidImage($image))
			{
				$return[] = $image;
			}
		}
		return $return;
	}

	private function alphaSearch($criterion, $image)
	{

		if (empty($criterion))
		{
			$regex ="/^./i"; // will return anything
		}
		else
		{
			$regex = "/^".$criterion."/i"; // will return names beginning with $criterion
		}
		return preg_match($regex, $image);
	}

	private function timeSearch($criterion, $image)
	{
		$imagePath = IMAGES_PATH . $image;

		switch ($criterion)
		{
			case 1:
			$time_criterion = 0;
			break;
			case 2:
			$time_criterion = time() - (60*60*24);
			break;
			case 3:
			$time_criterion = time() - (60*60*24*7);
			break;
			case 4:
			$time_criterion = time() - (60*60*24*30);
			break;
			default:
			return false;
		}

		return (filectime($imagePath) > $time_criterion );
	}
	  
}

?>
