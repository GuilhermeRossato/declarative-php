<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class FalsyValuesTest extends TestCase {

	public function testFalsyProperties() {
		$element = new Element("span", ["class" => 0]);

		$this->assertSame(
			'<span class="0"></span>',
			(string) $element
		);
	}

	public function testZeroContent() {
		$element = new Element("span", null, 0);

		$this->assertSame(
			"<span>0</span>",
			$element->render()
		);
	}

	public function testNullContent() {
		$element = new Element("span", null, null);

		$this->assertSame(
			"<span></span>",
			$element->render()
		);
	}

	public function testFalseContent() {
		$element = new Element("span", null, false);

		$this->assertSame(
			"<span>false</span>",
			$element->render()
		);
	}

	public function testTrueContent() {
		$element = new Element("span", null, true);

		$this->assertSame(
			"<span>true</span>",
			$element->render()
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
	public function testInvalidTruthyConfig() {
		$element = new Element("p", true, 0);
	}

}
