<?php

use PHPUnit\Framework\TestCase;
use \Rossato\Element;
use \Rossato\Page;

class StyleCompressionTest extends TestCase {

	public function testSimpleStyleCompression() {
		$style = "margin: 0px ;\n";
		$style .= "padding : 0px ;\n";
		$element = new Element("div", ["style" => $style]);
		$elementStr = (string) $element;
		$this->assertSame(
			$elementStr,
			'<div style="margin:0px;padding:0px"></div>',
			"Received unexpected string result from styled element"
		);
	}

	public function testDelayedStyleCompression() {
		$element = new Element("p");
		$element->setAttribute("class", "header");
		$strBefore = (string) $element;

		$this->assertSame(
			$strBefore,
			'<p class="header"></p>',
			"Received unexpected string result from element"
		);

		$style = "    grid-template-columns: 30% 60% 10%;\n";
		$style .= "\t\tgrid-template-rows: 50px 0 50px\t;\t\n";
		$element->setAttribute("style", $style);

		$strAfter = (string) $element;

		$this->assertSame(
			$strAfter,
			'<p class="header" style="grid-template-columns:30% 60% 10%;grid-template-rows:50px 0 50px"></p>',
			"Received unexpected string result from styled element"
		);
	}

}