<?php

class IM_Image
{
	
	private $image = ''; // just the filename + extension. We assume that it is in the 'images' directory
	private $imagePath = '';
	private $imageData = array();
	private $pathInfo = array();
	private $message = '';
	private $smartyData = array();
	private $retainCopy = false; // retain copy on resize or rename?

	public function __construct($image)
	{
		if (!$this->isValidImage($image))
		{
			throw new Exception ("$image is not a valid image. Failed to create an instance of " . get_class($this));
		}

		$this->image = $image;
	
		$this->imageProperties();
	}

	public function getImageData()
	{

		if (isset($this->pathInfo['filename']))
		{
			$nameWithoutExt = $this->pathInfo['filename'];
		}
		else
		{
			$nameWithoutExt = $this->getNameWithoutExt($this->pathInfo['basename']);
		}

		$return['name'] 			= $this->pathInfo['basename'];
		$return['nameWithoutExt']	= $nameWithoutExt;
		$return['path'] 			= $this->imagePath;
		$return['dirname']			= $this->pathInfo['dirname'];
		$return['width'] 			= @$this->imageData[0];
		$return['height'] 			= @$this->imageData[1];
		$return['ratio']			= $this->getRatio();
		$return['file-type'] 		= $this->pathInfo['extension'];
		$return['ext']				= $this->getExt($this->image);
		$return['uploaded']  		= filectime($this->imagePath);
		$return['size'] 			= number_format(filesize($this->imagePath)/1024, 2 ,".", ""); // size in Kb, 2 decimal places

		return $return;
	}

	public function getRatio()
	{
		// the width : height ratio of the image
		return $this->imageData[1] / $this->imageData[0];
	}

	public static function getExt($image) 
	{
		$bits = explode('.', $image);

		if (count($bits) < 2)
		{
			throw new Exception ("$image has no extension");
		}

		$ext =  end($bits);
		
		return $ext; // NB no preceeding '.'
	}

	public static function getNameWithoutExt($image)
	{
		$image 	= basename($image);

		$name 	= substr($image,0,strrpos($image,"."));

		return $name;
	}

	public static function makeRetainedName($image) // when we want to retain a copy on resizing
	{
		$retainedName = self::getNameWithoutExt($image) . '.old.' . self::getExt($image);

		return $retainedName;
	}

	public function retainCopy($bool)
	{
		$this->retainCopy = $bool;
	}

	public function resizeImage($newWidth, $newName)
	{
		$ratio 		= $this->getRatio();
		$newHeight 	= $newWidth*$ratio;
		$type 		= $this->imageData[2];
		$newPath	= IMAGES_PATH . $newName;
		

		switch ($type)
		{
			case 1:
				 $tempImage = imagecreatefromgif($this->imagePath);
				 break;
			case 2;
				 $tempImage = imagecreatefromjpeg($this->imagePath);
				 break;
			case 3;
				 $tempImage = imagecreatefrompng($this->imagePath);
				 break;
			default:
				 throw new Exception ("Unable to determine image type of {$this->image}");
		}

		$newImage = imagecreatetruecolor($newWidth,$newHeight);
		imagecopyresampled($newImage,$tempImage,0,0,0,0,$newWidth,$newHeight,$this->imageData[0],$this->imageData[1]);

		// if we are saving with the same name, remove/rename the old file to avoid confusion
		if ($newPath == $this->imagePath)
		{
			if ($this->retainCopy == true)
			{
				$retainedPath = IMAGES_PATH . $this->makeRetainedName($this->image);			
				copy ($this->imagePath, $retainedPath);
			}
			unlink ($this->imagePath);
		}

		switch($type)
		{
			 case 1:
			 $return = imagegif($newImage,$newPath); // returns 'true' on success, 'false' on failure
			 break;
			 case 2:
			 $return = imagejpeg($newImage,$newPath);
			 break;
			 case 3:
			 $return = imagepng($newImage,$newPath);
			 break;
	  	}

		//it is vital to destroy the temporary images before we leave!
		imagedestroy ($tempImage);
		imagedestroy ($newImage);

		if (!$return)
		{
			throw new Exception("Failed to resize image {$this->image}");
		}

		// if we have given the image a new name, and we don't want to retain the old image..
		if ($newPath != $this->imagePath && $this->retainCopy == false)
		{
			// .. unlink it
			unlink ($this->imagePath);
		}

		// recast the object so that it contains information about the resized (and possibly renamed) image		
		$this->image = $newName;

		$this->imageProperties();

		return $return;
	}

