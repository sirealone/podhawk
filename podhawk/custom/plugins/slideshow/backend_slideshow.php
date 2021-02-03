<?php

$actiontype = array('backend');
include INCLUDE_FILES . '/authority.php';

$warning = false;
$message = '';

$saveTo = 'upload';
$slideshowFileName = '';

// the default slideshow data which we will send to Smarty
$slideshow = array(	'name' => '',
					'thumbs' => '1',
					'textlink' => 'Click to start slideshow',
					'thumbsize' => '80',
					'axis' => 'square',
					'images' => array()
				);

if (isset($_GET['action']))
{
	try
	{
		/*if (!$authenticated)
		{
			throw new Exception ('no_auth');
		}*/
		if ($_GET['action'] == 'makeslideshow')
		{
			try
			{
				if (empty($_POST['name']))
				{
					throw new PodhawkException ('Slideshow must have a title');
				}
				if (empty($_POST['image']))
				{
					throw new PodhawkException ('Slideshow must contain at least one image');
				}
	 
				$xml = Plugin__slideshow__functions::makeSlideshowXML();

				$saveDir = $_POST['saveTo'];

				$tempFileName = (!empty($_POST['saveFileName'])) ? $_POST['saveFileName'] : 'slideshow_' . str_replace(' ', '_', $_POST['name']) . '.xml';

				$permissions->make_writable($saveDir);

					$fp = @fopen (PATH_TO_ROOT . '/' . $saveDir .'/' . $tempFileName, 'w');

					if (!$fp)
					{
						throw new Exception ('I cannot write slideshow file ' . $tempFileName);
					}

					fwrite($fp, $xml);
					fclose($fp);
	
					$message = "I have created/amended the slideshow file and placed it in your '$saveDir' folder.";
					$events->write("Created/amended slideshow $tempFileName in $saveDir folder");

				$permissions->make_not_writable($saveDir);				
			}
			catch (PodhawkException $e)	
			{
				$warning = true;
				$message = $e->getMessage();
				$slideshow = $_POST;
			}
		}
		elseif ($_GET['action'] == 'getexternalimage')
		{
			$imageSource = $_GET['url'];

			$imageDest = Plugin__slideshow__functions::getExternalImage($imageSource);

			$sender = new PO_File_Sender($imageDest);

			$sender->send();			
		}
		elseif ($_GET['action'] == 'retrieve_from_upload')
		{
			$slideshow = Plugin__slideshow__functions::arrayFromXML(UPLOAD_PATH . $_GET['filename']);

			if (!empty($_GET['filename']))
			{
				$slideshowFileName = $_GET['filename'];
			}

			$message = "Slideshow file " . $_GET['filename'] . " from upload folder.";	
		}
		elseif ($_GET['action'] == 'delete_from_upload')
		{
			@unlink(UPLOAD_PATH . $_GET['filename']);
			$message = 'I have removed ' . $_GET['filename'] . ' from the upload folder';
		}
		elseif ($_GET['action'] = 'retrieve_from_posting')
		{
			$slideshow = Plugin__slideshow__functions::arrayFromXML (AUDIOPATH . $_GET['filename']);

			$saveTo = 'audio';
			
			if (!empty($_GET['filename']))
			{
				$slideshowFileName = $_GET['filename'];
			}

			$message = "Slideshow file " . $_GET['filename'] . " from audio folder.";
		}
	}
	catch (Exception $e)
	{
echo ($e);
	}
}

// slideshow files in upload folder
$upload = get_dir_contents(PATH_TO_ROOT . '/upload');
$upload_folder = array();

foreach($upload as $file)
{
	if (substr($file, -4) != '.xml') continue;
	$doc= new DOMDocument();
	$doc->load(UPLOAD_PATH . $file);
	$xmlroot = $doc->documentElement->nodeName;
	if ($xmlroot == 'slideshow')
	{
		$upload_folder[] = $file;
	}
}

$smarty->assign('upload_folder', $upload_folder);

// slideshow files in postings
$slideshow_posts = array();
$dosql = "SELECT id, title, audio_file FROM " . DB_PREFIX . "lb_postings WHERE audio_type = '24';";
$result = $GLOBALS['lbdata']->GetArray($dosql);
if (!empty($result))
{
	foreach ($result as $row)
	{
		$doc = new DOMDocument();
		$doc->load(AUDIOPATH . $row['audio_file']);
		$names = $doc->getElementsByTagName('title');
		if (!empty($names))
		{
			$row['slideshow_name'] = $names->item(0)->nodeValue;
		} 
		$slideshow_posts[] = $row;
	}
}

$smarty->assign('slideshow_posts', $slideshow_posts);


$smarty->assign ('slideshow', $slideshow);
$smarty->assign ('images_path', IMAGES_PATH);

$smarty->assign('slideshow_auth_key', $sess->createpageAuthenticator('slideshow'));
$smarty->assign('record2_auth_key', $sess->createPageAuthenticator('record2'));

$smarty->assign(array(	'saveTo' => $saveTo,
						'slideshowFileName' => $slideshowFileName,
						'message' => $message,
						'warning' => $warning));

function makeSlideshowXML()
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

function arrayFromXML($xml)
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
				$slideshow['axis'] = $link->attributes->getNamedValue('axis')->nodeValue;
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
	


	

?>
