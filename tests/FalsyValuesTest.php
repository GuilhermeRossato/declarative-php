<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class FalsyValuesTest extends TestCase {

	public function testFalsyProperties() {
		$element = new Element("span", ["class" => 0]);

		$this->assertSame(
			(string) $element,
			'<span class="0"></span>'
		);
	}

	public function testFalsyContent() {
		$element = new Element("span", false, 0);

		$this->assertSame(
			(string) $element,
			"<span>0</span>"
		);
	}

	/**
     * @expectedException InvalidArgumentException
     */
	public function testFalsyTagType() {
		$element = new Element(null, false, 0);
	}
}