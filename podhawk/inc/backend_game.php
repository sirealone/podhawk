<?php

if (!isset($_POST['target']))  {
	$target = (int)rand(0,100);
	$tries = 0;
	$guess = "";
	$test = true;
	}

else {
	$guess = $_POST['myguess'];
	$tries =$_POST['tries']+1;
	$target = $_POST['target'];
	$test = ((ereg("^[0-9]{1,3}$",$guess)) && ($guess >=0) && ($guess <=100));
	}

$smarty->assign(array('target' => $target,
			'tries' =>$tries,
			'guess' => $guess,
			'test' => $test));

?>

