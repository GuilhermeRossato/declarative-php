# Declarative PHP

A micro-framework for building declarative and static web applications in PHP

You can think of this framework as a ReactDOM withou JSX, but for PHP developers.

## Getting started

Usage is as simple as possible:

```php
use Rossato\Element;

$div = new Element(
    "div",                   // tag name
    ["class" => "custom"],   // properties (as associative array)
    "text"                   // content
);

echo $div;

```

This outputs an html `div` element with the `custom` class and `text` as content.

### Nested examples

Elements can be combines to create more complex structures:

```php
use Rossato\Element;

echo new Element(
    "ul",
    ["style" => "margin: 10px"],
    [
        new Element("li", null, "First element"),
        new Element("li", null, "Second element")
    ]
);
```

Which is equivalent to:

```php
use Rossato\Element;

$list = new Element(
    "ul",
    ["style" => "margin: 10px"]
);
$list->add(
    new Element("li", null, "First element")
);
$list->add(
    new Element("li", null, "Second element")
);
echo $list;
```

Elements can be nested multiple times by other elements or text, they later get 'flatened' into a html content.

### Inheritance and Composition

You can compose you own elements as classes to abstract and separate complex behaviour:

```php
use Rossato\Element;

class Form extends Element {
    constructor($label) {
        $button = new Element(
            "input",
            [
                "type => "submit", 
                "value" => $label
            ]
        );

        parent::__construct(
            "form",
            ["action => "endpoint.php"],
            $button
        );
    }
}

echo new Form("Click here");
// returns <form><input type="submit" value="Click here" /></form>
```

### Page example

With the Page element, you can specify a full page as output (or extend it and call that your app)

```php
use Rossato\Element;
use Rossato\Page;

class App extends Page {
    constructor() {
        parent::__construct();

        $title = new Element("title", [], "My first declarative app in php");
        $div = new Element("div", [], "Hello world");

        $this->head->add($title);
        $this->body->add($div);
    }
}

$app = new App();

echo $app;

```

This renders your declarative App like the following: (formatted from a minified result for demonstration)

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

### Managing data

Data has to be passed around in a top-down fashion:

```

class PostList extends Element {
    constructor($postData) {
    
        $postList = [];
        foreach ($postsData as $name=>$post) {
                $postList[] = new Element(
                    "article",
                    [],
                    new Element("h2", [], $name),
                    new Element("p", [], $post)
                )
        }
        
        parent::__construct("div", ["class" => "post-list"], $postList);
    }
}

$postsData = [
    "My life as a developer" => "It was fun",
    "I'm declaring" => "It is fun",
    "I will declare" => "Something should be here"
];

echo new PostList($postData);

```

Obviously, you're left alone regarding how and where you get data. But be advised: **You must [strip the tags](http://php.net/manual/pt_BR/function.strip-tags.php) from your data!**

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

## Performance

It has the same drawbacks of react, except there's no virtual DOM, so it's a bit slower than a static page.

Thanksfully we have html caching for static content! The basic idea is:

```php

$cacheFilename = "cache.html";
if (file_exists($cacheFilename)) {
    readfile($cacheFilename);
} else {
    $pageContent = (string) new Page( ... );
    file_put_contents($cacheFilename, $pageContent);
    echo $pageContent;
}

```

Now, this doesn't take in account cache invalidation, you'd have to delete the cache to refresh it, but it works nicely.

Even better, you could have a `clear cache` method that automatically loads and creates the cache files, so that no end-user ever has to wait for the page to be processed ever again. (in theory).

However, this is a framework after all, there's always a "declarative-php" way of doing things so if you want to know more, read what the [docs have to say about cache](http://guilherme-rossato.com/declarative-php/caching-results/)
