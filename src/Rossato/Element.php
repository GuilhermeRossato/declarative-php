<?php

namespace Rossato;

/**
 *	A class used to represent an HTML element
 */

class Element {

	/**
	 * Creates a representation of an HTML element
	 * @param string $tag  		The tag as string of the HTML element, ex: "div"
	 * @param array $config 	The associative array describing the element, ex: ["class" => "button"]
	 * @param mixed $mixed 		The content inside the element (string|Element instance|array)
	 * @param mixed $...    	Other parameters are also added as content
	 */
	public function __construct($tag, $config = null, $mixed = null) {
		// Class guards
		if (!is_string($tag) || strlen($tag) === 0) {
			throw new \InvalidArgumentException("First parameter (tag) must be a non-empty string");
		}
		if (strpos(trim($tag), " ") !== false) {
			throw new \InvalidArgumentException("First parameter (tag) cannot contain spaces");
		}
		if (!preg_match("/^[a-z\-0-9]+$/i", trim($tag))) {
			throw new \InvalidArgumentException(
				"First parameter (tag) can only contain alfanumeric characters [A-Za-z0-9] and dashes (-)"
			);
		}
		if ($config !== null && !is_array($config)) {
			throw new \InvalidArgumentException(
				"Invalid config parameter: Second parameter must be an associative array or null"
			);
		}
		$this->tag = strtolower(trim($tag));
		$this->config = [];
		if ($config !== null) {
			foreach ($config as $key => $value) {
				$this->setAttribute($key, $value);
			}
		}
		if ($mixed !== null) {
			$args = func_get_args();
			$this->add(array_slice($args, 2));
		}
	}

	/**
	 * Adds a single element to its internal content array.
	 *
	 * @param mixed $content An object, string or array to be added to the content array
	 */
	private function addSingleContent($content) {
		$content = is_bool($content) ? ($content ? "true" : "false") : $content;

		if (!property_exists($this, 'content')) {
			$this->content = $content;
		} elseif (is_array($this->content)) {
			array_push($this->content, $content);
		} else {
			$this->content = ($this->content !== null) ? [$this->content, $content] : [$content];
		}
	}

	/**
	 * Adds sub-elements or strings to the current element (multiple-parameter)
	 *
	 * @param mixed @objects   An array or multiple parameters to be added to the element
	 * @param mixed @any       Objects can be arrays, string or other Element instances
	 *
	 * @return Element         The element itself ($this)
	 */
	public function add() {
		if ($this->isVoidElement($this->tag)) {
			throw new \InvalidArgumentException("Cannot add an element to a void element (".$this->tag.")");
		}
		$parameters = func_get_args();
		foreach ($parameters as $content) {
			if (is_array($content)) {
				// Call itself with the array flatened
				call_user_func_array([$this, "add"], $content);
			} elseif ($content instanceof Element ||
				is_string($content) ||
				is_numeric($content) ||
				is_bool($content)
			) {
				$this->addSingleContent($content);
			} else {
				throw new \InvalidArgumentException(
					"Invalid object of type '".gettype($content)."' to add to '".$this->tag."' element: "
				);
			}
		}
		return $this;
	}

	/**
	 * Sets a property in the element's configuration
	 *
	 * @param string $attribute          The attribute to be set
	 * @param mixed $value               The value to be set
	 *
	 * @return mixed                     The value written to the attribute
	 *
	 * @throws InvalidArgumentException  When the attribute parameter is not a string
	 */
	public function setAttribute($attribute, $value = null) {
		if (is_string($attribute)) {
			$attribute = strtolower(trim($attribute));
			return ($this->config[$attribute] = $value);
		} else {
			throw new \InvalidArgumentException("Config attribute must be a string");
		}
	}

	/**
	 * Sets a property in the element's configuration (alias for setAttribute)
	 *
	 * @param string $attribute          The attribute to be set
	 * @param mixed $value               The value to be set
	 *
	 * @return mixed                     The value written to the attribute
	 *
	 * @throws InvalidArgumentException  When the attribute parameter is not a string
	 */
	public function addAttribute($attribute, $value = null) {
		return $this->setAttribute($attribute, $value);
	}

	/**
	 * Retrievies a property in the element's configuration, without throwing any error if it does not exist
	 *
	 * @param  string $attribute  The attribute to be retrieved
	 *
	 * @return mixed              The value of the attribute as it is defined or null if it does not exists
	 */
	public function getAttribute($attribute) {
		return array_key_exists($attribute, $this->config) ? $this->config[$attribute] : null;
	}

