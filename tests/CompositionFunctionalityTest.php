<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class CompositionFunctionalityTest extends TestCase {

	public function testAddingStringContent() {
		$element = new Element("div", null, "Hello");
		$this->assertSame(
			"<div>Hello</div>",
			(string) $element,
			"Got unexpected string result from element"
		);
	}

	public function testAddingMixedContent() {
		$element = new Element("div");
		$element->add("Hello");
		$element->add(new Element("span", [], "World"));

		$this->assertSame(
			'<div>Hello<span>World</span></div>',
			(string) $element,
			"Got unexpected string result from element"
		);
	}

	public function testAddingAttributes() {
		$element = new Element("div");
		$element->setAttribute("style", "margin : 0px ; ");
		$element->addAttribute("class", "foo");

		$this->assertSame(
			'<div style="margin:0px" class="foo"></div>',
			(string) $element,
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
			"<div>abcd</div>",
			(string) $element,
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
				null,
				"there"
			)
		);

		$this->assertSame(
			"<div><p>hi</p><span>there</span></div>",
			(string) $element,
			"Got unexpected string result from element"
		);
	}

	public function testDeeplyNestedContentArrays() {
		$element = new Element("div", [], [[[["H", [[["ello",[new Element("span", [], "World")]]]]]]]]);

		$this->assertSame(
			"<div>Hello<span>World</span></div>",
			(string) $element,
			"Received unexpected string result from element"
		);
	}

}
