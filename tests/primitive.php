<?php

function multitest($testClass, $tests) {
	if (is_string($testClass)) {
		if (!class_exists($testClass)) {
			echo "Failed\n";
			echo " -> Missing class named '".$testClass."'";
			return false;
		}

		$testObject = new $testClass();
	} else {
		$testObject = $testClass;
	}
	// Sleeps are just to give an illusion of progress
	usleep(200000);
	echo "\n";
	usleep(200000);
	foreach ($tests as $method => $description) {
		test($testObject, $method, $description);
		usleep(200000);
		echo "\n";
		usleep(200000);
	}
}

function test($testClass, $testMethod, $testName) {
	echo "\t".trim($testName)."... ";

	if (is_string($testClass)) {
		if (!class_exists($testClass)) {
			echo "Failed\n";
			echo " -> Missing class named '".$testClass."'";
			return false;
		}

		$test = new $testClass();
	} else {
		$test = $testClass;
	}

	if (!method_exists($test, $testMethod)) {
		echo "Failed\n";
		echo " -> Missing method ".(is_string($testClass)?$testClass:'$object')."->".$testMethod."()\n";
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