	/**
	 * Determines if a given tag is a void (self-closing) element.
	 *
	 * @param $tag  The lowercase tag name of the element
	 */
	public function isVoidElement($tag) {
		return (in_array(
			$tag,
			["img", "input", "br", "wbr", "hr", "embed", "meta", "link", "col", "area", "base", "rect"]
		));
	}


	/**
	 * Converts an inner style to its minified form, removing unnecessary spaces and newlines.
	 *
	 * @param $css  The script without brackets to be stripped of commentary and whitespaces.
	 */
	public function minifyStyle($css) {
		$css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css);
		$css = preg_replace('/\s{2,}/', ' ', $css);
		$css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
		$css = str_replace(": ", ":", $css);
		$css = substr($css, -1) === ";" ? substr($css, 0, strlen($css)-1) : $css;
		return trim($css);
	}

	/**
	 * Composes the beggining of an element (or it fully, if it's a void element)
	 *
	 * @return string  The start of the component
	 */
	protected function getHeader() {
		$content = "<".$this->tag;
		if (count($this->config)) {
			foreach ($this->config as $key => $value) {
				if ($value === false) {
					continue;
				} elseif ($value === true) {
					$content .= ' '.$key;
					continue;
				} elseif ($key === "style") {
					$value = $this->minifyStyle($value);
				}
				$content .= ' '.$key.'="'.htmlspecialchars($value).'"';
			}
		}
		$content .= ($this->isVoidElement($this->tag))?" />":">";
		return $content;
	}

	/**
	 * Transform the element and its content into raw HTML code
	 *
	 * @return string  The compressed HTML code that represents the object and its content
	 */
	public function flatten() {
		$content = $this->getHeader();

		// Check if it's a void element to exit early
		if (substr($content, -2) === "/>") {
			return $content;
		}

		foreach ((is_array($this->content) ? $this->content : [$this->content]) as $part) {
			$content .= ($part instanceof Element)?$part->flatten():$part;
		}

		$content .= "</".$this->tag.">";

		return $content;
	}

	/**
	 * Transform the element and its content into raw html code (alias for the flatten method).
	 *
	 * @return string 	The compressed HTML code that represents the object and its content
	 */
	public function __toString() {
		return $this->flatten();
	}

	/**
	 * Transform the element and its content into raw html code, (alias for the flatten method).
	 *
	 * @return string 	The compressed HTML code that represents the object and its content
	 */
	public function render() {
		return $this->flatten();
	}

	/**
	 * Tries to find all sub-element that matches a query selection.
	 *
	 * @param  string $query   The query, like ".list" or "#root"
	 * @return array           Returns the array of Element classes that matched or an empty array if nothing was found
	*/
	public function querySelectorAll($query) {
		$results = [];

		// Handle multi-queries
		if (strpos($query, ",") !== false) {
			foreach (explode(",", $query) as $subQuery) {
				foreach ($this->querySelectorAll($subQuery) as $subResult) {
					array_push($results, $subResult);
				}
			}
			return $results;
		}

		// clean extra spaces
		$query = trim($query);

		if (strpos($query, "[") !== false) {
			throw new \InvalidArgumentException("Selecting by property is currently not supported!");
		}

		if ($query[0] === ".") {
			$prefixSearch = "class";
		} elseif ($query[0] === "#") {
			$prefixSearch = "id";
		} elseif ($query === "*") {
			$prefixSearch = "all";
		} else {
			$prefixSearch = "tag";
		}

		foreach ((is_array($this->content) ? $this->content : [$this->content]) as $part) {
			if ($part instanceof Element) {
				if ($prefixSearch === "all") {
					array_push($results, $part);
				} if ($prefixSearch == "class" && array_key_exists("class", $part->config)) {
					if ($part->config["class"] === substr($query, 1)) {
						array_push($results, $part);
					}
				} elseif ($prefixSearch === "id" && array_key_exists("id", $part->config)) {
					if ($part->config["id"] === substr($query, 1)) {
						array_push($results, $part);
					}
				} elseif ($prefixSearch === "tag" && property_exists($part, "tag")) {
					if ($part->tag === $query) {
						array_push($results, $part);
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Tries to find the first sub-element that matches a query selection.
	 * @param  string $query   The query, like ".list" or "#root"
	 * @return Element         Returns the element or null if nothing was found
	*/
	public function querySelector($query) {
		$list = $this->querySelectorAll($query);
		if ($list && count($list) > 0) {
			return $list[0];
		}
		return null;
	}
}
