<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class PropertiesWithoutValuesTest extends TestCase {

	public function testButtonDisabled() {
		$button = new Element("button", ["disabled" => true], "content");

		$this->assertSame(
			"<button disabled>content</button>",
			(string) $button,
			"Received unexpected string result from element"
		);
	}

	public function testButtonEnabled() {
		$button = new Element("button", ["disabled" => false], "content");

		$this->assertSame(
			"<button>content</button>",
			(string) $button,
			"Received unexpected string result from element"
		);
	}

	public function testButtonDisabledWithText() {
		$button = new Element("button", ["disabled" => "true"], "Click me!");

		$this->assertSame(
			'<button disabled="true">Click me!</button>',
			(string) $button,
			"Received unexpected string result from element"
		);
	}

}
