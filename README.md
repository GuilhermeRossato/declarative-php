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

Elements can be combined to create more complex structures:

```php
use Rossato\Element;

echo new Element(
    "ul",
    ["style" => "margin: 10px"],
    new Element("li", null, "First element"),
    new Element("li", null, "Second element")
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
    public function __construct($label) {
        $button = new Element(
            "input",
            [
                "type" => "submit", 
                "value" => $label
            ]
        );

        parent::__construct(
            "form",
            ["action" => "endpoint.php"],
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

echo $app->render();
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

## Inspiration

PHP developers started using robust frameworks and abstracting things like database access, business logic, etc, however no matter how much we distant ourselves with Request objects, Route classes or Delegators, we have to reply to the request with pure html (unless you use React or another front-end tool).

However, transforming data into HTML is a pain, take a look:

```php
<?php
    $price = $data["price"];
?>
<div class="price-box">
    <span class="label">Price:</span>
    <span class="value"><?php echo $price; ?></spen>
</div>
```

Spot the bug? That one was easy, even your text editor could've picked that one up.

To help you mitigate this kind of problem, however, you can abstract it further:

```php

class PriceBox {
    public function __construct($price) {
        $this->price = $price;
    }
    public function __toString() {
        return '<div class="price-box>'.
                '<span class="label">Price: </span>'.
                '<span class="value>'.$this->price.'</span>'.
                '</span>';
    }
}

$price = $data["price"];
$priceBox = new PriceBox($price);
```

We're missing a quote there, are we not?

Humans are not compilers, you don't need that much attention to detail to program. Leave PHP to tell you when something is wrong with syntax errors.

If you solve the data template problem, you'd still have difficulty expressing when small data changes cause a lot of structural html changes, that's why huge frameworks are necessary to get them right.

Ultimately, people go to GraphQL or sending data as JSON so that a frontend tool like React can process, transform and display it.

You can define how to display something based on data in PHP, you just have to structure your code as a declarative, composition-based system with sub-components that treat your data however it needs to be treated, conditionally or not, in way that is easy to test.

React developers have been doing this in javascript, same rules apply (except JSX, which would be nice but I can't get around to writing a parser, besides PHPX wouldn't catch on)

### Managing data

Data has to be passed around in a top-down fashion:

```php
class Post extends Element {
    public function __construct($index, $title, $text) {
        parent::__construct(
            "article",
            ["class"="article-".$index],
            new Element("h2", [], $title),
            new Element("p", [], $text),
        );
    }
}

class PostList extends Element {
    public function __construct($postData) {
        $postList = [];
        $index = 0;
        foreach ($postsData as $name=>$post) {
            $index++;
            $postList[] = new Post($index, $name, $post);
        }
        parent::__construct("div", ["class" => "post-list"], $postList);
    }
}

$postsData = [
    "My life as a developer" => "It was fun",
    "I'm declaring" => "data like normal",
    "And html elements treat" => "and using it easily"
];

echo new PostList($postData); // Renders pure html!
```

Obviously, you're left alone regarding how and where you get data. But be advised: **All security rules still apply and you must [strip your tags](http://php.net/manual/pt_BR/function.strip-tags.php) from your data!**.

## Instalation

Add this framework to your project using composer:

```
composer require rossato/declarative-php
```

and then require composer's autoload in your files and use the library freely.

```php
require "vendor/autoload.php";

use Rossato\Element;
```

Alternatively, you can download this repository and require the files inside `/src/` manually.

## Automatic Compressing of Styles in Elements

Sometimes, you will need to put styles into your components, so I put a simple (7-line function) and automatic way of compressing style properties:

```php
echo new Element("div", ["style" => "/* Text color comment */ color : red; background : blue; "]);
```

```html
<div style="color:red;background:blue"></div>
```

Javascript isn't so easy to compress, so I left it alone.

## Usage advice

You don't have to necessarily write your whole web application with this tool (but if you do, let me know), only in situations where your data critically changes your output structure.

For example, when you fetch your own application for a list of items, you could use this to easily compose the list and return it as a HTML that you can just "plug" into your frontend easily, you could even detect if the user is logged in or not and show an appropriate message, or if something went wrong with the data retrieval, or whatever. All your javascript has to care about is fetching.

```javascript
function receivedData(content) {
    document.querySelector(".content").innerHTML = content;
}

function requestData() {
    document.querySelector(".content").innerHTML = "loading...";
    fetch("/api/endpoint/").then(r=>r.text()).then(receivedData)
}

requestData();
```

And at the endpoint:

```php
<?php

require ".../vendor/autoload.php";

use Rossato/Element;
use Something/DataModel;
use Something/Session;
use Something/Log;

class EndpointResponse extends Element {
    public function __construct($element) {
        try {
            $data = new DataModel();
        } catch (Exception $err) {
            Log::error($err);
            $content = new Element("div", ["class" => "error"], "Fetching failed, check logs!");
            exit(1);
        }
        $content = new MyDataList($data);
        parent::__construct(
            "div",
            ["class" => "result"],
            new MyDataList($data)
        );
    }
}

class MyDataList extends Element {
    public function __construct($data) {
        $content = new Element("div", ["class" => "data"]);
        if (count($data) === 0) {
            $content->add("List is empty!");
        } else {
            foreach ($data as $text) {
                $content->add(new Element("div", [], $text));
            }
        }
        parent::__construct(
            "div",
            ["class" => "data-wrapper"],
            $content
        );
    }
}

// This could be abstracted in a middleware
if (!Session::getUser()) {
    return new Element("div", ["class" => "error"], "You are not logged in!");
} else {
    return new EndpointResponse();
}
```

If you were to return the data to the frontend, you'd need to write javascript functions to treat the result when it was empty or when the user lost the session, or when there was a database problem, etc.

## Testing and Balancing

Want to test your changes in production? Disgusting. But we have you covered:

```php
if ($_SERVER['REMOTE_ADDR'] === "129.129.129.129") {
    // Serve a copy of app but with some differences that can be migrated to the app class
    echo new TestApp();
} else {
    echo new App();
}
```

Or, you could setup caching (discussed at the following section) and serve the cache to end users and bypass it for developers (but keeping the cache files intact).

## Performance

Since we're developing the HTML structure in the backend, the server CPU usage will increase, but most servers have very low CPU usage (at least the ones I work with) because of database calls and file readings.

This project has the same drawbacks of react except there's no virtual DOM so it will be a bit slower since we have to process the entire tree at each request.

Thanksfully we can have html caching for content that we can cache! The basic idea is:

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

Ideas to deal with caching:

1. Have a top class to handle cache and results.
2. Have a composable class that hold cached results of other generic sub-components.
3. Have a clear cache method on your app that automatically creates the caches back, so that no end-user ever hits a "slow" processing page.

# Real world example

Yet to be developed. But on the way!

# Licence

I do not provide any type of warranty from this or any code I write.
