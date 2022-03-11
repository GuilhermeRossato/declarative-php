<p align="center"><img src="https://github.com/GuilhermeRossato/declarative-php/blob/master/logo.png?raw=true" alt="Declarative PHP"/></p>
<p align="center">:hammer: Simple micro-framework for building declarative web applications in PHP :newspaper:</p>

You can think of this framework as a ReactDOM without JSX for PHP developers.

This framework works by creating dynamic elements declaratively through php classes that render into HTML.

## Getting started

The element class constructor receives the tag name, the properties array and the contents as arguments:

```php
use Rossato\Element;

$div = new Element(
    "div",                   // tag name
    ["class" => "custom"],   // properties (as associative array)
    "text"                   // content
);

echo $div;

```

This outputs an html `div` element with the `custom` class and `text` as content, like so: `<div class="custom">text</div>`

### Nested examples

Elements can be nested, the third, fourth, and other parameters can be other elements

```php
use Rossato\Element;

echo new Element(
    "ul",
    ["style" => "margin: 10px"],
    new Element("li", null, "First element"),
    new Element("li", null, "Second element")
);
```

You can also supply an array of elements or, if it does not contain children, just omit the parameter, or set it to null.

```php
use Rossato\Element;

echo new Element(
    "div",
    ["style" => "margin: 10px"],
    [
        new Element("input", ["type" => "text", "id" => "hello"]),
    ]
);
```

Generates the following:

```
<div><input type="text" id="hello"/></div>
```

### Functional usage

You are supposed to create components like these:

```php
use Rossato\Element;

function Form($url) {
    return new Element("form", ["action" => $url],
    	new Element("input", ["type" => "submit", "value" => "Submit"])
    );
}

function App() {
    return new Element("div", [],
    	Form("/send/"),
	Form("/submit/")
    ]);
}

echo App();
```

### Page example

The Page element is useful if you're doing top-level stuff, it is supposedly the page's HTML tag element:

```php
use Rossato\Element;
use Rossato\Page;

$page = new Page([
    new Element("title", [], "Title in the head tag")
], [
    new Element("h1", [], "Title in the body tag")
]);

echo $page; // "<html><head><title>Title in the head tag</title></head><body><h1>Title in the body tag</h1></body></html>"
```

## Inspiration

React (the javascript library / framework) has an interesting way of defining components and their hierarchical relations. This experiment is supposed to explore that on PHP.

PHP already has a DOM library internally, but it's full of features such as querying and parsing, this library is only focused on building html elements that are sanitized so that you can just do stuff. This library is [2 source files](https://github.com/GuilhermeRossato/declarative-php/tree/master/src/Rossato) big and handles attribute and content sanitization.

```php
$userInput = "what\"><script>console.log('nope')</script><div ";
echo new Element("div", ["class" => $userInput], "Content")); // "<div class="what%22%3E%3Cscript%3Econsole.log('nope')%3C%2Fscript%3E%3Cdiv%20">Content</div>"
```

And this library wasn't made to be complicated, here's an oversimplification of how the element's content is made on the `Element` class:

```php
class Element {
    function __toString() {
        return "<" . $this->$tag . ">" . escapeHTML($this->$content) . "</" . $this->$tag . ">";
    }
}
```

You can probably read and understand this library in 15 minutes.

## Problems you can solve using this

Transforming data in PHP to HTML is difficult because html is loosly validated:

```php
<?php
    $price = $data["price"];
?>
<div class="price-box">
    <span class="label">Price:</span>
    <span class="value"><?php echo $price; ?></spen>
</div>
```

You think your browser will let you know that you mispelled 'span' on the second closing tag? Nope, welcome to the web, browser just silently goes on.

You can take advantage of PHP interpreter to tell you if something is incorrect with syntax errors.

The idea is to declare in composition-based components all the visual logic, with the data that it needs to render fully.

React developers have been doing this in javascript, its tremendously useful and the same coding rules apply. Sadly implementing JSX is a huge chore and I don't think PHPX would catch on.

### Managing data

Data has to be passed around in a top-down fashion:

```php
function Post($index, $title, $text) {
    return new Element(
        "article",
        ["class"="article-".$index],
        new Element("h2", [], $title),
        new Element("p", [], $text),
    );
}

function PostList($postData) {
    $postList = [];
    $index = 0;
    foreach ($postsData as $name => $post) {
        $index++;
        $postList[] = new Post($index, $name, $post);
    }
    return new Element("div", ["class" => "post-list"], $postList);
}

$postsData = [
    "My life as a developer" => "It was fun",
    "I'm declaring" => "data like normal",
    "And html elements treat" => "and using it easily"
];

echo PostList($postData); // Renders pure html (but it may also throw if rendering fails)
```

