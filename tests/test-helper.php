<?php
function test($testClass, $testMethod, $testName) {
	echo "\t".trim($testName)."... ";

	if (!class_exists($testClass)) {
		echo "Failed\n";
		echo " -> Missing class named '".$testClass."'";
		return false;
	}

	$test = new $testClass();

	if (!method_exists($test, $testMethod)) {
		echo "Failed\n";
		echo " -> Missing method ".$testClass."->".$testMethod."()\n";
		return false;
	}

	try {
		$test->$testMethod();
		echo "Success\n";
		return true;
	} catch (Exception $err) {
		echo "Failed\n -> ";
		$errorString = str_replace(__DIR__, "/tests", trim((string) $err));
		echo $errorString;
		echo "\n";
	}
	return false;
}

