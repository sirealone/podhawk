{*  'send me an e-mail' *}

{* puts e-mail address on the web-page, encoded in JavaScript to make life difficult for spam robots.
Uses the iTunes e-mail address by default - see settings page - if you want to use a different e-mail
address, amend the language file for your template, or send variable $send_to to the template  *}

{if isset($send_to) && isset($text)}
{mailto address=$send_to encode='javascript' subject=$trans.email_subject text=$text}
{else}
{mailto address=$trans.your_email encode='javascript' subject=$trans.email_subject text=$trans.send_me_email}
{/if}
