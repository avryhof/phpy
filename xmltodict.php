<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . "stdlib.php");

class xmltodict {
    var $xml = null;
    var $xml_object = null;
    var $xml_json = null;
    var $parsed_xml = array();
    var $return_dict = null;

    function __construct($xml=null)
    {
        $this->xml = $xml;
    }

    function simplexml_to_array($simplexml_object) {
        $xml_json = json_encode($simplexml_object);
        $parsed_xml = json_decode($xml_json, true);

        return $parsed_xml;
    }

    function parse_array($items) {
        $return_array = [];

        foreach($items as $key => $value) {
            if (gettype($value) == "array") {
                $return_array[$key] = new dict($this->parse_array($value));
            } else {
                $return_array[$key] = $value;
            }
        }
        return $return_array;
    }

    function parse($xml=false) {
        if ($xml) {
            $this->xml = $xml;
        }

        $this->xml_object = simplexml_load_string($this->xml, "SimpleXMLElement", LIBXML_NOCDATA);

        echo "<pre>"; var_dump($this->xml_object); echo "</pre>";

        $this->xml_json = json_encode($this->xml_object);
        $this->parsed_xml = json_decode($this->xml_json, true);
        $this->return_dict = new dict($this->parsed_xml);

        return new dict($this->parse_array($this->return_dict->items()));
    }
}