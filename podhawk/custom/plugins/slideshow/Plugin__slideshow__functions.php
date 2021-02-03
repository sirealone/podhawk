<?php

class Plugin__slideshow__functions
{
	static function makeSlideshowXML()
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = true;

		$slideshow = $dom->appendChild($dom->createElement('slideshow')); // 'slideshow' root element

		$title = $slideshow->appendChild($dom->createElement('title'));
		$title->appendChild($dom->createTextNode($_POST['name']));

		$link = $slideshow->appendChild($dom->createElement('link'));

		if ($_POST['thumbs'] == '0')
		{
			$link->setAttribute('type', 'text');
			$link->appendChild($dom->createTextNode($_POST['textlink']));
		}
		elseif ($_POST['thumbs'] == '1')
		{
			$link->setAttribute('type', 'singlethumb');
			$link->setAttribute('thumbsize', $_POST['thumbsize']);
			$link->setAttribute('axis', $_POST['axis']);
		}
		elseif ($_POST['thumbs'] == '2')
		{
			$link->setAttribute('type', 'allthumbs');
			$link->setAttribute('thumbsize', $_POST['thumbsize']);
			$link->setAttribute('axis', $_POST['axis']);
		}

		foreach ($_POST['image'] as $key=>$name)
		{
			$image = $slideshow->appendChild($dom->createElement('image'));
			$imagename = $image->appendChild($dom->createElement('name'));
			$imagename->appendChild($dom->createTextNode($name));
			$captiontext = $image->appendChild($dom->createElement('caption'));
			$captiontext->appendChild($dom->createTextNode($_POST['caption'][$key]));
		}

		return $dom->saveXML();
	}

	static function arrayFromXML($xml)
	{
		$doc = new DOMDocument();

		$doc->load($xml);

		$slideshow['name'] = $doc->getElementsByTagName('title')->item(0)->nodeValue;
		
		$link = $doc->getElementsByTagName('link')->item(0);

		$linkType = $link->attributes->getNamedItem('type')->nodeValue;

		switch ($linkType)
		{
			case 'text' :
				$slideshow['thumbs'] = '0';
				$slideshow['textlink'] = $link->nodeValue;
				break;
			case 'singlethumb' :
				$slideshow['thumbs'] = '1';
				$slideshow['thumbsize'] = $link->attributes->getNamedItem('thumbsize')->nodeValue;
				$slideshow['axis'] = $link->attributes->getNamedItem('axis')->nodeValue;
				break;
			case 'allthumbs' :
				$slideshow['thumbs'] = '2';
				$slideshow['thumbsize'] = $link->attributes->getNamedItem('thumbsize')->nodeValue;
				$slideshow['axis'] = $link->attributes->getNamedItem('axis')->nodeValue;
				break;
		}

		$images = $doc->getElementsByTagName('image');		
		foreach($images as $image)
		{
			$im['name'] = $image->getElementsByTagName('name')->item(0)->nodeValue;
			$im['caption'] = $image->getElementsByTagName('caption')->item(0)->nodeValue;
			$slideshow['images'][] = $im;
		}
		
		return $slideshow;
	}

	static function getExternalImage($imageSource, $returnName=false)
	{
		$imageName = 'slideshow_' . basename($imageSource);
		$imageDest = IMAGES_PATH . $imageName;

		if (!file_exists($imageDest)) // download the external image to images folder if it isn't there already
		{
			$permissions = new Permissions(array('images'));

			$permissions->make_writable('images');	
			$ch = curl_init($imageSource);
			$fp = fopen($imageDest, "wb");

			$options = array(	CURLOPT_FILE => $fp,
								CURLOPT_HEADER => 0,
								CURLOPT_FOLLOWLOCATION => 1,
								CURLOPT_TIMEOUT => 60); // 1 minute timeout (should be enough) 

			curl_setopt_array($ch, $options);
			curl_exec($ch);
			curl_close($ch);

			fclose($fp);

			$permissions->make_not_writable('images');
		}

		$return = ($returnName) ? $imageName : $imageDest; // do we want just the name of the image, or its location relative to server root? 

		return $return;
	}

	static function makeSlideshowHTML($xml)
	{
		$data = self::arrayFromXML($xml);
		
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->formatOutput = true;

		// containing div with class=clearfloats to prevent complications if it contains floating elements
		$container = $dom->appendChild($dom->createElement('div'));

		if (ACTION == 'backend')
		{
			$container->setAttribute('style', 'text-align: center; margin: 5px;');
		}
		else
		{ 
			$container->setAttribute('class', 'clearfloats');
		}

		$imageIndex = 0;
		foreach ($data['images'] as $key => $image)
		{
			$a = $container->appendChild($dom->createElement('a'));

			if (substr($image['name'], 0, 7) == 'http://' || substr($image['name'], 0, 8) == 'https://') // have we an external image ...
			{
				$imageName = self::getExternalImage ($image['name'], true);				
			}
			else // .. or an image in the images folder?
			{
				$imageName = $image['name'];
			}
			$imageHREF = THIS_URL . '/images/' . $imageName;

			$a->setAttribute('href', $imageHREF);

			if (!empty($image['caption'])) // have we a caption to display?
			{
				$a->setAttribute ('title', $image['caption']);
			}
			
			$a->setAttribute('rel', 'lightbox[' . $data['name'] . ']'); // rel=lightbox attribute

			if ($data['thumbs'] == '0' || ACTION == 'backend') // text link
			{
				if (ACTION == 'backend') $data['textlink'] = 'Slideshow';
				$link = ($imageIndex == 0) ? $data['textlink'] : ''; // text link for first image only
				$a->appendChild($dom->createTextNode($link));
			}
			else // image link
			{
				if ($data['thumbs'] == 1 && $imageIndex > 0) // no thumbnail
				{
					$a->appendChild($dom->createtextNode(''));
				}
				else // create a thumbnail image
				{
					$thumbnail = $a->appendChild($dom->createElement('img'));

					$data['lightbox_size'] = $data['thumbsize'];
					$data['lightbox_axis'] = $data['axis'];
					
					$imageObj = new IM_Image($imageName);
					
					$thumb = $imageObj->lightboxThumbnailDimensions($data);
					
					$thumbnail->setAttribute('src', $thumb['timthumb']);
					
					$thumbnail->setAttribute('height', $thumb['height']);
					
					$thumbnail->setAttribute('width', $thumb['width']);

					$thumbnail->setAttribute('alt', 'Thumbnail');

					$thumbnail->setAttribute('title', 'Click for slideshow');

					$thumbnail->setAttribute('class', 'slideshowThumbnail'); // to float images to left
				}
			}
			$imageIndex++; // increment the counter
		}

		$html = $dom->saveHTML();

		return $html;
	}

}
?>