	public function renameImage($newName)
	{
		$newPath = IMAGES_PATH . $newName;

		if ($this->retainCopy == true)
		{
			if (!copy($this->imagePath, $newPath))
			{
				throw new Exception ("Cannot copy {$this->imagePath}");
			}
		}
		else
		{
			if (!rename($this->imagePath, $newPath))
			{
				throw new Exception ("Cannot rename {$this->imagePath}");
			}
		}

		$this->image = $newName; // NB the object now contains information about the renamed image, not the old one

		$this->imageProperties();
	}

	public function makeHTMLTags($title, $link, $caption, $align, $border, $urltype)
	{
		$width = $this->imageData[0];
		$height = $this->imageData[1];

		$alt = self::getNameWithoutExt($this->image);

		$divclass = $this->getImageContainerClass($align, $border);

		if ($urltype == "relative")
		{
			$imgurl = 'images/' . $this->image;
		}
		else
		{
			$imgurl = THIS_URL . '/images/' . $this->image;
		}
		
		if (!empty($link) && substr($link,0,7) != "http://")
		{
			$link = "http://" . $link;
		}
		
		$html = new DOMDocument('1.0', 'UTF-8');

		//container div
		$div = $html->createElement('div');
		$div->setAttribute('class', $divclass);
		$div->setAttribute('style', 'width:' . $width . 'px;');

		//paragraph
		$p = $html->createElement('p');
		$p->setAttribute('class', 'lb_no_margins');

		//image		
		$img = $html->createElement('img');
		$img->setAttribute('src', $imgurl);
		$img->setAttribute('width', $width . 'px');
		$img->setAttribute('height', $height . 'px');
		$img->setAttribute('alt', $alt);
		if (!empty($title))
		{
			$img->setAttribute('title', $title);
		}

		// chain the elements, starting with the innermost
		if (empty($link))
		{
			$p->appendChild($img);
		}
		else // create <a> tags around the image if we have a link
		{
			$a = $html->createElement('a');
			$a->setAttribute('href', $link);

			$a->appendChild($img);

			$p->appendChild($a);
		}

		$div->appendChild($p);

		if (!empty($caption)) // add caption if we have one
		{
			$p2 = $html->createElement('p');
			$p2->setAttribute('class', 'lb_no_margins');
			$captionText = $html->createTextNode($caption);
			$p2->appendChild($captionText);

			$div->appendChild($p2);
		}

		$html->appendChild($div);

		$return = htmlentities($html->saveHTML(), ENT_QUOTES, 'UTF-8');

		return $return;
	}

