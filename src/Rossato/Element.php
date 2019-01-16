<?php

namespace Rossato;

/**
 *	A class used to represent an HTML element
 */

class Element {
	public function __construct($tag, $config = null, $content = null) {
		$this->tag = strtolower($tag);
		$this->config = $config ? $config : [];
		$this->content = $content ? $content : null;
	}

	public function add($property, $value = null) {
		if ($property instanceof HTMLElement && $value == null) {
			if ($this->isVoidElement($this->tag)) {
				throw new \Exception("Cannot add an element to a void element (".$this->tag.")");
			}
			$object = $property;
			if (is_array($this->content)) {
				array_push($this->content, $object);
			} else {
				$this->content = ($this->content)?[$this->content, $object]:[$object];
			}
		} else if (is_string($property)) {
			$this->config[$property] = $value;
		} else {
			throw new \Exception("Invalid property to 'add' method of HTML '".$this->tag."' element");
		}
		return $this;
	}

	public function content($content) {
		$this->content = $content;
	}

	/**
	 * Determines if a given tag is a void (self-closing) element.
	 * @param $tag  The lowercase tag name of the element
	 */
	public function isVoidElement($tag) {
		return (in_array($tag, ["img", "input", "br", "wbr", "hr", "embed"]));
	}


	/**
	 * Converts an inner style to its minified form, removing unnecessary spaces and newlines.
	 * @param $css  The script without brackets to be stripped of commentary and whitespaces.
	 */
	public function minifyStyle($css) {
		$css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css);
		$css = preg_replace('/\s{2,}/', ' ', $css);
		$css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
		$css = str_replace(": ", ":", $css);
		$css = substr($css, -1)===";"?substr($css, 0, strlen($css)-1):$css;
		return $css;
	}

	/**
	 * Composes the beggining of an element (or it fully, if it's a void element)
	 * @return string  The start of the component
	 */
	protected function getHeader() {
		$content = "<".$this->tag;
		if (count($this->config)) {
			foreach ($this->config as $key=>$value) {
				if ($key === "style") {
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
	 * @return string  The compressed HTML code that represents the object and its content
	 */
	public function flatten() {
		$content = $this->getHeader();

		// Check if it's a void element quickly
		if (substr($content, -2) === "/>") {
			return $content;
		}

		foreach ((is_array($this->content) ? $this->content : [$this->content]) as $part) {
			$content .= ($part instanceof HTMLElement)?$part->flatten():$part;
		}

		$content .= "</".$this->tag.">";

		return $content;
	}

	/**
	 * Transform a minus separated string to camel case
	 * @param  string $str  The string to be converted  Ex: "something-something"
	 * @return string       The converted string        Ex: "somethingSomething"
	 */
	public function minusSeparatedToCamelCase($str) {
		$ret = "";
		$nextIsCapital = false;
		for ($i=0; $i<strlen($str); $i+=1) {
			$letter = $str[$i];
			if ($letter == '-') {
				$nextIsCapital = true;
			} else if ($nextIsCapital && $letter > 'a' && $letter < 'z') {
				$ret .= strtoupper($letter);
				$nextIsCapital = false;
			} else if ($letter !== ' ' && $letter !== "\t" && $letter !== "\n") {
				$ret .= $letter;
			}
		}
		if (trim($ret) != $ret) {
			Log::info("Something went wrong with '".$str."'");
		}
		return $ret;
	}

	public function __toString() {
		return $this->flatten();
	}

	/**
	 * Tries to find a sub-element through query selection.
	 * @param  string $query   The query, like ".list" or "#root"
	 * @return Element         Returns the element or false if it wasn't found
	*/
	public function querySelector($query) {
		$result = [];

		// Handle multi-queries
		if (strpos($query, ",") !== false) {
			foreach (explode(",", $query) as $subQuery) {
				foreach ($this->querySelector($subQuery) as $subResults) {
					foreach ($subResults as $singleSubResult) {
						array_push($result, $singleSubResult);
					}
				}
			}
			return $result;
		}

		// clean extra spaces
		$query = trim($query);

		if (strpos($query, "[") !== false) {
			throw new \Exception("Selecting by property is currently not supported!");
		}

		if ($query[0] == ".") {
			$prefixSearch = "class";
		} else if ($query[0] == "#") {
			$prefixSearch = "id";
		} else {
			$prefixSearch = "tag";
		}

		foreach ((is_array($this->content) ? $this->content : [$this->content]) as $part) {
			if ($part instanceof HTMLElement) {
				if ($prefixSearch == "class" && array_key_exists("class", $part->config)) {
					if ($part->config["class"] == substr($query, 1)) {
						array_push($result, $part);
					}
				} else if ($prefixSearch == "id" && array_key_exists("id", $part->config)) {
					if ($part->config["id"] == substr($query, 1)) {
						array_push($result, $part);
					}
				} else if ($prefixSearch == "tag" && property_exists($part, "tag")) {
					if ($part->tag == $query) {
						array_push($result, $part);
					}
				}
			}
		}

		return $result;
	}
}
