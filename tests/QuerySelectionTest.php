<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class QuerySelectionTest extends TestCase {

	public function testFindSimpleElement() {
		$dummyObject = new Element("p");
		$internalDiv = new Element("div");

		$element = new Element(
			"span",
			null,
			$dummyObject,
			$internalDiv
		);

		$divs = $element->querySelectorAll("div");


		$this->assertSame(
			1,
			count($divs)
		);

		$this->assertSame(
			$internalDiv,
			$divs[0]
		);
	}

	public function testFindWildcardElement() {
		$internalSpan = new Element("span");

		$element = new Element(
			"something-else",
			null,
			$internalSpan
		);

		$span = $element->querySelector("*");

		$this->assertSame(
			$internalSpan,
			$span
		);
	}

	public function testFindAllWildcardElement() {
		$firstInternalElement = new Element("p");
		$secondInternalElement = new Element("div");

		$element = new Element(
			"a",
			["class" => "homepage"],
			$firstInternalElement,
			$secondInternalElement
		);

		$elements = $element->querySelectorAll("*");

		$this->assertSame(
			$firstInternalElement,
			$elements[0]
		);

		$this->assertSame(
			$secondInternalElement,
			$elements[1]
		);
	}

	public function testFindElementByClass() {
		$firstInternalElement = new Element("div", ["class" => "dummy-div"]);
		$secondInternalElement = new Element("div", ["class" => "wally"]);

		$element = new Element(
			"div",
			null,
			$firstInternalElement,
			$secondInternalElement
		);

		$elements = $element->querySelectorAll(".wally");

		$this->assertSame(
			count($elements),
			1
		);

		$this->assertSame(
			$secondInternalElement,
			$elements[0]
		);
	}

	public function testMultipleQueries() {
		$element = new Element(
			"div",
			null,
			new Element("div", ["class" => "dummy-div"]),
			new Element("div", ["class" => "fixed-class"]),
			new Element("div", ["id" => "unique", "class" => "expected-class"])
		);

		$elements = $element->querySelectorAll("#unique, .fixed-class, .no-one");

		$this->assertSame(
			count($elements),
			2
		);

		$this->assertSame(
			"expected-class",
			$elements[0]->getAttribute("class")
		);

		$this->assertSame(
			"fixed-class",
			$elements[1]->getAttribute("class")
		);
	}
}
