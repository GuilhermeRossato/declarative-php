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
			$div->render(),
			"<div></div>",
			"Got unexpected render result from div string"
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
			$page->__toString(),
			$expectedPageString,
			"Got unexpected string result from page string"
		);

		$this->assertSame(
			$page->render(),
			$expectedPageString,
			"Got unexpected string result from page string"
		);
	}
}