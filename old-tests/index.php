<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once "primitive.php";

use \Rossato\Element;
use \Rossato\Page;

class BasicTest
{
	public function testBasicElementFlattening()
	{
		$divElement = new Element("div");
		if (method_exists($divElement, "toString")) {
			throw new Exception("Missing a string representation method of the element");
		}
		if ($divElement->__toString() !== "<div></div>") {
			throw new Exception("Received unexpected string result from div string:\n\"".$divElement."\"");
		}
	}

	public function testBasicPageFlattening()
	{
		$page = new Page();
		if (method_exists($page, "toString")) {
			throw new Exception("Missing a string representation method of the page");
		}
		if ($page->__toString() !== "<!doctype html><html><head></head><body></body></html>") {
			throw new Exception("Received unexpected string result from page string:\n\"".$page."\"");
		}
	}

	public function testImageFlattening()
	{
		$img = new Element("img", ["src" => "#"]);

		$imgStr = $img->__toString();

		if ($imgStr !== '<img src="#">' && $imgStr !== '<img src="#" />') {
			throw new Exception("Received unexpected string result from img element:\n\"".$imgStr."\"");
		}
	}

	public function testMetaFlattening() {
		$meta = new Element("meta", ["charset" => "utf-8"]);

		$metaStr = $meta->__toString();

		if ($metaStr !== '<meta charset="utf-8">' && $metaStr !== '<meta charset="utf-8" />') {
			throw new Exception("Received unexpected string result from meta element:\n\"".$metaStr."\"");
		}
	}

	public function testImageAddConstraint()
	{
		$img = new Element("img", ["src" => "#"]);

		try {
			$img->add(new Element("div"));
		} catch (Exception $err) {
			return true;
		}
	}

	public function testStyleCompression() {
		$style = "margin: 0px ;\n";
		$style .= "padding : 0px ;\n";
		$element = new Element("div", ["style" => $style]);
		//$element->setAttribute("style", $style);
		$elementStr = (string) $element;
		if ($elementStr !== '<div style="margin:0px;padding:0px"></div>') {
			throw new Exception("Received unexpected string result from styled element:\n\"".$elementStr."\"");
		}
	}

	public function testAddingContent() {
		$element = new Element("div");
		$element->add("Hello");
		$element->add(new Element("span", [], "World"));
		$elementStr = (string) $element;
		if ($elementStr !== '<div>Hello<span>World</span></div>') {
			throw new Exception("Received unexpected string result from element:\n\"".$elementStr."\"");
		}
	}

	public function testAddingAttributes() {
		$element = new Element("div");
		$element->setAttribute("style", "margin : 0px ; ");
		$element->addAttribute("class", "foo");
		$elementStr = (string) $element;
		if ($elementStr !== '<div style="margin:0px" class="foo"></div>') {
			throw new Exception("Received unexpected string result from element:\n\"".$elementStr."\"");
		}
	}

	public function testMultipleSubElements() {
		$element = new Element(
			"div",
			null,
			[
				"a",
				["b", "c"]
			],
			"d"
		);
		$elementStr = (string) $element;
		$expects = "<div>abcd</div>";
		if ($elementStr !== $expects) {
			throw new Exception("Received unexpected string result from element:\n\"".$elementStr."\"");
		}
	}

	public function testParameterWithoutValue() {
		$element1 = new Element("button", ["disabled" => true], "content");
		$element2 = new Element("button", null, "content");
		$element2->setAttribute("disabled");
		$element1Str = (string) $element1;
		$element2Str = (string) $element2;
		$expects = "<button disabled>content</button>";
		if ($element1Str !== $expects || $element2Str !== $expects) {
			throw new Exception("Received unexpected string result from element:\n\"".$elementStr."\"");
		}
	}

	public function testDeeplyNestedContentArrays() {
		$element = new Element("div", [], [[[[[[["Hello",[new Element("span", [], "World")]]]]]]]]);
		$elementStr = (string) $element;
		$expects = "<div>Hello<span>World</span></div>";
		if ($elementStr !== $expects) {
			throw new Exception("Received unexpected string result from element:\n\"".$elementStr."\"");
		}
	}
}

multitest("BasicTest", [
	"testBasicElementFlattening" => "Test basic element flattening",
	"testBasicPageFlattening" => "Test basic page flattening",
	"testImageFlattening" => "Test basic image flattening",
	"testMetaFlattening" => "Test meta (void) element flattening",
	"testImageAddConstraint" => "Test constraints when adding content to img element",
	"testStyleCompression" => "Test automatic style compression on elements",
	"testAddingContent" => "Test adding content to an element",
	"testAddingAttributes" => "Test adding attributes to an element",
	"testMultipleSubElements" => "Test adding multiple sub elements and variable parameter width",
	"testParameterWithoutValue" => "Test adding a parameter without value to an object",
	"testDeeplyNestedContentArrays" => "Test adding deeply nested array of elements",
]);