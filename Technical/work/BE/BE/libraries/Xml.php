<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/***
 * XML library for CodeIgniter
 * 
 *  http://codeigniter.com/wiki/Xml_Library
 * 
 *    author: Woody Gilk
 * copyright: (c) 2006
 *  modified: Sam Bassett, January 13 2012
 *   license: http://creativecommons.org/licenses/by-sa/2.5/
 *      file: libraries/Xml.php
 * 
 * Sam: I added load_string method, and moved original authors comments outside of function definition
 * to line up with consistency,
 * changed @public to @access public 
 */

class Xml {
  function Xml () {
  }

  private $document;
  private $filename;

  /***
 * @access public 
 * Load an file for parsing
 */
  public function load ($file) 
  {
    
    $bad  = array('|//+|', '|\.\./|');
    $good = array('/', '');
    $file = APPPATH.preg_replace ($bad, $good, $file).'.xml';

    if (! file_exists ($file)) {
      return false;
    }

    $this->document = utf8_encode (file_get_contents($file));
    $this->filename = $file;

    return true;
  }  /* END load */

  /**
  * @author Sam Bassett 
  * @access public 
  * added to library January 13, 2012
  * used if we have an xml file already loaded in string format, not saved in a file
  * 
  * @param mixed $xml_string
  */
  public function load_string($xml_string)
  {
	  $this->filename=false;
	  $this->document=$xml_string;
  }
 /**
 * @access public 
 * Parse an XML document into an array
 */ 
  public function parse () 
  {

    $xml = $this->document;
    if ($xml == '') {
      return false;
    }

    $doc = new DOMDocument ();
    $doc->preserveWhiteSpace = false;
    if ($doc->loadXML ($xml)) {
      $array = $this->flatten_node ($doc);
      if (count ($array) > 0) {
        return $array;
      }
    }

    return false;
  } /* END parse */
  
/**
 * @access private
 * Helper function to flatten an XML document into an array
 */
  private function flatten_node ($node) 
  {


    $array = array();

    foreach ($node->childNodes as $child) {
      if ($child->hasChildNodes ()) {
        if ($node->firstChild->nodeName == $node->lastChild->nodeName && $node->childNodes->length > 1) {
          $array[$child->nodeName][] = $this->flatten_node ($child);
        }
        else {
          $array[$child->nodeName][] = $this->flatten_node($child);

          if ($child->hasAttributes ()) {
            $index = count($array[$child->nodeName])-1;
            $attrs =& $array[$child->nodeName][$index]['__attrs'];
            foreach ($child->attributes as $attribute) {
              $attrs[$attribute->name] = $attribute->value;
            }
          }
        }
      }
      else {
        return $child->nodeValue;
      }
    }

    return $array;
  } /* END node_to_array */
}

?> 