<?php

## The head section of the webpage

$head = <<<EOF

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <meta http-equiv="content-language" content="en" />
    <title>PodHawk Installation Programme</title>

    <meta name="robots" content="noindex, nofollow" />
    <meta name="description" content="PodHawk" />
    <meta name="author" content="Peter Carter, Birmingham, UK" />
	    
    <link rel="stylesheet" type="text/css" href="podhawk/install/install.css" />

    <script language="JavaScript" type="text/javascript">
<!--

function hide(object) 
{    
if (object.value == 'sqlite' || object.value == 'sqlite3') {
    document.getElementById('notsqlite').style.display = 'none';
    } else {
    document.getElementById('notsqlite').style.display = 'block';
    }

}

function selectAllText(id) {

    document.getElementById(id).focus();
    document.getElementById(id).select();
}


-->
</script>

</head>
EOF;

## The footer
$v = THIS_PH_VERSION;
$footer = <<<EOF

<div id="footer">

<p>PodHawk $v</p>
<p>Podcasting to the world</p>

</div>
</div>

</body>
</html>

EOF;

## Page 0 - check php version, select language and check new installation or LoudBlog upgrade.
if ($page == 0) {

$body = <<<EOF

<body>

<div id="wrapper">

<div id="header">
	<h1>{$trans['install']}</h1>
	<h3>{$trans['intro']}</h3>	
	</div>

<div id="navbar">
<table>
<tr><td class="focus">{$trans['intro']}</td></tr>
<tr><td>{$trans['writable']}</td></tr>
<tr><td>{$trans['db']}</td></tr>
<tr><td>{$trans['config']}</td></tr>
<tr><td>{$trans['ftp']}</td></tr>
<tr><td>{$trans['finished']}</td></tr>
</table>
</div>

<div id="content">
EOF;

if (version_compare(PHP_VERSION, '5.0.0', '<')) {

$body .= "<p>{$trans['not_php_5']}</p>";

	}  else {

$body .=<<<EOF

<h3>{$trans['welcome']}</h3>
<p>{$trans['welcome_2']}</p>
<form action="install.php?page=1" method="post">
<table>
<tr>
<td class="left">{$trans['select_lang']}</td>
<td class="center">
	<select name="language" class="wide">
EOF;

foreach ($languages as $language) {
	$body .= "<option value=\"" . $language . "\">" . $language . "</option>";
	}

$body .= <<<EOF
</select>
</td><td class="right"></td></tr>
<tr>
<td class="left">{$trans['do_what']}</td>
<td class="center">
<input type="radio" name="install_type" value="new_install" $checked1 />{$trans['new_install']} <br />
<input type="radio" name="install_type" value="convert_loudblog" $checked2 />{$trans['convert']}<br />
EOF;

if ($configured) // only show update to 1.83 and update DB options if there is already a valid PodHawk config file
{
	$body .=
	"<input type=\"radio\" name=\"install_type\" value=\"re-install\" $checked3 />{$trans['upgrade1']}<br />
	<input type=\"radio\" name=\"install_type\" value=\"update_database\" $checked3 />{$trans['upgrade6']}";
}

$body .= <<<EOF
</td><td class="right"></td>
</tr>
<tr><td class="left">
<input type="submit" value="{$trans['ready']}" />
</td><td></td><td></td>
</tr>
</table>
</form>
<p class><script language="javascript" type="text/javascript">
<!--
document.write('{$trans['javascript_on']}');
//-->
</script><noscript>{$trans['javascript_off']}</noscript></p>
EOF;

// warning if gd extension is not presesnt
if (!extension_loaded('gd')) {
$body .= "<p>{$trans['no_gd']}</p>";
		}

// another warning if the session save path is not writable
/* delete - it displays an error when an open base_dir restriction is in force
$ssp = ini_get('session.save_path');
if (!is_writable($ssp)) {
$body .= "<p class=\"msg\">{$trans['ssp_not_writable_1']}  $ssp  {$trans['ssp_not_writable_2']}</p>";
		}
*/	
	if ($status == 15)  {

	$body .= "<p class=\"msg\">$error_message</p>";

		}
	}

$body .= "</div>";
}
	

