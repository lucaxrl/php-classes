<?php

namespace Cajudev\Classes;

use Cajudev\Classes\Arrays;
use Cajudev\Classes\Regexs;

class Strings
{
    private $content;

    public function __construct(string $string = '')
    {
        $this->content = $string;
    }

    /**
     * Perform a basic text replacement
     * 
     * @param  string $search
     * @param  string $replace
     * @param  int    $count
     *
     * @return self
     */
    public function replace(string $search, string $replace, int &$count = null): self
    {
        $this->content = str_replace($search, $replace, $this->content, $count);
        return $this;
    }

    /**
     * Perform a advanced text replacement using regular expressions
     *
     * @param  string $pattern
     * @param  string $replacement
     * @param  int    $limit
     * @param  int    $count
     *
     * @return self
     */
    public function xreplace(string $pattern, string $replacement, int $limit = -1, int &$count = null): self
    {
        $this->content = preg_replace($pattern, $replacement, $this->content, $limit, $count);
        return $this;
    }

    /**
     * Divide the string by a string
     *
     * @param  string $delimiter
     * @param  int    $limit
     *
     * @return Arrays
     */
    public function split(string $delimiter, int $limit = PHP_INT_MAX): Arrays
    {
        return new Arrays(explode($delimiter, $this->content, $limit));
    }

    /**
     * Divide the string by a regular expression
     *
     * @param  string $pattern
     * @param  int    $limit
     * @param  int    $flags
     *
     * @return Arrays
     */
    public function xsplit(string $pattern, int $limit = -1, int $flags = 0): Arrays
    {
        return new Arrays(preg_split($pattern, $this->content, $limit, $flags));
    }

    public function set(string $string): self
    {
        $this->content = $string;
        return $this;
    }
    
    public function get(): string
    {
		return $this->content;
	}
    
    public function __toString()
    {
        return $this->content;
    }
}