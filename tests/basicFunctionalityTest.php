<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class BasicFunctionalityTest extends TestCase {

	public function testBasicElementFlattening() {
		$div = new Element("div");

		$this->assertTrue(
			method_exists($div, "render"),
			"Missing a render representation method of the element"
		);

		$this->assertSame(
			"<div></div>",
			$div->render(),
			"Got unexpected render result from div as string"
		);
	}

	public function testBasicPageFlattening() {
		$page = new Page();

		$this->assertTrue(
			method_exists($page, "__toString"),
			"Missing a string representation method of the element"
		);

		$this->assertTrue(
			method_exists($page, "render"),
			"Missing a render representation method of the element"
		);

		$expectedPageString = "<!doctype html><html><head></head><body></body></html>";

		$this->assertSame(
			$expectedPageString,
			$page->__toString(),
			"Got unexpected string result from page as string"
		);

		$this->assertSame(
			$expectedPageString,
			$page->render(),
			"Got unexpected string result from page as string"
		);
	}

	public function testCustomHtmlElement() {
		$element = new Element("my-element", ["class" => "root"]);

		$this->assertSame(
			'<my-element class="root"></my-element>',
			(string) $element,
			"Got unexpected string result from element as string"
		);
	}

}