## Page 1 Introduction - explain install process

if ($page == 1)  {

$body = <<<EOF

<body>

<div id="wrapper">

<div id="header">
	<h1>{$trans['install']}</h1>
	<h3>{$trans['intro']}</h3>	
	</div>

<div id="navbar">
<table>
<tr><td class="focus">{$trans['intro']}</td></tr>
<tr><td>{$trans['writable']}</td></tr>
<tr><td>{$trans['db']}</td></tr>
<tr><td>{$trans['config']}</td></tr>
<tr><td>{$trans['ftp']}</td></tr>
<tr><td>{$trans['finished']}</td></tr>
</table>
</div>

<div id="content">

<h3>{$trans['prelim']}</h3>
EOF;

if ($windows) // different explanation of permissions for a Windows install
{
	$body .= "<p>{$trans['prelim_1_windows']}</p>";
}
else
{
	$body .= "<p>{$trans['prelim_1']}</p>";
}


if ($_SESSION['install_type'] == "new_install") // new PodHawk install
{

	$body .= '<p>' . $trans['prelim_2'] .'</p></ul>';

	if(!empty($supported_databases))
	{
		foreach ($supported_databases as $database)
		{
			$body .= "<li>{$database[1]}</li>";
		}
		$body .= '</ul>';

		$body .= "<p>{$trans['prelim_3']}</p>";
	}
	else
	{
		die ($trans['prelim_4']);
	}

	if (extension_loaded('ftp') && !$windows)
	{
		$body .= "<p>{$trans['prelim_ftp']}</p>";
	}

}

elseif ($_SESSION['install_type'] == 'convert_loudblog') // converting from LoudBlog

{ 

	$body .=<<<EOF
<p>{$trans['prelim_lb_1']}</p>
<ul>
<li>{$trans['prelim_lb_2']}</li>
<li>{$trans['prelim_lb_3']}
	<ul>
	<li>{$trans['prelim_lb_4']}</li>
	<li>{$trans['prelim_lb_5']}</li>
	{$trans['prelim_lb_6']}
	</ul>
</li>
</ul>
	
EOF;

	if (extension_loaded('ftp') && !$windows)
	{
		$body .= "<p>{$trans['prelim_ftp']}</p>";
	}

	if ($status == 15)
	{

	$body .=<<<EOF

<p class="msg">$error_message</p>

EOF;
	}
}

elseif ($_SESSION['install_type'] == 're-install') // upgrading existing PodHawk site

{

	$body .=<<<EOF
<p>{$trans['upgrade2']}</p>
<p>{$trans['upgrade3']}</p>
EOF;

}

$body .= <<<EOF
<p>{$trans['ready']}</p><br />
<form action="install.php?page=2" method="post">
<input type="submit" value="{$trans['lets_go']}" />
</form>

</div>
EOF;

} //end of page 1

