<?php
abstract class Serializer {
    public function __construct() {}

    protected function DoSerialize($poObject, &$paObjectList = null) {
        //If the object list hasn't been initialized yet, create an empty array
        if (is_null($paObjectList)) {
            $paObjectList = [];
        }

        if (is_object($poObject)) {
            //If the object hasn't already been serialized, serialize it and return it
            if (!in_array($poObject, $paObjectList)) {
                $lnObjectId = count($paObjectList) + 1;
                $paObjectList[$lnObjectId] = $poObject;

                $laObject = [];
                $laObject["\$id"] = $lnObjectId;

                $loReflectionObject = new \ReflectionObject($poObject);

                //Loop through all public properties of the object
                foreach ($loReflectionObject->getProperties(\ReflectionProperty::IS_PUBLIC) as $loReflectionProperty) {
                    $laObject[$loReflectionProperty->getName()] = $this->DoSerialize($poObject->{$loReflectionProperty->getName()}, $paObjectList);
                }

                return $laObject;
            }
            else { //If the object has been serialized, return its $id
                return ["\$ref" => array_search($poObject, $paObjectList, true)];
            }
        }

        if (is_array($poObject)) {
            $laArray = [];

            foreach ($poObject as $loKey => $loValue) {
                $laArray[$loKey] = $this->DoSerialize($loValue, $paObjectList);
            }

            return $laArray;
        }

        if (is_scalar($poObject)) {
            return $poObject;
        }

        throw new \Exception("Trying to serialize invalid type.");
    }

    protected function DoDeserialize($poObject, $psType, &$paObjectList = null) {
        //If the object list hasn't been initialized yet, create an empty array
        if (is_null($paObjectList)) {
            $paObjectList = [];
        }

        //If the object is a scalar type or null, return it without doing anything
        if (in_array(strtolower($psType), ["bool", "int", "float", "string", "null"])) {
            return $poObject;
        }

        //If this is a reference to another object, return the actual object from the object list
        if (array_key_exists("\$ref", $poObject)) {
            return $paObjectList[$poObject["\$ref"]];
        }

        //Create a new object of the passed type
        $loObject = new $psType();

        //Store the object in the object list under its id
        $paObjectList[$poObject["\$id"]] = $loObject;

        $loReflectionObject = new \ReflectionObject($loObject);

        //Loop through all public properties of the object
        foreach ($loReflectionObject->getProperties(\ReflectionProperty::IS_PUBLIC) as $loReflectionProperty) {
            //If the property doesn't exist in the original data
            if (!isset($poObject[$loReflectionProperty->getName()])) {
                continue;
            }

            //If the property doesn't have a doc comment
            if (is_null($loReflectionProperty->getDocComment())) {
                throw new \Exception("Property \"" . $loReflectionProperty->getName() . "\" of class \"" . $loReflectionProperty->getDeclaringClass()->getName() . "\" does not have a specified type.");
            }

            $laMatchList = [];

            //If the property's doc comment's format isn't recognized
            if (preg_match('/\*\s*@var\s([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(?:\[\])?)\s+(.*)\s?\*/', $loReflectionProperty->getDocComment(), $laMatchList) !== 1) {
                throw new \Exception("Property \"" . $loReflectionProperty->getName() . "\" of class \"" . $loReflectionProperty->getDeclaringClass()->getName() . "\" has an unrecognized docblock comment format.");
            }

            //If the property is not an array
            if (strpos($laMatchList[1], "[]") === false) {
                $loObject->{$loReflectionProperty->getName()} = $this->DoDeserialize($poObject[$loReflectionProperty->getName()], $laMatchList[1], $paObjectList);
            }
            else { //If the property is an array
                $laArray = [];

                //Loop through all key value pairs in the array
                foreach ($poObject[$loReflectionProperty->getName()] as $loKey => $loValue) {
                    $laArray[$loKey] = $this->DoDeserialize($loValue, str_replace("[]", "", $laMatchList[1]), $paObjectList);
                }

                $loObject->{$loReflectionProperty->getName()} = $laArray;
            }
        }

        return $loObject;
    }
}