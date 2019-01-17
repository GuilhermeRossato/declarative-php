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
	$showError = true;
	foreach ($tests as $method => $description) {
		$success = test($testObject, $method, $description, $showError);
		usleep(200000);
		echo "\n";
		usleep(200000);
		if ($success === false) {
			$showError = false;
		}
	}
}

function test($testClass, $testMethod, $testName, $showFullError = true) {
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
		echo "Failed\n";
		echo " -> ";
		$errorString = str_replace(__DIR__, "/tests", trim((string) $err));
		if (strpos($errorString, "exception 'Exception' with message") === 0) {
			$errorString = substr($errorString, strlen("exception 'Exception' with message")+1);
		}
		if ($showFullError) {
			echo $errorString;
		} else if (strlen($errorString) < 70) {
			echo $errorString;
		} else {
			echo substr($errorString, 0, 70)."... [ommited]";
		}
		echo "\n";
	}
	return false;
}