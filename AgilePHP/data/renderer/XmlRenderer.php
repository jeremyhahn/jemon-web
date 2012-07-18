<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.data.renderer
 */

/**
 * Transforms data to well formed xml
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.data.renderer
 */
class XmlRenderer implements DataRenderer {

      /**
       * Renders the specified data as XML.
       *
       * @param mixed $data The data to render. (primitive|array|object)
       * @param string $name The root node name
       * @param string $pluralName The plural name to use when children are encountered
       * @param boolean $isChild Use internally by the method when called recursively
       * @param boolean $declaration True to include <?xml ... ?> doctype declaration, false to omit
       * @return string An XML document representing the specified data
       */
      public static function render($data, $name = 'Result', $pluralName = 'Results', $isChild = false, $declaration = true) {

             if($isChild) $xml = '';
	  		 else if($declaration) $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
	  		 else $xml = '';

	  	     if(is_array($data)) {

	  	      	if(!count($data)) return '<' . $name . '/>';

  	 		  	$xml .= '<' . ((!$isChild) ? $pluralName : $name) . '>';
  	 		  	foreach($data as $key => $val) {

  	 		  	        //if(is_numeric($key)) throw new FrameworkException('XmlRenderer requires associative arrays');

  	 		  	        if(is_object($val)) {

		  		 		    $ns = explode('\\', get_class($val));
		  		 		    $cls = array_pop($ns);

		  		 		    $xml .= self::render($val, $cls, $pluralName, true, false);
		  		 		 }
		  		 		 elseif(is_array($val)) {

		  		 		    if(isset($val[0]) && is_object($val[0])) {

		  		 		       $ns = explode('\\', get_class($val[0]));
		  		 		       $name = array_pop($ns);
		  		 		    }

		  		 		    $xml .= self::render($val, $name, $pluralName, true, false);
		  		 		 }
    	 		  	  	 else {

    	 		  	  	    $val = mb_convert_encoding($val, 'UTF-8', 'ISO-8859-1');
      	 		  	  	    $xml .= '<' . $key . '>' . $val . '</' . $key . '>';
      	 		  	  	 }
  	 		  	 }
  	 		  	 $xml .= '</' . ((!$isChild) ? $pluralName : $name) . '>';

 	 		  	 return $xml;
	  	      }

	  	      else if(is_object($data)) {

	  	      	  $class = new ReflectionClass($data);
	  	      	  $clsName = $class->getName();

	  	      	  // stdClass has public properties
		  		  if($clsName == 'stdClass') {

		  		  	  $xml .= '<' . $name . '>';
		  		  	  foreach(get_object_vars($data) as $property => $value) {

		  		 		  if(is_object($value) || is_array($value))
		  		 		  	 $xml .= self::render($value, $property, $property, true, false);

		  		 		  else {

			  		 		  $value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
			  		 		  $xml .= '<' . $property . '>' . $value . '</' . $property . '>';
		  		 		  }
		  		 	  }
		  		 	  $xml .= '</' . $name . '>';

		  		 	  return $xml;
	  		     }

		  	     // @todo Interceptors are still being somewhat intrusive to reflection operations
	  		     if(method_exists($data, 'getInterceptedInstance')) {

	  		     	$clsName = preg_replace('/_Intercepted/', '', $class->getName());
	  		     	$instance = $data->getInterceptedInstance();
	  		     	$class = new ReflectionClass($instance);
	  		     	$data = $instance;
	  		     }

	  		     // php namespace support
			     $namespace = explode('\\', $clsName);
			     $className = array_pop($namespace);
		 	     $namespace = implode('\\', $namespace);

		 	     $node = ($name == 'Result') ? $className : $name;

		  		 $xml = '<' . $node . '>';
		  		 foreach($class->getProperties() as $property) {

		  		 		 $context = null;
		  		 		 if($property->isPublic())
		  		 		  	$context = 'public';
		  		 		 else if($property->isProtected())
		  		 		 	$context = 'protected';
		  		 		 else if($property->isPrivate())
		  		 		  	 $context = 'private';

		  		 		 $value = null;
		  		 		 if($context != 'public') {

		  		 		  	$property->setAccessible(true);
				  		 	$value = $property->getValue($data);
				  		 	$property->setAccessible(false);
		  		 		 }
		  		 		 else {

		  		 		  	$value = $property->getValue($data);
		  		 		 }

		  		 		 if(is_object($value)) {

		  		 		    $ns = explode('\\', get_class($value));
		  		 		    $cls = array_pop($ns);

		  		 		    $xml .= self::render($value, $property->getName(), $cls, true, false);
		  		 		 }
		  		 		 elseif(is_array($value)) {

		  		 		     if(isset($value[0]) && is_object($value[0])) {

		  		 		        $ns = explode('\\', get_class($value[0]));
		  		 		        $name = array_pop($ns);
		  		 		     }
		  		 		     else
		  		 		        $name = $property->getName();

		  		 		 	$xml .= self::render($value, $property->getName(), $name, true, false);
		  		 		 }
		  		 		 else {

			  		 		$value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
			  		 		$xml .= '<' . $property->getName() . '>' . $value . '</' . $property->getName() . '>';
		  		 		  }
		  		 }
		  		 $xml .= '</' . $node . '>';
	  		 }
	  		 return $xml;
      }
}
?>