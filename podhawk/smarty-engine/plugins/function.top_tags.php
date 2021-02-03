<?php

function smarty_function_top_tags ($params, &$smarty) {

$smarty->clear_assign('tag_list', 'tag_weights');

$number = $params['number'];

$t = TagManager::instance();

$smarty->assign('tag_list', $t->getTopTagsList($number));
$smarty->assign('tag_weights',$t->getTopTagsWeights($number));

}
