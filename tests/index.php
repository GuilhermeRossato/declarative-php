<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once "test-helper.php";

use Rossato\Element;

class BasicTest
{
	public function testCreate()
	{
		$divElement = new Element("div");
		if (method_exists($divElement, "toString")) {
			throw new Exception("Missing a string representation method of the element");
		}
		if ($divElement->__toString() !== "<div></div>") {
			throw new Exception("Received unexpected string result from div string");
		}
    }
}


test("BasicTest", "testCreate", "Testing div");