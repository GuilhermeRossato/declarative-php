<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class BasicFunctionalityTest extends TestCase
{
	public function testBasicElementFlattening()
	{
		$div = new Element("div");

		$this->assertTrue(
			method_exists($div, "__toString"), "Missing a string representation method of the element");

		$this->assertTrue(method_exists($div, "render"), "Missing a render representation method of the element");

		$stringResult = $div->__toString();
		$this->assertSame(
			$stringResult,
			"<div></div>",
			"Received unexpected string result from div string:\n\"".$stringResult."\""
		);

		$renderResult = $div->render();
		$this->assertSame(
			$renderResult,
			"<div></div>",
			"Received unexpected render result from div string:\n\"".$renderResult."\""
		);
	}

	public function testBasicPageFlattening()
	{
		$page = new Page();

		$this->assertTrue(
			method_exists($page, "__toString"),
			"Missing a string representation method of the element"
		);

		$this->assertTrue(
			method_exists($page, "render"),
			"Missing a render representation method of the element"
		);

		$stringResult = $page->__toString();
		$expectedPageString = "<!doctype html><html><head></head><body></body></html>";

		$this->assertSame(
			$stringResult,
			$expectedPageString,
			"Received unexpected string result from page string:\n\"".$stringResult."\""
		);

		$renderResult = $page->render();
		$this->assertSame(
			$renderResult,
			$expectedPageString,
			"Received unexpected string result from page string:\n\"".$renderResult."\""
		);
	}
}