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
	*	@return DOMDocument
	* @ingroup Skins
	*/
	public static function renderSidebar() {
		global $sgSidebarOptions;
		$result = false;

		// get HTML-parsed MediaWiki page
		$out = BootstrapRenderer::parsePage( $sgSidebarOptions['page'] );

		// generate DOM from HTML-parsed MediaWiki page
		$doc = DOMDocument::loadXML( $out->getText() );
		$doc->documentElement->setAttribute('class','nav nav-stacked nav-' . $sgSidebarOptions['type']	);

		// create dropdowns for nested list items
		if( $sgSidebarOptions['dropdown'] ) {
			BootstrapRenderer::renderDropdowns( $doc );
		}
	
		// render special words
		BootstrapRenderer::renderSpecial( $doc );

		$result = $doc->saveXML( $doc->documentElement, true);
		echo $result;

		return $result;
	}

	public static function renderNavbar() {
		global $sgNavbarOptions;
		$result = false;

		// generate DOM from boilerplate HTML
		$doc = DOMDocument::loadXML('
			<div class="navbar">
				<div class="navbar-inner">
					<div class="container">
						<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"> </span>
							<span class="icon-bar"> </span>
							<span class="icon-bar"> </span>
						</a>

						<!-- Title & logo -->
						<a class="brand" href="#">' . $GLOBALS['wgSitename'] . '</a>

					<!-- Collapsible nav -->
						<div class="nav-collapse">
						</div>
					</div>
				</div>
			</div>');

		// get HTML-parsed MediaWiki page
		$out = BootstrapRenderer::parsePage( $sgNavbarOptions['page'] );

		// create dropdowns DOM fragment
		$dropdownFrag= $doc->createDocumentFragment();
		$dropdownFrag->appendXML( $out->getText() );
$dropdownFrag->firstChild->setAttribute('class', 'nav');

		// insert fragment into DOM document
		$finder = new DOMXPath( $doc );
		$placeholder = $finder->query('//div[contains(@class,"nav-collapse")]')->item(0);
		$placeholder->appendChild( $dropdownFrag );

		// create dropdowns for nested list items
		if( $sgNavbarOptions['dropdown'] ) {
			BootstrapRenderer::renderDropdowns( $doc );
		}

		// render special words
		// BootstrapRenderer::renderSpecial( $doc );

		$result = $doc->saveXML( $doc->documentElement, true);
		echo $result;

		return $result;
	}

	/**
	* Parse the wikitext page to HTML.
	*
	* @params String
	* @return String
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
	*	Render special reserved Wiki words.
	*
	*	@param DOMDocument
	* @ingroup Skins
	*/
	public function renderSpecial( $doc ) { 
		$finder = new DOMXPath( $doc );
		$headerTextNodes = $finder->query( '//ul[contains(@class,"nav")]/li/text()' );
		foreach( $headerTextNodes as $headerText ) {
			switch( trim($headerText->nodeValue) ) {
				case 'SEARCH':
					// TODO replace TextNode with form
					//print $GLOBALS['wgScript'];
					break;
				case 'TOOLBOX':
					// TODO
					break;
				case 'LANGUAGES':
					// TODO
					break;
				default:
					break;
			}
		}
	}

	/**
	* Render search form.
	*	@param DOMDocument
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
	* Render the dropdown list items and dropdown sub-menus
	*
	* @params DOMDocument
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function renderDropdowns( $doc ) {
		$finder = new DOMXPath( $doc );
		$dropdownMenuPath = '//ul[contains(@class,"nav")]/li/ul';
			
		// create togglable dropdown menus
		$dropdownMenus = $finder->query( $dropdownMenuPath );
		foreach( $dropdownMenus as $dropdownMenu ) {

			// create dropdown menu
			$dropdownMenu->setAttribute( 'class', 'dropdown-menu' );
			$dropdownMenu->parentNode->setAttribute( 'class', 'dropdown' );

			// add toggle anchor to document
			$existingAnchor = $finder->query( $dropdownMenu->getNodePath() . '/../a' )->item(0);
			if( $existingAnchor ) {
				// replace anchor with toggle anchor
				BootstrapRenderer::makeTogglable( $existingAnchor );
			} else {
				// insert new toggle anchor
				$textNode = $finder->query( $dropdownMenu->getNodePath() . '/../text()')->item(0);
				$toggle = BootstrapRenderer::renderToggleAnchor( $doc, $textNode->nodeValue );
				$dropdownMenu->parentNode->replaceChild( $toggle, $textNode );
			}
		}

	}

	/**
	* Render the dropdown toggle anchor.  
	* @param DOMDocument, String
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function renderToggleAnchor( $doc, $text ) {
		if( $doc instanceof DOMDocument ) {
			$fragment = $doc->createDocumentFragment();		

			$anchor = $doc->createElement( 'a', $text );
			BootstrapRenderer::makeTogglable( $anchor );
			
			// TODO add caret to anchor
			$caret = $doc->createElement( 'b' );
			$caret->setAttribute( 'class', 'caret' );

			$fragment->appendChild( $anchor );

		} else $fragment = null;

		return $fragment;
	}

	/**
	* Make an anchor node togglable for dropdown menu
	* @param DOMNode
	* @ingroup Skins
	*/
	private function makeTogglable( $node ) {
		if( $node instanceof DOMNode ) {
			$node->setAttribute( 'class', 'dropdown-toggle' );
			$node->setAttribute( 'data-toggle', 'dropdown' );
			$node->setAttribute( 'href', '#' );
		}
	}	

	/**
	* Print a DOM node (for debugging).
	* @param DOMNode
	*	@return String
	* @ingroup Skins
	*/
	private function toString( $node ) {
		if( $node instanceof DOMNode ) {
			$string = '<br/><span style="color:blue">' . $node->getNodePath() . '</span>: ' . $node->nodeValue; 
			if( $node->hasChildNodes() ) {
				foreach( $node->childNodes as $child ) {
					$string .= '<br/>' . BootstrapRenderer::toString( $child );
				}
			}
		}
		return $string;
	}

}