Obviously, you're left alone regarding how and where you get data. But be advised: **All security rules still apply and you must [strip your tags](http://php.net/manual/pt_BR/function.strip-tags.php) from your data!**.

## Instalation

But you should use `composer` to install the project from the command line, at the folder of your project:

```
composer require rossato/declarative-php
```

Just don't forget to require composer's autoload on your entry point:

```php
require "vendor/autoload.php";
```

And at the top of every file you need to use the Element class, you write this:

```
use Rossato\Element;
```

Now everytime you write `Element` you'll be referring to this project's Element.

Alternatively, you can download this repository and require the files inside `/src/` manually, no credits required.

## Automatic Compressing of Styles in Elements

Alright, there's just ONE piece of magic in this library: they `style` property of html elements are heavily featured in two ways:

1. If you put in an object on the property, it becomes the string representation of that object:

```php
$style = [
    "width" => 200,
    "height" => 200,
    "background-color" => "red"
];
echo new Element("div", ["style" => $style]);;
```

```html
<div style="width:200px,height:200px,background-color:red"/>
```

2. But if you put a raw string, then we minimize it with a simplistic function to save a few bytes every now and then:

``` your components, so I put a simple (7-line function) and automatic way of compressing style properties:

```php
echo new Element("div", ["style" => "/* Text color comment */ color : red; background : blue; "]);
```

```html
<div style="color:red;background:blue"/>
```

CSS is such a simple language that there is hopefully no downsite on minifying it.

## Javascript

You can tell this library to f\*ck off regarding it html escaping:

```
$script = new Element("script", [], "console.log('hi <div></div>');");

echo $script; // '<script>console.log('hi &gt;div&lt;&gt;div&lt');</script>"

$script->isRawHTML = true;

echo $script; // '<script>console.log('hi <div></div>');</script>"
```

Obviously there isn't any javascript minification going on because that would require quite a lot of code.

## Usage advice

I don't think you should write your whole web app with this tool (but if you do, let me know), only in situations where your data critically changes your output structure.

For example, when you fetch your own application for a list of items, you could use this to easily compose the list and return it as a HTML that you can just "plug" into your frontend easily, you could even detect if the user is logged in or not and show an appropriate message, or if something went wrong with the data retrieval, or whatever. All your javascript has to care about is fetching.

```javascript
async function requestData() {
    const element = document.querySelector(".content");
    document.querySelector(".content").innerHTML = "loading...";
    const response = await fetch("/api/endpoint/");
    const html = await response.text();
    document.querySelector(".content").innerHTML = content;
}

requestData();
```

And at the endpoint you could just handle that

```php
<?php
// "./api/endpoint/index.php"
require "../vendor/autoload.php";

use Rossato/Element;
use Something/Database;

Database::connect();

function EndpointResponse($connected, $user) {
	if (!$connected) {
        return "Database could not be reached.";
	}
    if (!$user) {
        return "Log in please.";
    }
	return new Element("div", ["class" => "profile-name"], "Hello, " . $user->$name);
}

$connected = Database::connect();
$user = Database::getUser();

return EndpointResponse($connected, $user);
```

## Testing and Balancing

The idea about the declarative programming style is that you can render different things based on arbitrary conditions:

```php
if ($_SERVER['REMOTE_ADDR'] === "129.129.129.129") {
    echo new ThatNewFeatureComponent();
} else {
    echo new ProductionComponent();
}
```

Or, you could setup caching and serve the cache to end users and bypass it for developers (but keeping the cache files intact).

## Performance

Since we're developing the HTML structure in the backend, the server CPU usage will be increased, I think most servers have low CPU usage and high memory latency problems, like database calls and file readings.

Every request re-renders the entire structure, so caching the result (memoing) is advised. Guess how the you do that:

```php
function saveCache($content) {
	file_put_contents($cacheFilename, strval($content));
}
function loadCache() {
	return file_get_contents("./cache.html");
}
function cacheExists() {
	return file_exists("./cache.html");
}
function generate() {
    return App();
}

if (cacheExists()) {
	echo loadCache();
} else {
	$content = generate();
	saveCache($content);
	echo $content;
}
```php

# Licence

You are free to use this and do what you want with it, like changing, redistributing, even claiming as your own, just don't annoy me as I provide no warranty or anything.
