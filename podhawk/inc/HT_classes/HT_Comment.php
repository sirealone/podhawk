<?php

class HT_Comment extends HT_Purifier
{
	// for comments using the the TinyMCE editor, we allow a restricted number of tags

	protected function setConfig()
	{
		$this->config->set('HTML.AllowedElements', 'p, b, a, i, u, strong, em, span, img, div, blockquote, ul, ol, li', 'code');

		$this->config->set('HTML.MaxImgLength', 20); // limit max size of images to roughly emoticon size

		$this->config->set('CSS.MaxImgLength', '20px'); // limit size of any css background image

		$this->config->set('HTML.Nofollow', TRUE); // add rel=nofollow to outgoing links

		$this->config->set('HTML.Parent', 'li'); // comments are displayed within <li> on page

		$this->config->set('HTML.AllowedAttributes', 'img.src, img.alt, img.title, a.href, a.rel, span.style'); // the attributes which our allowed elements will support
	}

}
?>
