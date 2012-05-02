<?php
/**
	* HTML renderer for Bootstrap skin
	*
	* @file
	* @ingroup Skins
	*/

	/**
	* BootstrapRenderer class for Bootstrap skin 
	* @ingroup Skins	
	*/

	class BootstrapRenderer {

	/*
	* Render sidebar.
	*
	* @ingroup Skins
	*/
	public static function renderSidebar() {
		global $sgSidebarOptions;
		$result = false;

		// get HTML-parsed MediaWiki page
		//$out = BootstrapRenderer::parsePage( $sgSidebarOptions['page'] );
		$out = BootstrapRenderer::parsePage( 'MediaWiki:Sidebar' );

		// generate DOM from HTML-parsed MediaWiki page
		$doc = DOMDocument::loadXML( $out->getText() );
		$doc->documentElement->setAttribute('class','nav nav-stacked nav-' . $sgSidebarOptions['type']	);

		// create dropdowns for nested list items
		if( $sgSidebarOptions['dropdown'] ) {
			BootstrapRenderer::renderDropdowns( $doc );
		}

		$result = $doc->saveXML( $doc->documentElement, true);
		echo $result;

		return $result;
	}

	/**
	* Render search form.
	*
	* @ingroup Skins
	*/
	public function renderSearch() { ?>
		<form action="<?php $this->text('wgScript')?>" 
					class="navbar-search pull-left">
		<?php echo $this->makeSearchInput( array(
			'id'=>'searchInput', 
			'class'=>'search-query', 
			'placeholder'=>'Search')); 
		?>		
		</form>
	<?php }

	/**
	* Parse the wikitext page to HTML.
	*
	* @params String
	* @ingroup Skins
	*/
	private function parsePage( $page ) {
		global $wgParser, $wgUser;
		$pageTitle = Title::newFromText( $page ); 
		$article = new Article( $pageTitle ); 
		$raw = $article->getRawText();
		return $wgParser->parse( $raw, $pageTitle, ParserOptions::newFromUser($wgUser));
	}

	/**
	* Render the dropdown list items and dropdown sub-menus
	*
	* @params DOMDocument
	* @ingroup Skins
	*/
	private function renderDropdowns( $doc ) {
		$xpath = new DOMXPath( $doc );
		$dropdownQuery = '/ul[1]/li';
		$dropdownTextQuery = '/ul[1]/li/text()'; // from dropdownQuery context
		$dropdownMenuQuery = '/ul[1]/li/ul';
			
		$dropdowns = $xpath->query( $dropdownQuery );
		$i = 1;
		foreach( $dropdowns as $dropdown ) {
			$dropdown->setAttribute('class', 'dropdown');
			$textNodes = $xpath->query( $dropdownQuery . '[' . $i . ']/text()[1]');
			$textNode = $textNodes->item(0);
			$textValue = $textNode->nodeValue;
			$toggle = BootstrapRenderer::renderToggleAnchor( $doc, $textValue );
			$dropdown->replaceChild( $toggle, $textNode );
			$i++;
		}

		$dropdownMenus = $xpath->query( $dropdownMenuQuery );
		foreach( $dropdownMenus as $dropdownMenu ) {
			$dropdownMenu->setAttribute('class', 'dropdown-menu');
		}
	}

	/**
	* Render the dropdown toggle anchor.  
	* @param DOMDocument, String
	* @ingroup Skins
	*/
	private function renderToggleAnchor( $doc, $text ) {
		if( $doc instanceof DOMDocument ) {
			$toggle = $doc->createElement( 'a', $text );
			$toggle->setAttribute( 'class', 'dropdown-toggle' );
			$toggle->setAttribute( 'data-toggle', 'dropdown' );
			$toggle->setAttribute( 'href', '#' );

			$caret = $doc->createElement( 'b' );
			$caret->setAttribute('class', 'caret' );

			//$toggle->appendChild( $caret );
		} else $toggle = null;

		return $toggle;
	}

	/**
	* Print a DOM node (for debugging).
	* @param DOMNode
	* @ingroup Skins
	*/
	private function printNode( $node ) {
		if( $node instanceof DOMNode ) {
			print '<br/>' . get_class($node) . ' ( at ' .
						$node->getNodePath() . '): ' . $node->nodeValue; 
		}
	}

}

