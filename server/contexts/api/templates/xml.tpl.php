<?php
// function defination to convert array to xml
function array_to_xml( $data, &$xml_data,$node=false) {
    foreach( $data as $key => $value ) {
        if(trim($key) == '') {
            $key = 'item_';
        }
        if((is_numeric($key) || is_numeric($key[0])) && !is_numeric($node)){
            if($node){
                $key = $node;
            } else {
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
        } else {
            $key = str_replace([" ","/","\\","-"],"_",$key);
        }

        if( is_array($value) ) {
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode,$key);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
     }
}
$xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

// function call to convert array to xml
array_to_xml(\utils\Tools::object2array($this->data),$xml_data);

echo $xml_data->asXML(); die();