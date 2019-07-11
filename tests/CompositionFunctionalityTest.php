<?php


use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class CompositionFunctionalityTest extends TestCase {

	public function testAddingStringContent() {
		$element = new Element("div", null, "Hello");
		$this->assertSame(
			(string) $element,
			"<div>Hello</div>",
			"Got unexpected string result from element"
		);
	}

	public function testAddingMixedContent() {
		$element = new Element("div");
		$element->add("Hello");
		$element->add(new Element("span", [], "World"));

		$elementStr = (string) $element;

		$this->assertSame(
			$elementStr,
			'<div>Hello<span>World</span></div>',
			"Got unexpected string result from element"
		);
	}

	public function testAddingAttributes() {
		$element = new Element("div");
		$element->setAttribute("style", "margin : 0px ; ");
		$element->addAttribute("class", "foo");

		$elementStr = (string) $element;

		$this->assertSame(
			$elementStr,
			'<div style="margin:0px" class="foo"></div>',
			"Got unexpected string result from element"
		);
	}

	public function testMultipleSubElements() {
		$element = new Element(
			"div",
			null,
			[
				"a",
				["b", "c"]
			],
			"d"
		);
		$elementStr = (string) $element;

		$this->assertSame(
			$elementStr,
			"<div>abcd</div>",
			"Got unexpected string result from element"
		);
	}
}