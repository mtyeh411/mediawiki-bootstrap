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

	private $skin, $data;

	/*
	* Constructor
	*
	* @param BaseTemplate, Array
	*/
	public function __construct( $skin, $data ) {
		$this->skin = $skin;
		$this->data = $data;
	}

	/*
	* Render sidebar.
	*
	*	@return DOMDocument
	* @ingroup Skins
	*/
	public function renderSidebar() {
		global $sgSidebarOptions;
		$result = false;

		// get HTML-parsed MediaWiki page
		$out = BootstrapRenderer::parsePage( $sgSidebarOptions['page'] );

		// generate DOM from HTML-parsed MediaWiki page
		$doc = DOMDocument::loadXML( $out->getText() );
		$doc->documentElement->setAttribute('class','nav nav-stacked nav-' . $sgSidebarOptions['type']	);

		// render special words
		$this->renderSpecial( $doc );

		// create dropdowns for nested list items
		if( $sgSidebarOptions['dropdown'] ) {
			$this->renderDropdowns( $doc );
		}
	
		$result = $doc->saveXML( $doc->documentElement, true);
		echo $result;

		return $result;
	}

	public function renderNavbar() {
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

		// render special words
		$this->renderSpecial( $doc );

		// create dropdowns for nested list items
		if( $sgNavbarOptions['dropdown'] ) {
			$this->renderDropdowns( $doc );
		}

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
		foreach( $headerTextNodes as $headerTextNode ) {
			switch( trim( $headerTextNode->nodeValue ) ) {
				case 'SEARCH':
					$fragment= $this->renderSearch( $doc );
					$headerTextNode->parentNode->replaceChild( $fragment, $headerTextNode );
					break;
				case 'TOOLBOX': 
					$fragment= $this->renderPortal( $doc, $this->skin->getToolbox() );
					$headerTextNode->parentNode->appendChild( $fragment );
					break;
				case 'LANGUAGES':
					if( $this->skin->data['language_urls'] ) {
						$fragment = $this->renderPortal( $doc, $this->skin->data['language_urls'] );
						$headerTextNode->parentNode->appendChild( $fragment );
					} else 
						$headerTextNode->parentNode->removeChild( $headerTextNode );
					break;
				default:
					break;
			}

		}
	}

	/**
	* Render search form.
	*
	*	@param DOMDocument
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function renderSearch( $doc ) { 
		$fragment = $doc->createDocumentFragment();

		$fragment->appendXml(
		'<form action="' . $GLOBALS['wgScript'] .
				'" class="search pull-left">' .
				$this->skin->makeSearchInput( array(
				'id'=>'searchInput', 
				'class'=>'search-query', 
				'placeholder'=>'Search')) 
		.	'</form>' );

		return $fragment;
	}

	/**
	* Render special word portals.
	*
	* @param DOMDocument
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function renderPortal( $doc, $links ) {
		$fragment = $doc->createDocumentFragment();
		
		if( is_array( $links ) ) {
			$xml = '<ul>';
			foreach( $links as $key => $val ) {
				$xml .= $this->skin->makeListItem( $key, $val );
			}
			$xml .= '</ul>';
		}
		$fragment->appendXml( $xml );

		return $fragment;
	}

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
				$this->makeTogglable( $existingAnchor );
			} else {
				// insert new toggle anchor
				$textNode = $finder->query( $dropdownMenu->getNodePath() . '/../text()')->item(0);
				$toggle = $this->renderToggleAnchor( $doc, $textNode->nodeValue );
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
			$this->makeTogglable( $anchor );
			
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

