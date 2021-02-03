<?php

$actiontype = array('backend');
include 'authority.php';

// autocomplete adds spaces to the ends of tag names - get rid of them.
$_POST = array_map('trim', $_POST);

$message = '';
$deleted = false;

try // outer try block
{
	if (!$currentUser->isAdmin())
	{		 	
		throw new Exception('adminonly'); 
	}

	if (isset($_GET['do']))
	{
		try // inner try block
		{
			if(!$authenticated)
			{
				throw new Exception('no_auth');
			}	
		
			if ($_GET['do'] == 'savecats')
			{
				// search $_POST for $_POST['del...'] - this is an instruction to delete a category
				foreach ($_POST as $name => $value)
				{
					if (substr($name, 0, 3) == 'del')
					{
						$catToDelete = substr($name, 3);

						if (!ctype_digit($catToDelete)) // check that we have a numerical value for the category id
						{
							throw new Exception('failcatsupdate');
						}

						$dosql = 'DELETE FROM ' . DB_PREFIX . 'lb_categories WHERE id = :id';
						$GLOBALS['lbdata']->prepareStatement($dosql);
						$GLOBALS['lbdata']->executePreparedStatement(array(':id' => $catToDelete));

						// we need also to remove the category from individual postings
						for ($i = 1; $i <= 4; $i++)
						{
							$dosql = "UPDATE " . DB_PREFIX . "lb_postings
									SET category{$i}_id = 0 WHERE category{$i}_id = :id";
							$GLOBALS['lbdata']->prepareStatement($dosql);
							$GLOBALS['lbdata']->executePreparedStatement(array(':id' => $catToDelete));
						}
							
						$deleted = true;
					}
				}

				if (!$deleted) // if we haven't deleted anything
				{
					//set up our prepared statement
					$dosql = "UPDATE " . DB_PREFIX . "lb_categories SET name = :name,
							description = :description, hide = :hide WHERE id = :id";
					$GLOBALS['lbdata'] -> prepareStatement($dosql);
		
					// search for each instance of $_POST['cat...']...
					foreach ($_POST as $name => $value)
					{
						if (substr($name, 0, 3) == 'cat')
						{
							$catToUpdate = substr($name, 3);

							if (!ctype_digit($catToUpdate)) // check that we have a numerical value for the category id
							{
								throw new Exception('failcatsupdate');
							}

							// ...and run the prepared statement each time
							$preparedStatementArray = array(':name' 		=> entity_encode($value),
															':description' 	=> entity_encode($_POST['desc' . $catToUpdate]),
															':id' 			=> $catToUpdate,
															':hide'			=> strval(!empty($_POST['hide' . $catToUpdate]))
															);

							$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);
						}
					}

					// have we entered the name of a new category
					if (!empty($_POST['newcat']))
					{
						$dosql = "INSERT INTO " . DB_PREFIX . "lb_categories (name, description, hide) VALUES (:name, :description, :hide)";
						$GLOBALS['lbdata']->prepareStatement($dosql);

						$preparedStatementArray = array(':name' 		=> entity_encode($_POST['newcat']),
														':description' 	=> entity_encode($_POST['newdesc']),
														':hide'			=> strval(!empty($_POST['newhide']))
														);

						$GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);
					}

				} // close 'if we haven't deleted anything'
		
				$message = 'successcatsupdate';

			} // close $_GET['do'] == savecats
		

			elseif ($_GET['do'] =='edittag')
			{
				if (!isset($_POST['tagaction']))
				{
					throw new Exception('no_action');
				}
		
				if ($_POST['tagaction'] == "deletetag")
				{
					if (!isset($_POST['tagnames']))
					{
						throw new Exception('no_tag');
					}
					
					$message = amendtags("","delete",$_POST['tagnames'],"");
				}

				if (($_POST['tagaction'] == "amendtag"))
				{
					if (!isset($_POST['tagnames']) || !isset($_POST['newtagname']))
					{
						throw new Exception('no_tag');						                               
					}
					
					$message = amendtags("","replace",$_POST['tagnames'],$_POST['newtagname']);          
				}

				if ($_POST['tagaction'] == "createcat")
				{
					if (!isset($_POST['catname']) || !isset($_POST['tagnames']))
					{
						throw new Exception('no_tag_cat');						                         
					}

					$message = catfromtag($_POST['catname'],$_POST['tagnames']);             
				}

			} // close $_GET['do'] == edittag

			elseif ($_GET['do'] == 'tagfromcat')
			{
				if (!isset($_POST['catname']) || !isset($_POST['newtagname']))
				{
					throw new Exception('no_tag_cat');
				}
				$criteria = " WHERE (category1_id = {$_POST['catname']}
							OR category2_id = {$_POST['catname']}
							OR category3_id = {$_POST['catname']}
							OR category4_id = {$_POST['catname']}) ";

				$message = amendtags($criteria,"add","",$_POST['newtagname']);                                       

			} // close $_GET['do'] == tagfromcat

			$clear->setFlag(array('SmartyCache', 'PHCache', 'Registry')); // set flag to delete caches and reload the Registry

		} // close inner try block

		catch (Exception $e)
		{
			$message = $e->getMessage();
		}

	} // close isset $_GET['do']

	$categories = $reg->refreshCategories();

	$smarty->assign('categories', $categories);

	$smarty->assign('cats_auth_key', $sess->createPageAuthenticator('cats'));

} //close outer try block
catch (Exception $e)
{
	$message = $e->getMessage;
} 

