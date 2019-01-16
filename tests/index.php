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
			throw new Exception("Received unexpected string result from img string:\n\"".$imgStr."\"");
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

	public function testStyleCompressing() {
		$style = "margin: 0px ;\n";
		$style .= "padding : 0px ;\n";
		$element = new Element("div");
		$element->add("style", $style);
		$elementStr = (string) $element;
		if ($elementStr !== '<div style="margin:0px;padding:0px"></div>') {
			throw new Exception("Received unexpected string result from styled element:\n\"".$elementStr."\"");
		}
	}

}

multitest("BasicTest", [
	"testBasicElementFlattening" => "Testing basic element flattening",
	"testBasicPageFlattening" => "Testing basic page flattening",
	"testImageFlattening" => "Testing basic image flattening",
	"testImageAddConstraint" => "Testing constraints on void elements",
	"testStyleCompressing" => "Testing automatic style compression on elements",
]);
