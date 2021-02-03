{*   template for java upload   *}

<h1>{$trans.ftp}</h1>
<h3>{$settings.ftp_server}<h3>

<applet code="ZUpload" archive="modules/ZUpload.jar" width="450" height="250" border="0" alt="{$trans.nojava}">

<param name="host" value="{$settings.ftp_server}" />

<param name="user" value="{$settings.ftp_user}" />

<param name="pass" value="{$settings.ftp_pass}" />

<param name="path" value="{$settings.ftp_path}" />

<param name="postscript" value="index.php?page=record1" />

</applet> 

<input onClick="opener.location.reload(); window.close();" type="submit" value="{$trans.finished}" />
