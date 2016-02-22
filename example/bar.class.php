<?php
class Bar {
    /** @var string */
    public $Name;
    /** @var Foo */
    public $Foo;
    /** @var Foo[] */
    public $FooList;

    public function __construct() {
        $this->FooList = [];
    }
}