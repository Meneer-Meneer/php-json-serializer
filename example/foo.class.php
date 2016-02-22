<?php
class Foo {
    /** @var string */
    public $Name;
    /** @var Bar */
    public $Bar;
    /** @var Bar[] */
    public $BarList;

    public function __construct() {
        $this->BarList = [];
    }
}