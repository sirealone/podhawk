<?php

$actiontype = array('backend');
include 'authority.php';


$authors = $reg->getAuthorNicknamesIDIndex();

$cats = $reg->getCategoryNames();


$months = array('01' => $trans_months['jan'],
		'02' => $trans_months['feb'],
		'03' => $trans_months['mar'],
		'04' => $trans_months['apr'],
		'05' => $trans_months['may'],
		'06' => $trans_months['jun'],
		'07' => $trans_months['jul'],
		'08' => $trans_months['aug'],
		'09' => $trans_months['sep'],
		'10' => $trans_months['oct'],
		'11' => $trans_months['nov'],
		'12' => $trans_months['dec']);

$y = date('Y');

$years = array($y-6 => $y-6,
		$y-5 => $y-5,
		$y-4 => $y-4,
		$y-3 => $y-3,
		$y-2 => $y-2,
		$y-1 => $y-1,
		$y  =>  $y,
		$y+1 => $y+1);

$smarty->assign(array('months' => $months,
			'this_month' => date('m'),
			'years' => $years,
			'this_year' => $y,
			'authors' => $authors,
			'categories' => $cats,			
			'record2_auth_key' => $sess->createPageAuthenticator('record2')
			));
?>
