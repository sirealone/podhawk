{*   a silly game   *}

{include file='manager_head.tpl'}

<body id="comments">
<div id="wrapper">
	<div id="header">
	<h1>{$trans.game}</h1>
	
	</div> <!-- close header -->

{include file='menu.tpl'}

<div id="content">

{if $guess == $target}
	<p>Yes - my number was {$target}.</p>
	<p>You took {$tries} tries to get the right answer.</p>
	<form action="index.php?page=game" method="POST" enctype="multipart/form-data">
	<input type="submit" value="Another game?" />
	</form>
{else}

	{if !isset($smarty.post.target)}
		<p>OK, sucker, guess the number I am thinking of.</p>

	{elseif $test == false}
		<p>Listen, dumbo, you have to enter a whole number between 0 and 100. Now try again.</p>

	{elseif $guess > $target}
		<p>{$guess} is too high.</p>

	{elseif $guess< $target}
		<p>{$guess} is too low.</p>
	{/if}


	<p>Go on - have a guess!</p>	
	<form action="index.php?page=game" method="POST" enctype="multipart/form-data">
	<input type="text" name="myguess" />
	<!-- we use a hidden input to pass the value of $target to the next iteration  -->
	<input type="hidden" name="target" value="{$target}" />
	<!-- and another one to pass the number of tries  -->
	<input type="hidden" name="tries" value="{$tries}" />
	<input type="submit" value="Guess!" />
	</form>
{/if}

</div> <!--   close content   -->
{include file='manager_footer.tpl'}
