{*  message to display if no posts are found  *}
<div class="warning">
<{#style_no_posts#|default:'p'}>{$trans.no_posts}</{#style_no_posts#|default:'p'}>
</div>

{*
Configuration variables

style_no_posts = tags to surround the 'no posts' message, <p>...</p> by default

*}
