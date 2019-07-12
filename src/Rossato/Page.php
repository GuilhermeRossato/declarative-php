<?php

namespace Rossato;

use Rossato\Element;

/**
 *	A class used to represent an HTML page
 */

class Page extends Element {
	public function __construct($content = [], $htmlConfig = [], $bodyConfig = []) {
		$this->head = new Element("head");
		$this->body = new Element("body", $bodyConfig, $content);
		parent::__construct("html", $htmlConfig, [$this->head, $this->body]);
	}

	/**
	 * Transform the element and its content into raw HTML code
	 * @return string The compressed HTML code that represents the object and its content
	 */
	public function flatten() {
		return "<!doctype html>".parent::flatten();
	}
}
