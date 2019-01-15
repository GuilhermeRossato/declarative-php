# declarative-php

A micro-framework for building declarative and static web applications in PHP

You can think of this framework as a ReactDOM for PHP developers.

## Usage

I've tried to keep usage as simple as possible:

```php
use Rossato\Element;

$div = new Element("div", ["class" => "my-class"], "div content");

echo $div;

```


# Full page example

```php
use Rossato\Element;
use Rossato\Page;

class App extends Page {
	constructor() {
		$head = new Element("title", [], "My first declarative app in php");
		$body = new Element("div", [], "Hello world");

		parent::__construct([
			"head" => $head,
			"body" => $body
		]);
	}
}

$app = new App();

echo $app;

```

This renders your declarative App like the following: (de-minified to showcase)

```html
<!DOCTYPE html>
<html>
<head>
	<title>Web App Test</title>
</head>
<body>
	<div>Hello world</div>
</body>
</html>
```

## Instalation

Add this framework to your project using composer:

```
composer require rossato/declarative-php
```

and then require composer's autoload in your files:

```php
require "vendor/autoload.php";

use Rossato\Element; // Add this line if you're using the Element class
```

Alternatively, you can download this repository and require the files inside `/src/` manually.