<?php

$actiontype = array('backend');
include 'authority.php';

include "lang/" . $reg->findSetting('language') . ".php";

if (!$authenticated)
{

	$return = $trans_testFTP['no_auth'];

}

else

{

		if (!extension_loaded('ftp'))
		{

		$return = $trans_testFTP['no_ftp'];

		}
		else
		{

			$conn = @ftp_connect($_POST['server']);

			if (!$conn)
			{

				$return = $trans_testFTP['no_connection'];
		
			}
			else
			{

				$login = @ftp_login($conn, $_POST['user'], $_POST['password']);

				if(!$login)
				{

					$return = $trans_testFTP['no_login'];
		
				}
				else
				{

 					$path = @ftp_chdir($conn, $_POST['path']);

					if (!$path)
					{

						$return = $trans_testFTP['no_upload_folder'];

					}
					elseif (substr($_POST['path'], -6) != 'upload')
					{

						$return = $trans_testFTP['no_upload_folder'];
		
					}
					else
					{

						$return = $trans_testFTP['success']; 
 

        			}
				}
			}
		}
	}
	
	if ($conn) $close = ftp_close($conn);
	
	echo $return;

?>
