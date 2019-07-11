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

		$this->assertSame(
			(string) $element,
			'<div>Hello<span>World</span></div>',
			"Got unexpected string result from element"
		);
	}

	public function testAddingAttributes() {
		$element = new Element("div");
		$element->setAttribute("style", "margin : 0px ; ");
		$element->addAttribute("class", "foo");

		$this->assertSame(
			(string) $element,
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

		$this->assertSame(
			(string) $element,
			"<div>abcd</div>",
			"Got unexpected string result from element"
		);
	}

	public function testMultipleSideElements() {
		$element = new Element(
			"div",
			[],
			new Element(
				"p",
				null,
				"hi"
			),
			new Element(
				"span",
				false,
				"there"
			)
		);

		$this->assertSame(
			(string) $element,
			"<div><p>hi</p><span>there</span></div>",
			"Got unexpected string result from element"
		);
	}
}