$smarty->assign('message', $message);				
	
##################################################################################
// two functions to make this script work!

function amendtags($criteria,$action,$oldtag,$newtag)
{
	// if we forget to specify a new tag
	if (($action == 'replace' || $action == 'add') && empty($newtag))
	{
		throw new Exception ('no_tag');
	}

	//we want to add, delete or replace $oldtag - so lets get tag info for all posts
  	$dosql = "SELECT id, tags FROM " . DB_PREFIX . "lb_postings" . $criteria;
  	$tagarray = $GLOBALS['lbdata'] -> GetArray($dosql);

  	$i = 0;
	//go through the posts one by one
	foreach ($tagarray as $tagline)
	{
        $tagline['tags'] = my_html_entity_decode($tagline['tags']);        
		$newtagline = "";
		$tags = explode(" ", my_html_entity_decode($tagarray[$i]['tags']));

		//if 'add' and $newtag is not already present, add it
		if ($action == "add" && !in_array($newtag, $tags))
		{
			$newtagline = $tagline['tags'] . " " . $newtag;
		}
		else
		{
			foreach ($tags as $t)
			{
				//if 'delete' and we find $oldtag, then delete it
				if ($action == "delete" && $t == $oldtag)
				{
					continue;
				}

				//if 'replace' and we find $oldtag, replace it with $newtag (unless $newtag is an empty string)
				if ($action == "replace" && $t == $oldtag)
				{
					$t = $newtag;
				}

				$newtagline .= " " . $t;
			}
		}
		//replace the old tag line in the array with the new tag line
		$tagarray[$i]['tags'] = trim($newtagline);

		//go back for the next line
		$i +=1;
	}

	//prepare DB statement
	$dosql = 'UPDATE ' . DB_PREFIX . 'lb_postings SET tags = :tags WHERE id = :id';
	$GLOBALS['lbdata']->prepareStatement($dosql);

	// ..and execute it for each tag line
	foreach ($tagarray as $tagline)
	{		
		$result = $GLOBALS['lbdata']->executePreparedStatement(array(	':tags' => entity_encode($tagline['tags']),
																		':id' 	=> $tagline['id']));		
		if (!$result)
		{
			throw new Exception('failtagsupdate');
		}
	}

	if ($action == "add") $message = 'tag_added';
 	if ($action ==  "delete") $message = 'tag_deleted';
 	if ($action == "replace") $message = 'replace_tag';

	return $message;
}


 function catfromtag($category,$newtag)
{
	//the message if all goes OK
	$message = 'tag_to_cat';

	//we want to add every post with tag $newtag to the category $category
	//get information about tags and categories for all posts
	$dosql = "SELECT id, tags, category1_id, category2_id, category3_id, category4_id FROM " . DB_PREFIX ."lb_postings";
	$tagarray = $GLOBALS['lbdata'] ->GetArray($dosql);

	// prepare the DB query for rewriting the posting categories
	$dosql = 	"UPDATE " . DB_PREFIX . "lb_postings SET category1_id = :cat1, category2_id = :cat2,
				category3_id = :cat3, category4_id = :cat4
				WHERE id = :id";
	$GLOBALS['lbdata']->prepareStatement($dosql);

	//examine the information about each post
	foreach ($tagarray as $tagline)
	{
		// create an array of posting tags
		$tags = explode(" ", $tagline['tags']);

		// create an array of existing categories
		$catarray = array_slice($tagline,2,4);

		// if $newtag is not one of the posting tags, or posting is already in category $category
		if(!in_array($newtag, $tags) || in_array($category, $catarray))
		{
			continue;
		}			

		//find the first empty category slot - category1_id, category2_id etc
		for ($j = 1, $where = false; ($where == false) && ($j <5); $j++)
		{
			$catlocation = "category".$j."_id";
			if ($catarray[$catlocation] == 0)
			{
				$catarray[$catlocation] = $category;
				$where = true;
			}
		}

		//if there are no empty category slots, then we can't add this post to $category
		if ($where == false)
		{
			continue;
		}
		
		//rewrite the posting categories
		$preparedStatementArray = array(':cat1' => $catarray['category1_id'],
										':cat2' => $catarray['category2_id'],
										':cat3' => $catarray['category3_id'],
										':cat4' => $catarray['category4_id'],
										':id'	=> $tagline['id']
										);

		$result = $GLOBALS['lbdata'] -> executePreparedStatement($preparedStatementArray);

		if (!$result)
		{
			throw new Exception('fail_tag_to_cat');
		}
     	
	} // end foreach loop

	return $message;

}

?>