## Page 2 - check whether directories are writable
if ($page == 2)  {

$body = <<<EOF
<body>

<div id="wrapper">

<div id="header">
	<h1>{$trans['install']}</h1>
	<h3>{$trans['writable']}</h3>	
	</div>

<div id="navbar">
<table>
<tr><td>{$trans['intro']}</td></tr>
<tr><td class="focus">{$trans['writable']}</td></tr>
<tr><td>{$trans['db']}</td></tr>
<tr><td>{$trans['config']}</td></tr>
<tr><td>{$trans['ftp']}</td></tr>
<tr><td>{$trans['finished']}</td></tr>
</table>
</div>

<div id="content">

EOF;
if ($status == 2)  { // if the cache directories are already in place

	$body .=<<<EOF
<h3>{$trans['cache_1']}</h3>
<p>{$trans['cache_2']}</p>
<br />
<form action="install.php?page=3" method="post">
<input type="submit" value="{$trans['next']}" />
</form>

EOF;

	} else { // if cache directories not already in place

	$body .=<<<EOF

<h3{$trans['cache_3']}</h3>

<p>{$trans['cache_4']}</p>

<table>
<tr><th>{$trans['dir']}</th><th>{$trans['cache_6']}</th></tr>

EOF;

foreach ($writable as $dir=>$property) {

$body .= "<tr><td class=\"left\">$dir</td><td class=\"center {$property['class']}\">{$property['writable']}</td></tr>";

	}
$body .= "</table>";

		if ($status == 1) { // if all the parent directories are writeable

	$body .= <<<EOF
<p>{$trans['cache_5']}</p>
<br />
<form action="install.php?page=3" method="post">
<input type="submit" value="{$trans['next']}" />
</form>
EOF;
		}  else  {  // if some are not writeable

	$body .= <<<EOF
<p>{$trans['cache_7']}</p>
<br />
<form action="install.php?page=2" method="post">
<input type="submit" value="{$trans['cache_8']}" />
</form>
EOF;
		} // close 'some not writeable'
	} // close 'cache dirs do not already exist'
$body .= "</div>";


}

## Page 3 - change permissions on parent directories of cache directories back to 0755

if ($page == 3) {

$body =<<<EOF

<body>

<div id="wrapper">

<div id="header">
	<h1>{$trans['install']}</h1>
	<h3>{$trans['writable']}</h3>	
	</div>

<div id="navbar">
<table>
<tr><td>{$trans['intro']}</td></tr>
<tr><td class="focus">{$trans['writable']}</td></tr>
<tr><td>{$trans['db']}</td></tr>
<tr><td>{$trans['config']}</td></tr>
<tr><td>{$trans['ftp']}</td></tr>
<tr><td>{$trans['finished']}</td></tr>
</table>
</div>

<div id="content">

<h3>{$trans['cache_9']}</h3>
<ul>
<li>{$trans['cache_10']}</li>
<li>{$trans['cache_11']}</li>
<li>{$trans['cache_12']}</li>
<li>{$trans['cache_13']}</li>
</ul>
EOF;

 
if (!$windows) // we don't try to revert any permissions in Windows
{
	$body .= <<<EOF

	<p>{$trans['cache_14']}</p>	

	<table>
	<tr><th>{$trans['dir']}</th><th>{$trans['permissions']}</th></tr><th></th>
EOF;

foreach ($permissions as $dir=>$property)  {

	$body .= "<tr><td class=\"left\">" . $dir . "</td>
		<td class=\"center {$property['class']}\">{$property['permissions']}</td>
		<td class=\"right\"></td>
		</tr>";

	}

	$body .= "</table>";
}
	if ($status == 2) // cache dirs made, parents still 0777
	{

	$body .=<<<EOF

<p>{$trans['cache_15']}</p>
<br />
<form action="install.php?page=3" method="POST">
<input type="submit" value="{$trans['cache_8']}" />
</form>
</div>
EOF;

	}

	elseif ($status == 3) // everything OK with cache directories and parents

	{

		if ($windows)
		{
			$body .= "<p>{$trans['cache_14_windows']}</p>";
		}
		else
		{
			$body .= "<p>{$trans['cache_16']}</p><br />";
		}
		
		if ($_SESSION['install_type'] == 're-install')
		{
			$body .= "<form action=\"install.php?page=5\" method=\"post\">";
		}
		else
		{
			$body .= "<form action=\"install.php?page=4\" method=\"post\">";
		}
		$body .= "<input type=\"submit\" value=\"{$trans['next']}\" />
		</form>
		</div>";
	
	}

}

## Page 4 - form for submitting database data

