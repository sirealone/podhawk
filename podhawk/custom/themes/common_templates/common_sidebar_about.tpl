{*  the 'about this site' section  *}

{*  PodHawk will encode your e-mail address so that robots which collect e-mail addresses cannot easily read it  *}
<ul>
<li>{#me_icon#|default:''}{$trans.info_about_me}</li>
<li>{#email_icon#|default:''}{include file='sidebar_email.tpl'}</li>
</ul>

{* 
Configuration variables

me_icon - html image tag for any image to go before 'about me' 
email_icon - html image tag for any image to go before e-mail link

*}
