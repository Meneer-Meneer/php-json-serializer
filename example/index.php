<?php
ini_set("display_startup_errors", 1);
ini_set("display_errors", 1);
error_reporting(-1);

require_once("../classes/serializer.class.php");
require_once("../classes/jsonserializer.class.php");
require_once("../classes/xmlserializer.class.php");
require_once("foo.class.php");
require_once("bar.class.php");

$loJsonSerializer = new JsonSerializer();

$loFoo1 = new Foo();
$loFoo2 = new Foo();
$loFoo3 = new Foo();

$loBar1 = new Bar();
$loBar2 = new Bar();
$loBar3 = new Bar();

$loFoo1->Name = "Foo1";
$loFoo2->Name = "Foo2";
$loFoo3->Name = "Foo3";

$loBar1->Name = "Bar1";
$loBar2->Name = "Bar2";
$loBar3->Name = "Bar3";

$loBar1->Foo = $loFoo1;
$loBar2->Foo = $loFoo2;
$loBar3->Foo = $loFoo3;

$loFoo1->Bar = $loBar1;
$loFoo2->Bar = $loBar2;
$loFoo3->Bar = $loBar3;

$loBar1->FooList[] = $loFoo1;
$loBar1->FooList[] = $loFoo2;
$loBar1->FooList[] = $loFoo3;

$loBar2->FooList[] = $loFoo1;
$loBar2->FooList[] = $loFoo2;
$loBar2->FooList[] = $loFoo3;

$loBar3->FooList[] = $loFoo1;
$loBar3->FooList[] = $loFoo2;
$loBar3->FooList[] = $loFoo3;

$loFoo1->BarList["Bar1"] = $loBar1;
$loFoo1->BarList["Bar2"] = $loBar2;
$loFoo1->BarList["Bar3"] = $loBar3;

$loFoo2->BarList[] = $loBar1;
$loFoo2->BarList[] = $loBar2;
$loFoo2->BarList[] = $loBar3;

$loFoo3->BarList[] = $loBar1;
$loFoo3->BarList[] = $loBar2;
$loFoo3->BarList[] = $loBar3;

$lsEncodedFoo1 = $loJsonSerializer->Serialize($loFoo1);
$loDecodedFoo1 = $loJsonSerializer->Deserialize($lsEncodedFoo1, "Foo");

echo $lsEncodedFoo1;
var_dump($loDecodedFoo1);