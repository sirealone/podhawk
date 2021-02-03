<?php

	header("HTTP/1.0 404 Not Found");

		echo 	"<html>
				<head>
				<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
				<title>" . SITENAME . " -  Page Not Found</title>
				</head>
				<body>
				<h3>Error 404 - Not Found</h3>
				<p>Sorry - I cannot find the page or file you asked for.</p>
				<p><a href=\"" . THIS_URL ."\">Return to " . SITENAME . ".</a></p>
				</body>
				</html>";

	exit();
?>
