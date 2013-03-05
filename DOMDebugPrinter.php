<?php

/**
* @file
* @ingroup Skins
*/
class DOMDebugPrinter {

	public static function printDoc( $domDoc ) {
		$root = $domDoc->documentElement;
		foreach( $root->childNodes as $node ) {
			print( $node->nodeName." => ".$node->nodeValue );
		}
	}

	/**
	* Print a DOM node (for debugging).
	* @param DOMNode
	*	@return String
	* @ingroup Skins
	*/
	public static function printNode( $node ) {
		if( $node instanceof DOMNode ) {
			$string = '<br/><span style="color:blue">' . $node->getNodePath() . '</span>: ' . $node->nodeValue; 
			if( $node->hasChildNodes() ) {
				foreach( $node->childNodes as $child ) {
					$string .= '<br/>' . BootstrapRenderer::NodeToString( $child );
				}
			}
			return $string;
		} else {
			return "Not a DOMNode";
		}
	}

}