	public function makeLightbox($params)
	{
		$alt = self::getNameWithoutExt($this->image);

		$divclass = $this->getImageContainerClass($params);

		$hidden = (strpos($divclass, '_hidden')!=false);

		$lightbox_url = "images/{$this->image}";

		$thumbnailDimensions = $this->lightboxThumbnailDimensions($params);

		$width = $thumbnailDimensions['width'];
		$height = $thumbnailDimensions['height'];
		$timthumb = $thumbnailDimensions['timthumb'];

		$rel = (!empty($_POST['lightbox_slideshow'])) ? "lightbox[{$_POST['lightbox_slideshow']}]" : 'lightbox';

		$html = new DOMDocument('1.0', 'UTF-8');

		$div = $html->createElement('div');

		if ($divclass)
		{
			$div->setAttribute('class', $divclass);
		}

		if (!$hidden) // no width attribute in a hidden div...
		{
			$div->setAttribute('style', 'width: ' . $width . 'px;');
		}

		// create link to the image to display in the Lightbox
		$lightboxLink = $html->createElement('a');
		$lightboxLink->setAttribute('href', $lightbox_url);
		$lightboxLink->setAttribute('rel', $rel);

		if (!empty($params['lightbox_caption']))
		{
			$lightboxLink->setAttribute('title', $params['lightbox_caption']);
		}

		if (!$hidden) // no thumbnail in a hidden div
		{
			$thumb = $html->createElement('img');
			$thumb->setAttribute('src', $timthumb);
			$thumb->setAttribute('width', $width . 'px');
			$thumb->setAttribute('height', $height . 'px');
			$thumb->setAttribute('alt', $alt);
			$thumb->setAttribute('title', 'Click for larger image');

			$lightboxLink->appendChild($thumb);
		}

		$div->appendChild($lightboxLink);

		if (!$hidden) // no caption in a hidden div
		{
			if (!empty($params['lightbox_webpage_caption']))
			{
				$para = $html->createElement('p');

				$para->setAttribute('class', 'lb_no_margins');
				$captionText = $html->createTextNode($params['lightbox_webpage_caption']);
				$para->appendChild($captionText);
		
				$div->appendChild($para);
			}
		}

		$html->appendChild($div);

		$return = $html->saveHTML();

		return $return;
		
	}

	public static function isValidImage($image)
	{
		// checks whether the image exists in the 'images' directory and whether it has a valid extension
		return (is_file(IMAGES_PATH . $image)
				&& preg_match("/(\.jpg|\.gif|\.png|\.jpeg|\.JPG|\.GIF|\.PNG|\.JPEG)$/", $image));
	}

	private function getImageContainerClass($params)
	{
		switch ($params['lightbox_webpage_align'])
		{
			case 0:
			if (!empty($params['lightbox_webpage_class']))
			{
				$divclass = $params['lightbox_webpage_class'];
			}
			else $divclass = false;
			break;
			case 1:
			$divclass = "lb_image_left";
			break;
			case 2:
			$divclass = "lb_image_center";
			break;
			case 3:
			$divclass = "lb_image_right";
			break;
			case 4:
			$divclass = "lb_image_hidden";
			break;
			default:
			$divclass = 'lb_image_left';
		}

		if ($params['lightbox_webpage_border'] == "1" && $divclass)
		{
		   	$divclass .= " lb_with_border";
		}

		return $divclass;
	}

	private function imageProperties()
	{
		$this->imagePath = IMAGES_PATH . $this->image;

		$this->imageData = @(array)getimagesize($this->imagePath);

		$this->pathInfo = pathinfo($this->imagePath);
	}

	public function lightboxThumbnailDimensions($params)
	{
		// we can pass either a array or a single value to the function
		$dimension = (isset($params['lightbox_size']))? $params['lightbox_size'] : $params;

		$ratio = $this->imageData[1]/$this->imageData[0];
		$ratio2 = $this->imageData[0]/$this->imageData[1];

		switch ($params['lightbox_axis'])
		{
			case 'width':
			$width = $dimension;
			$height = intval($ratio*$width);
			$timthumb = "podhawk/timthumb/timthumb.php?src=images/{$this->image}&w=$width&q=100";
			break;
			case 'height':
			$height = $dimension;
			$width = intval($height/$ratio);
			$timthumb = "podhawk/timthumb/timthumb.php?src=images/{$this->image}&h=$height&q=100";
			break;
			case 'square':
			$height = $dimension;
			$width = $dimension;
			$timthumb = "podhawk/timthumb/timthumb.php?src=images/{$this->image}&w=$width&h=$height&zc=1";
			break;
			default:
			$width = $dimension;
			$height = intval($ratio1*$width);
			$timthumb = "podhawk/timthumb/timthumb.php?src=images/{$this->image}&w=$width&q=100";

		}

		return array('width' => $width, 'height' => $height, 'timthumb' => $timthumb);
	}
}	
?>