if ($page == 4)  {

	

	$body =<<<EOF
<body>

<div id="wrapper">

<div id="header">
	<h1>{$trans['db']}</h1>
	<h3>{$trans['creating_db']}</h3>	
	</div>

<div id="navbar">
<table>
<tr><td>{$trans['intro']}</td></tr>
<tr><td>{$trans['writable']}</td></tr>
<tr><td class="focus">{$trans['db']}</td></tr>
<tr><td>{$trans['config']}</td></tr>
<tr><td>{$trans['ftp']}</td></tr>
<tr><td>{$trans['finished']}</td></tr>
</table>
</div>

<div id="content">
EOF;

// new install
if ($_SESSION['install_type'] == 'new_install')  {

		if ($status == 4) {
	
	$body .= "<h3>{$trans['sorry']}</h3>
		<p class=\"msg\">{$trans['db_1']}'$error_message'</p>";

		}

		if ($status == 5)  {
	$body .="<h3>{$trans['sorry']}</h3>
		<p class=\"msg\">{$trans['db_2']}</p>";
		}

		if ($status == 7) {
	$body .= "<h3>{$trans['sorry']}</h3>
		<p class=\"msg\">$error_message</p>";

	$body .= "<p class=\"msg\">{$trans['db_3']}</p></div>";

		}  else  {

	$body .=<<<EOF
<form action="install.php?page=5" method="post">
<table>
<tr><th colspan="3">{$trans['db_4']}</th></tr>
<tr>
	<td class="left">{$trans['db_5']}</td>
	<td class="center">
		<input type="text" name="nickname" value="$nickname" />
	</td>
	<td class="right">{$trans['db_6']}</td>
</tr>
<tr>
	<td class="left">{$trans['db_7']}</td>
	<td class="center">
		<input type="text" name="login" value="$login" />
	</td>
	<td class="right">{$trans['db_8']}</td>
</tr>
<tr>
	<td class="left">Your password</td>
	<td class="center">
		<input type="text" name="password" value="$password" />
	</td>
	<td class="right">{$trans['db_9']}</td>
</tr>
</table>

<table>
<tr><th colspan="3">{$trans['db_10']}</th></tr>
<tr>
	<td class="left">{$trans['type']}</td>
	<td class="center">
		<select id="select_database" class="wide" onchange="hide(this)" name="sqltype" >
EOF;

	foreach ($supported_databases as $database) {
		$selvar = "selected_" . $database[0];
		if ($selvar == 'selected_sqlite3') $selvar = 'selected_sqlite';
		$selected = $$selvar;
		$body .= "\n<option value=\"{$database[0]}\" $selected>{$database[1]}</option>";
			}

		$body .=<<<EOF
		\n</select>
	</td>
	<td class="right">{$trans['db_11']}</td>
</tr>
</table>
<div id="notsqlite">
<table>
<tr>
	<td class="left">{$trans['host']}</td>
	<td class="center">
		<input type="text" name="sqlhost" value="$sqlhost" />
	</td>
	<td class="right">{$trans['db_12']}</td>
</tr>
<tr>
	<td class="left">{$trans['database']}</td>
	<td class="center">
		<input type="text" name="sqldata" value="$sqldata" />
	</td>
	<td class="right">{$trans['db_13']}</td>
</tr>
<tr>
	<td class="left">{$trans['username']}</td>
	<td class="center">
		<input type="text" name="sqluser" value="$sqluser" />
	</td>
	<td class="right">{$trans['db_14']}</td>
</tr>
<tr>
	<td class="left">{$trans['password']}</td>
	<td class="center">
		<input type="text" name="sqlpass" value="$sqlpass" />
	</td>
	<td class="right">{$trans['db_15']}</td>
</tr>
</table>
</div>

<table>
<tr><th colspan="3">{$trans['db_16']}</th></tr>
<tr>
	<td class="left">{$trans['db_17']}</td>
	<td class="center">
		<input type="text" name="siteurl" value="$siteurl" />
	</td>
	<td class="right">{$trans['db_18']}</td>
</tr>
<tr>
	<td class="left">{$trans['db_19']}</td>
	<td class="center">
		<input type="text" name="email" value="$email" />
	</td>
	<td class="right"></td>
</tr>

<tr>
	<td class="left"></td>
	<td class="center">
		<input type="submit" value="{$trans['db_20']}" />
	</td>
	<td class="right"></td>
</tr>
</table>
</form>
</div>
<script language="JavaScript" type="text/javascript">
	var s = document.getElementById('select_database');
	var v = s.value;
	if (v == 'sqlite' || v == 'sqlite3') {
		document.getElementById('notsqlite').style.display = 'none';
		}
</script>	

EOF;
			}// close 'we are not displaying database write errors'

		} else { //if install_type == convert_loudblog

	if ($status == 5) { // there is a problem with the LoudBlog config file and we have been sent here from page 5
$body .= "<p>{$trans['db_21']}</p>";
	}  
	elseif ($status == 7) {
$body .= "<h3>{$trans['sorry']}</h3><p>$error_message</p>";
	}
	else {
$body .=<<<EOF
<p>{$trans['db_22']}</p>
<p>{$trans['db_23']}</p>
EOF;
	}
$body .=<<<EOF
<br />
<form action="install.php?page=5" method="post">
<input type="submit" value="{$trans['db_24']}" />
</form>
</div>
EOF;

		}// close install_type = convert_loudblog

	} // close page 4

