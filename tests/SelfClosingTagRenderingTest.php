<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;

class SelfClosingTagRenderingTest extends TestCase {

	public function testImageRendering() {
		$img = new Element("img", ["src" => "#"]);

		$imgStr = $img->__toString();

		$this->assertSame(
			'<img src="#" />',
			$imgStr,
			"Got unexpected string result from img element"
		);
	}

	public function testMetaFlattening() {
		$meta = new Element("meta", ["charset" => "utf-8"]);

		$metaStr = $meta->__toString();

		$this->assertSame(
			'<meta charset="utf-8" />',
			$metaStr,
			"Got unexpected string result from meta element"
		);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testImageContentConstraint() {
		$img = new Element("img", ["src" => "#"], 'content');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testImageAddConstraint() {
		$img = new Element("img", ["src" => "#"]);
		$img->add(new Element("div"));
	}

}
