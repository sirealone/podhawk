<?php

$actiontype = array('backend');
include 'authority.php';

$return['html'] 	= $trans_autosave['not_ok'];
$return['success'] 	= FALSE;

if ($authenticated && !empty($_POST))
{
	try
	{
		$makeHTML = new HT_MakeHTML($_POST['editor']);
		$message_html = $makeHTML->make(urldecode($_POST['content']));

		$preparedStatementArray = array(':title' 			=> entity_encode(urldecode($_POST['title'])),
										':summary' 			=> entity_encode(urldecode($_POST['summary'])),
										':message_input' 	=> entity_encode(urldecode($_POST['content'])),
										':message_html' 	=> $message_html,
										':id' 				=> $_POST['id']
										);
	 
		$dosql = "UPDATE " . DB_PREFIX . "lb_postings SET title = :title, message_input = :message_input,
					message_html = :message_html, summary = :summary WHERE id = :id";	

		$GLOBALS['lbdata']->prepareStatement($dosql);

		$result = $GLOBALS['lbdata']->executePreparedStatement($preparedStatementArray);
	
		$return['html'] = $trans_autosave['ok'];
		$return['success'] = TRUE;
	}
	catch (Exception $e)
	{
		$log->write($e->getMessage());
	}
	
}

$return = json_encode($return);
echo $return;

?>
