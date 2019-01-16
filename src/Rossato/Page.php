<?php

namespace Rossato;

use Rossato\Element;

/**
 *	A class used to represent an HTML page
 */

class Page extends Element {
	public function __construct($content = []) {
		$this->head = new Element("head");
		$this->body = new Element("body", [], $content);
		parent::__construct("html", [], [$this->head, $this->body]);
	}

	/**
	 * Transform the element and its content into raw HTML code
	 * @return string The compressed HTML code that represents the object and its content
	 */
	public function flatten() {
		$content = $this->getHeader();

		if ($this->tag === "img" || $this->tag === "input") {
			return $content;
		}

		foreach ((is_array($this->content) ? $this->content : [$this->content]) as $part) {
			$content .= ($part instanceof HTMLElement)?$part->flatten():$part;
		}

		$content .= "</".$this->tag.">";

		return "<!doctype html>".$content;
	}

	public function __toString() {
		return $this->flatten();
	}
}
