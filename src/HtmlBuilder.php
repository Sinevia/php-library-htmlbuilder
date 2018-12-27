<?php

namespace Sinevia;

class HtmlBuilder {
    private $array = [];
    
    public function setJson($jsonString) {
        $this->array = json_decode($jsonString);
        return $this;
    }

    public function getArray() {
        return $this->array;
    }

    public function setArray($array) {
        $this->array = $array;
        return $this;
    }

    function createElement($doc, $parent, $tag) {
        // 1. Data
        $tagName = $tag[0];
        $tagAttributes = $tag[1];
        $tagChildren = $tag[2];
        
        // 2. Create nde
        $node = $doc->createElement($tagName);
        
        // 3. Append to parent or document
        if (is_null($parent)) {
            $doc->appendChild($node);
        } else {
            $parent->appendChild($node);
        }
        
        // 4. Append attributes
        foreach ($tagAttributes as $key => $value) {
            $node->setAttribute($key, $value);
        }
        
        // 5. Append children
        if (is_array($tagChildren)) {
            foreach ($tagChildren as $child) {
                // If child is array create element out of it
                if (is_array($child)) {
                    $this->createElement($doc, $node, $child);
                }
                // Strings and numerics add as value
                if (is_string($child) OR is_numeric($child)) {
                    $node->nodeValue = $child;
                }
            }
        }
        
        // Strings and numerics add as value
        if (is_string($tagChildren) OR is_numeric($tagChildren)) {
            $node->nodeValue = $tagChildren;
        }
    }

    function toHtml() {
        $doc = new DOMDocument();
        $this->createElement($doc, null, $this->getArray());
        $doc->formatOutput = true;
        return $doc->saveHTML();
    }
}