## Page 5 - the new configuration file

if ($page == 5) {

	$body =<<<EOF
<body>

<div id="wrapper">

<div id="header">
	<h1>{$trans['config']}</h1>
	<h3>{$trans['config_1']}</h3>	
	</div>

<div id="navbar">
<table>
<tr><td>{$trans['intro']}</td></tr>
<tr><td>{$trans['writable']}</td></tr>
<tr><td>{$trans['db']}</td></tr>
<tr><td class="focus">{$trans['config']}</td></tr>
<tr><td>{$trans['ftp']}</td></tr>
<tr><td>{$trans['finished']}</td></tr>
</table>
</div>

<div id="content">

EOF;

if ($_SESSION['install_type'] == 're-install')
{
	$body .= "<p>{$trans['upgrade4']}</p>
	<p>{$trans['upgrade5']}</p><br /> ";
}
else
{
	
	if ($status == 11)
	{ // page 6 cannot find the config file and has sent us back here

		$body .= "<h3>{$trans['sorry']}</h3>
		<p class=\"msg\">{$trans['config_2']}</p>";

	}
	else
	{

		$body .= "<h3>{$trans['good']}</h3>
				<p>{$trans['config_3']}</p>";

		if ($status == 9 || $status == 10)
		{
			$body .= "<p>{$trans['config_4']}";
			if ($status == 10)
			{
				$body .= "{$trans['config_5']}";
			}
			$body .= ".</p>";

		}
	}

	$config = (isset($_SESSION['config_file'])) ? $_SESSION['config_file'] : $config_file;

	$body .=<<<EOF

<p>{$trans['config_6']}</p>
<ul>
	<li>{$trans['config_7']}</li>
	<li>{$trans['config_8']}</li>
	<li>{$trans['config_9']}</li>
	<li>{$trans['config_10']}</li>
	<li>{$trans['config_11']}</li>
</ul>

<textarea id="config" onClick="selectAllText('config')">$config</textarea>
EOF;
	}

	if ($windows || $_SESSION['install_type'] == 're-install') // we skip the FTP page if we are re-installing PodHawk, or if we have a Windows machine
	{
		$theNextPage = 7;
	}
	else
	{
		$theNextPage = 6;
	}

	$body .=<<<EOF
<form action="install.php?page=$theNextPage" method="post">
<input type="submit" value="{$trans['config_12']}" />
</form>
</div>
EOF;

} // close page 5 

## Page 6 - FTP data


