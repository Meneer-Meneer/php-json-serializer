<?php
class JsonSerializer extends Serializer {
    public function __construct() {

    }

    public function Serialize($poObject) {
        return json_encode(parent::DoSerialize($poObject));
    }

    public function Deserialize($psJson, $psType) {
        return parent::DoDeserialize(json_decode($psJson, true), $psType);
    }
}