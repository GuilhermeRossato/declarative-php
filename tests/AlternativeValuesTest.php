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

	public function testZeroContent() {
		$element = new Element("span", null, 0);

		$this->assertSame(
			$element->render(),
			"<span>0</span>"
		);
	}

	public function testNullContent() {
		$element = new Element("span", null, null);

		$this->assertSame(
			$element->render(),
			"<span></span>"
		);
	}

	public function testFalseContent() {
		$element = new Element("span", null, false);

		$this->assertSame(
			$element->render(),
			"<span>false</span>"
		);
	}

	public function testTrueContent() {
		$element = new Element("span", null, true);

		$this->assertSame(
			$element->render(),
			"<span>true</span>"
		);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFalsyTagType() {
		$element = new Element(null, false, 0);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFalsyTagType() {
		$element = new Element(null, true, 0);
	}

}
