<?php

use PHPUnit\Framework\TestCase;
use Cajudev\Classes\Strings;

class StringsTest extends TestCase
{
    public function setUp(): void
    {
        $this->strings = new Strings('Lorem ipsum dolor sit amet, consectetur adipiscing elit');
    }

    public function test_replace()
    {
        $this->strings->replace(' ', '');
        $expect = 'Loremipsumdolorsitamet,consecteturadipiscingelit';
        self::assertEquals($expect, $this->strings); //comparing the object
    }

    public function test_xreplace()
    {
        $this->strings->xreplace('/\s/', '');
        $expect = 'Loremipsumdolorsitamet,consecteturadipiscingelit';
        self::assertEquals($expect, $this->strings->get()); //comparing the string
    }

    public function test_split()
    {
        $arrays = $this->strings->split(" ");
        $expect = ['Lorem', 'ipsum', 'dolor', 'sit', 'amet,', 'consectetur', 'adipiscing', 'elit'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_xsplit()
    {
        $this->strings->set('Lorem ipsum dolor sit amet');
        $arrays = $this->strings->xsplit("/\s/");
        $expect = ['Lorem', 'ipsum', 'dolor', 'sit', 'amet'];
        self::assertEquals($expect, $arrays->get());
    }

    public function test_lower()
    {
        $this->strings->lower();
        $expect = 'lorem ipsum dolor sit amet, consectetur adipiscing elit';
        self::assertEquals($expect, $this->strings->get());
    }

    public function test_upper()
    {
        $this->strings->upper();
        $expect = 'LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISCING ELIT';
        self::assertEquals($expect, $this->strings->get());
    }
}