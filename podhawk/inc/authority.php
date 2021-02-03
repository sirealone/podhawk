<?php

	$authority = false;

	// if the calling script defines $actiontype, check that the constant ACTION matches $actiontype
	if (!empty($actiontype) && defined('ACTION'))
	{
		if (is_array($actiontype))
		{
			if (in_array(ACTION, $actiontype))
			{
				$authority = TRUE;
			}
		}
		elseif (ACTION == $actiontype)
		{
			$authority=true;
		}
	}
	else
	// if $actiontype is not set, check that the constant ACTION is defined
	{
		if (defined('ACTION'))
		{
			$authority=true;
		}
	}

	if ($authority == false)
	{
		header ("HTTP/1.0 404 Not Found");
		die ('Sorry - this page is not available.');
	}
?>