if ($page == 6)  {

$body =<<<EOF
<body>

<div id="wrapper">

<div id="header">
	<h1>{$trans['ftp']}</h1>
	<h3>{$trans['ftp_1']}</h3>	
	</div>

<div id="navbar">
<table>
<tr><td>{$trans['intro']}</td></tr>
<tr><td>{$trans['writable']}</td></tr>
<tr><td>{$trans['db']}</td></tr>
<tr><td>{$trans['config']}</td></tr>
<tr><td class="focus">{$trans['ftp']}</td></tr>
<tr><td>{$trans['finished']}</td></tr>
</table>
</div>

<div id="content">

<h3>FTP data</h3>
EOF;

if (strpos(PHP_SAPI, 'cgi') !== FALSE) // php runs under CGI
{
	$body .= <<<EOF

<p>{$trans['ftp_1a']}<p>

<form action="install.php?page=7" method="post">
<input type="submit" value="{$trans['complete']}" />
</form>
</div>
EOF;
}

elseif (!extension_loaded('ftp')) // no ftp extension
{

$body .=<<<EOF

<p>{$trans['ftp_2']}</p><br />
<form action="install.php?page=7" method="post">
<input type="submit" value="{$trans['complete']}" />
</form>
</div>
EOF;

}
else // PHI runs as apache module, and ftp extension is available
{

	if ($status == 13) // page 7 has found an error in the ftp data and has sent us back here
	{
	$body .= "<p>$error_string</p>";

	}
	else
	{

	$body .= "<p>{$trans['ftp_3']}</p>";

	}

	$body .=<<<EOF
<br />
<form action="install.php?page=7" method="post">
<table>
<tr>
	<td class="left">{$trans['ftp_4']}</td>
    	<td class="center">
    	<input name="ftp_server" id="ftp_server" type="text" value="$ftp_server" />
   	</td>
    	<td class="right">
    	{$trans['ftp_5']}
    	</td>
</tr>

<tr>
    <td class="left">{$trans['ftp_6']}</td>
    <td class="center">
    <input name="ftp_user" id="ftp_user" type="text"
    value="$ftp_user" />
    </td>
    <td class="right">
    {$trans['ftp_7']}
    </td>
</tr>

<tr>
    <td class="left">{$trans['ftp_8']}</td>
    <td class="center">
    <input name="ftp_pass" id="ftp_pass" type="text"
    value="$ftp_pass" />
    </td>
    <td class="right">
    {$trans['ftp_9']}
    </td>
</tr>

<tr>
    <td class="left">{$trans['ftp_10']}</td>
    <td class="center">
    <input name="ftp_path" id="ftp_path" type="text"
    value="$ftp_path" />
    </td>
    <td class="right">
    {$trans['ftp_11']}
    </td>
</tr>

<tr>
	<td class="left">
	<input type="submit" name="submit_ftp" value="{$trans['ftp_12']}" />
	</td>
	<td class="center"></td>
	<td class="right"></td>
</tr>

<tr>
	<td colspan="3">{$trans['ftp_13']}</td>
</tr>

<tr>
	<td class="left">
	<input type="submit" name="skip_ftp" value="{$trans['ftp_14']}" />
	</td>
	<td class="center"></td>
	<td class="right"></td>
</tr>

</table>
</form>
</div>

EOF;

		}// end ftp extension loaded

	}// end page = 6

## Page 7 THE END

if ($page == 7)
{

	$body =<<<EOF

<body>

<div id="wrapper">

<div id="header">
	<h1>{$trans['finished']}</h1>
	<h3>{$trans['fin_1']}</h3>	
	</div>

<div id="navbar">
<table>
<tr><td>{$trans['intro']}</td></tr>
<tr><td>{$trans['writable']}</td></tr>
<tr><td>{$trans['db']}</td></tr>
<tr><td>{$trans['config']}</td></tr>
<tr><td>{$trans['ftp']}</td></tr>
<tr><td class="focus">{$trans['finished']}</td></tr>
</table>
</div>

<div id="content">

<h3>{$trans['fin_2']}</h3>
EOF;

if (!empty($databaseUpdate))
{
	$body .= "<p>$updateMessage</p>";
}

$body .= "<p>{$trans['fin_3']}</p>
		<p>{$trans['fin_4']}</p> 
		<p>{$trans['fin_5']}</p>";

	if (isset($_SESSION['install_type']) && $_SESSION['install_type'] == 'convert_loudblog')
	{
		$body .= "<p>{$trans['fin_6']}</p>";
	}
	$body .= '</div>';

} // close page 7
?>
