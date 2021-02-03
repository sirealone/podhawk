<?php

$actiontype = array('backend');
include 'authority.php';

//adding "post" data to "get" data
if (isset($_POST['show']))
{
    $_GET['show'] = $_POST['show'];
} 
elseif (!isset($_GET['show']))
{
	$_GET['show'] = "tenpost";
}

$smarty->assign('show', $_GET['show']);

//sorting-variables
if (isset($_GET['sort']))
{ 
    $sortby = substr($_GET['sort'],1);
    $sortdir = substr($_GET['sort'],0,1);
    if ($sortdir == "0")
	{
		$order = "ASC";
	}
	else
	{
		$order = "DESC";
	}
}
else
{ 
    $_GET['sort'] = "1posted"; 
    $sortby = "posted"; 
    $order= "DESC"; 
    $sortdir = 1;
}

$smarty->assign('sortby', $sortby);

//default values for new url-requests
$dirpos = "0"; $dirtit = "0"; $dirweb = "1"; $dirfla = "1"; $dirpod = "1"; $dirall = "1";

//reverse the sorting order for the current sorting criterion
$n = 'dir'.substr($sortby,0,3);
if ($sortdir == "1")
{
	$$n = "0";
}
else
{
	$$n = "1";
}

$heading_array =array(	'posted'	=>	$dirpos.'posted',
						'title'		=>	$dirtit.'title',
						'countweb'	=>	$dirweb.'countweb',
						'countfla'	=>	$dirfla.'countfla',
						'countpod'	=>	$dirpod.'countpod',
						'countall'	=>	$dirall.'countall');

$smarty->assign('headings', $heading_array);


//some calculations for the query
if (isset($_GET['show']))
{
    switch ($_GET['show'])
	{
        case "tenpost" : 
            $showlimit = 10; 
            $showdate = "1900-01-01 00:00:00"; 
            break;
        case "oneweek" : 
            $showlimit = 99999; 
            $showperiod = 604800;
            $showdate = date("Y-m-d H:i:s", time()-$showperiod);
            break;
        case "onemonth" : 
            $showlimit = 99999;
            $showperiod = 2678400; 
            $showdate = date("Y-m-d H:i:s", time()-$showperiod); 
            break;
        case "threemonth" : 
            $showlimit = 99999; 
            $showperiod = 7776000; 
            $showdate = date("Y-m-d H:i:s", time()-$showperiod); 
            break;
        case "oneyear" : 
            $showlimit = 99999; 
            $showperiod = 31536000;
            $showdate = date("Y-m-d H:i:s", time()-$showperiod); 
            break;
        case "allpost" : 
            $showlimit = 99999; 
            $showdate = "1900-01-01 00:00:00"; 
            break;
    }
}
else
{
    $showlimit = 10; 
    $showdate = "1900-01-01 00:00:00";
}

//getting all sql-data needed for the table
$dosql = "SELECT id, posted, title, countweb, countfla, countpod, countall 
          FROM ".DB_PREFIX."lb_postings 
          WHERE posted > '$showdate' ORDER BY $sortby $order LIMIT $showlimit";

$showtable = $GLOBALS['lbdata']->GetArray($dosql);

$count = count($showtable);

//calculating data for figure
$figwidth = 690;
$figheight= 156;

//getting highest counting value
$maxcount = 1;

foreach ($showtable as $value)
{
    if ($value['countall'] > $maxcount)
	{
		$maxcount  = $value['countall'];
	}
}


//get the position values
$factorx = $figwidth/($count+2);
$factory = $figheight / $maxcount;

$data_array = array('x','hweb','hfla','hpod','h','yfla','ypod','cdate','ctitle');

$i = 0;

foreach ($showtable as $row)
{
	$x = $factorx * ($count - $i);   
    $hweb = round($row['countweb'] * $factory);
   	$hfla = round($row['countfla'] * $factory);
    $hpod = round($row['countpod'] * $factory);
    $h = $hweb + $hfla + $hpod + 2;
    $yfla = $hweb + 1;
    $ypod = $hweb + $hfla + 2;
    $cdate = date('d M Y',strtotime($row['posted']));
    $ctitle = $row['title'];

	foreach ($data_array as $item)
	{
		$showtable[$i][$item] = $$item;
	}
	$i++;
}

$smarty->assign('postings', $showtable);
$smarty->assign('record2_auth_key', $sess->createPageAuthenticator('record2'));
 
?>
