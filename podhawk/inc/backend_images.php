<?php

/*var_dump ($_SESSION);
var_dump ($_POST);
var_dump ($_GET);*/

$actiontype = array('backend');
include 'authority.php';

try

{
	$message = "";
	$ipage = 'find';
	$warning = false;
	$retain = false;

	if (extension_loaded('gd') == false || function_exists('gd_info') == false)
	{
		throw new Exception("Sorry - php on your site does not have the 'gd' extension loaded. You need this extension to manage images. Ask your web host or server admin to add the 'gd' extension.");
	}

	// create a list of images either from $_REQUEST parameters or from a cached array

	$smarty->assign(array(	'gif_supported' 	=> (imagetypes() & IMG_GIF),
							'jpg_supported' 	=> (imagetypes() & IMG_JPG),
							'png_supported' 	=> (imagetypes() & IMG_PNG),
							'images_auth_key' 	=> $sess->createPageAuthenticator('images'),
							'sessid' 			=> session_id(),
							'repeat_search'		=> (!empty($_SESSION['imageList'])) // we have the results of an earlier search in session variables
							));

	// do we have an action to perform?
	if (isset($_GET['action']))
	{

		if (!$authenticated)
		{
			throw new Exception('no_auth');
		}

		// for most actions, we need to be able to write to the images folder
		$permissions->make_writable('images');

		if ($_GET['action']=="list")
		{

			$pagination = new IM_Pagination();

			$imageData = $pagination->getRows();

			if (empty($imageData))
			{
				$ipage = 'find';
				$message = 'none_found';
				$smarty->assign('repeat_search', false);
			}
			else
			{
				$smarty->assign('images', $imageData);
				$smarty->assign('paging_string', $pagination->getPaginationString());

				$ipage = 'list';
				$message = 'images_found';
			}
			
		}	
		
		elseif (($_GET['action']=="delete") && (isset($_POST['del_image'])))
		{
			//delete an image
			unlink (IMAGES_PATH . $_POST['del_image']);

			//remove it from the stored result of previous search
			$listManager = new IM_ImageList();
			$listManager->deleteFromList($_POST['del_image']);

			$ipage = 'find';
			$message = 'delete_success';
		}

		elseif (($_GET['action'] == "rename_show") AND (isset($_POST['rename_image'])))
		{
			//form for renaming an image
			$smarty->assign('image',$_POST['rename_image']);
			$smarty->assign('ext', IM_Image::getExt($_POST['rename_image']));
			$ipage = 'new_name';			   	
		}

		elseif ($_GET['action'] == "rename_do"
				&& isset($_POST['newimagename'])
				&& isset($_POST['oldimagename']))
				
		{
		   	// rename an image
			$oldImageName = $_POST['oldimagename'];
			$newImageName = $_POST['newimagename'] . "." . IM_Image::getExt($oldImageName);
			$newImagePath = IMAGES_PATH . $newImageName;

			if(file_exists($newImagePath))
			{
				// ask user to choose a different name
				$smarty->assign('image', $oldImageName);
				$smarty->assign('ext', IM_Image::getExt($oldImageName));				

				$ipage = 'new_name';
				$message = 'image_exists';	
			}
			else
			{
				$imageToRename = new IM_Image($oldImageName);

				if (isset($_POST['retainCopy']) && $_POST['retainCopy'] == '1')
				{
					$retain = true;
					$imageToRename->retainCopy(true);
				}

				$imageToRename->renameImage($newImageName);

				$listManager = new IM_ImageList();

				if ($retain)
				{
					$listManager->add($newImageName);
				}
				else
				{
					$listManager->amendList($oldImageName, $newImageName);
				}

				$pagination = new IM_Pagination();

				$imageData = $pagination->getRows();

				$smarty->assign('images', $imageData);
				$smarty->assign('paging_string', $pagination->getPaginationString());

				$ipage = 'list';
				$message = 'image_renamed';
			}
		}

		elseif (($_GET['action'] == "change_size_show") && (isset($_POST['imageToResize'])))
		{
		   	//form for resizing an image
			$imageToResize = new IM_Image($_POST['imageToResize']);

			$smarty->assign('imagedata', $imageToResize->getImageData());
			
			$ipage = 'new_size';
		 }

		elseif ($_GET['action'] == "resize"
				&& isset($_POST['imageToResize'])
				&& isset($_POST['newWidth']))
		{
		   	//check whether the requested newname is already in use ...
			$newImageName = $_POST['newname'] . '.' . IM_Image::getExt($_POST['imageToResize']);

			$imageToResize = new IM_Image($_POST['imageToResize']);

			//...if it is, show the 'change size' screen again
			if (file_exists(IMAGES_PATH . $newImageName) && $newImageName != $_POST['imageToResize'])
			{
				$smarty->assign('imagedata', $imageToResize->getImageData());
		
				$ipage = 'new_size';
				$message = 'image_exists';
			}
			else
			{
				// do we want to retain a copy with the old image size/name?
				if (isset($_POST['retainCopy']) && $_POST['retainCopy'] == '1')
				{
					$retain = true;
					$imageToResize->retainCopy(true);
				}

				// change the image size
				$success = $imageToResize->resizeImage($_POST['newWidth'], $newImageName);

				$listManager = new IM_ImageList();

				// if we are changing the name of the image, tell the list manager ...
				if ($newImageName != $_POST['imageToResize'])
				{
					if ($retain) // ..to add the new image if we are retaining a copy of the old
					{
						$listManager->add($newImageName);
					}
					else // .. otherwise to replace the old image with the new
					{
						$listManager->amendList($_POST['imageToResize'], $newImageName);
					}
				}
				else // no change of name
				{
					if ($retain) // if we want to retain a copy of the old file...
					{
						// ...give the old file a new name ...
						$retainedName = IM_Image::makeRetainedName($_POST['imageToResize']);
						//... and add it to the list
						$listManager->add($retainedName);
					}
				}
				$pagination = new IM_Pagination();

				$smarty->assign('images', $pagination->getRows());
				$smarty->assign('paging_string', $pagination->getPaginationString());

				$ipage = 'list';
				$message = 'resizedsuccess';
			}
		}
			
		elseif (($_GET['action'] == "make_tag_show") && (isset($_POST['imageToHTML'])))
		{
			//form for tag-making	
			$smarty->assign(array(	'alt' 		=> IM_Image::getNameWithoutExt($_POST['imageToHTML']),
									'imageToHTML' => $_POST['imageToHTML']));
	
			$ipage = 'make_html';
		}

		elseif (($_GET['action'] == "maketag") && (isset($_POST['imageToHTML'])))
		{

			$image = new IM_Image($_POST['imageToHTML']);

			if (isset($_POST['lightbox']) && $_POST['lightbox'] == '1')
			{
				$html = $image->makeLightbox($_POST);
			}
			else
			{
				$html = $image->makeHTMLTags($_POST['title'], $_POST['link'], $_POST['caption'], $_POST['align'], $_POST['border'],	$_POST['urltype']); 
			}

			$smarty->assign('displayTag', $html);
			$smarty->assign('imageToHTML', $_POST['imageToHTML']);						
	
			$ipage = 'display_tag';  	
		}


	 	//if we have got this far, something is wrong!
		else
		{			
			throw new Exception('no_action_error');
		}

		// make the images folder non-writable
		$permissions->make_not_writable('images');	

	} // end if isset ($_GET['action'])
			 
} // close try block

catch (Exception $e)
{
	$permissions->make_not_writable('images');
	$message = $e->getMessage();
	$ipage = 'find';
	$warning = true;
}

$smarty->assign (array('message' => $message, 'ipage' => $ipage, 'warning'=>$warning));

/*if ($ipage == 'find')
{
	unset ($_SESSION['imagesearch1'], $_SESSION['imagesearch2'], $_SESSION['imagesearch3']);
}*/
?>
