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

	private $skin, $data, $doc;

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
		$this->doc = DOMDocument::loadXML( $out->getText() );
		$this->doc->documentElement->setAttribute('class','nav nav-stacked nav-' . $sgSidebarOptions['type']	);

		// render special words
		$this->buildSpecial();

		// create dropdowns for nested list items
		if( $sgSidebarOptions['dropdown'] ) {
			// make header list items togglable
			$this->makePathTogglable('/ul[1]/li/a', 'a');
			$this->makePathTogglable('/ul[1]/li/text()', 'a');

			$this->renderDropdowns();
		}
	
		$result = $this->doc->saveXML( $doc->documentElement, true);
		echo $result;

		return $result;
	}

	public function renderNavbar() {
		global $sgNavbarOptions;
		$result = false;

		// generate DOM from boilerplate HTML
		$this->doc = DOMDocument::loadXML('
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

		if( $out ) {
		// create dropdowns DOM fragment
		$dropdownFrag= $this->doc->createDocumentFragment();
		$dropdownFrag->appendXML( $out->getText() );
		$dropdownFrag->firstChild->setAttribute('class', 'nav');

		// insert fragment into DOM document
		$finder = new DOMXPath( $this->doc );
		$navCollapse = $finder->query('//div[contains(@class,"nav-collapse")]')->item(0);
		$navCollapse->appendChild( $dropdownFrag );

		// render special words
		$this->buildSpecial();
		$searchBar = $finder->query('//form[contains(@class,"search")]')->item(0);
		if( $searchBar ) $searchBar->setAttribute('class', 'navbar-search' );

		// create page button
		$pageBtn = array(
			'tag' => 'button',
			'attr' => array('class' => 'btn btn-info dropdown-toggle pull-right',
											'data-toggle' => 'dropdown',
											'href' => '#'),
			'children' => array(
				'icon' => array( 'attr' => array('class' => 'icon-file icon-white' )),
				'span' => array( 'attr' => array('class' => 'caret' ) )
			)
		);
		$pageButton = $this->renderButtonGroup( 
			array('class'=>'pull-right', 'id' => 'page' ), 
			array( $pageBtn ) 
		);

		$navCollapse->parentNode->insertBefore( $pageButton, $navCollapse );
		$pageTools = $this->renderDataLinks( $this->skin->data['content_actions'] );
		$pageToggle = $finder->query( '//div[@id="page"]' );
		if( $pageToggle ) 
			$pageToggle ->item(0)->appendChild( $pageTools );

		// create user button
		$userBtn = array(
			'tag' => 'button',
			'attr' => array('class' => 'btn btn-warning' ),
			'children' => array(
				'icon' => array( 'attr'=>array('class' => 'icon-user icon-white' )),
			)
		);
		$caretBtn = array(
			'tag' => 'button',
			'attr' => array('class' => 'btn btn-warning dropdown-toggle', 
											'data-toggle'=>'dropdown'),
			'children' => array(
				'span' => array( 'attr' => array('class' => 'caret' ) )
			)
		); 
		$userButtonGroup = $this->renderButtonGroup( 
			array( 'class' => 'pull-right', 'id' => 'user' ), 
			array( $userBtn, $caretBtn ) 
		);

		$navCollapse->parentNode->insertBefore( $userButtonGroup, $navCollapse );

		$userPageLink = $this->renderUserPageLink();
		$userIcon = $finder->query( '//*[contains(@class,"icon-user")]')->item(0);
		if( $userIcon ) 
			$userIcon->parentNode->insertBefore( $userPageLink, $userIcon );

		$userTools = $this->renderUserTools();
		$button = $finder->query( '//div[@id="user"]' );
		if( $button ) 
			$button->item(0)->appendChild( $userTools );

		// create dropdowns for nested list items
		if( $sgNavbarOptions['dropdown'] ) {
			$this->makePathTogglable('//ul[contains(@class,"nav")]/li/a', 'a');
			$this->makePathTogglable('//ul[contains(@class,"nav")]/li/text()', 'a');
			$this->renderDropdowns();
		}

		$result = $this->doc->saveXML( $doc->documentElement, true);
		echo $result;
		}

		return $result;
	}

	public function renderFooter() {
		global $sgFooterOptions;
		$result = false;

		$out = BootstrapRenderer::parsePage( $sgFooterOptions['page'] );

		// generate DOM from HTML-parsed MediaWiki page
		if( $out ) {
			$html = $out->getText();
		} else { // use MediaWiki footer links & icons

			foreach( $this->skin->getFooterLinks() as $category => $links ) {	
				$html .= Xml::openElement('ul', array( 'class' => 'horizontal' ));
				foreach( $links as $link ) {
					$html .= Xml::openElement('li');
					$html .= $this->skin->data[ $link ];
					$html .= Xml::closeElement('li');	
				}
				$html .= Xml::closeElement('ul');	
			}
			
			foreach( $this->skin->getFooterIcons("icononly") 
				as $blockName => $footerIcons ) {
				$html .= Xml::openElement('ul', array('class' => 'horizontal pull-right' ));
				foreach( $footerIcons as $icon ) {
					$html .= Xml::openElement('li');	
					$html .= $this->skin->getSkin()->makeFooterIcon( $icon );
					$html .= Xml::closeElement('li');
				}
				$html .= Xml::closeElement('ul');
			}
		}

		$this->doc = DOMDocument::loadXML( 
			Xml::openElement( 'footer', array( 'class' => 'footer' )) .
			$html .
			Xml::closeElement( 'footer' )
		);

		$result = $this->doc->saveXML( $doc->documentElement, true);
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
		if( $raw ) 
			return $wgParser->parse( $raw, $pageTitle, ParserOptions::newFromUser($wgUser));
	}

	/**
	*	Render special Wiki words from Navbar & Sidebar pages.
	*
	* @ingroup Skins
	*/
	public function buildSpecial() { 
		$finder = new DOMXPath( $this->doc );
		$headerTextNodes = $finder->query( '//ul[contains(@class,"nav")]/li/text()' );
		foreach( $headerTextNodes as $headerTextNode ) {
			switch( trim( $headerTextNode->nodeValue ) ) {
				case 'SEARCH':
					$fragment= $this->renderSearch();
					$headerTextNode->parentNode->replaceChild( $fragment, $headerTextNode );
					break;
				case 'TOOLBOX': 
					$fragment= $this->renderDataLinks( $this->skin->getToolbox() );
					$headerTextNode->parentNode->appendChild( $fragment );
					break;
				case 'LANGUAGES':
					if( $this->skin->data['language_urls'] ) {
						$fragment = $this->renderDataLinks( $this->skin->data['language_urls'] );
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
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function renderSearch() { 
		$fragment = $this->doc->createDocumentFragment();

		$fragment->appendXml(
		'<form action="' . $GLOBALS['wgScript'] .
				'" class="search">' .
				$this->skin->makeSearchInput( array(
				'id'=>'searchInput', 
				'class'=>'search-query', 
				'placeholder'=>'Search')) 
		.	'</form>' );

		return $fragment;
	}

	private function renderButtonGroup( $groupAttrs, $buttons, $toString=false ) {
		$groupAttrs['class'] .= ' btn-group';
		
		if( is_array( $groupAttrs ) ) {
			$groupElement = Xml::openElement( 'div', $groupAttrs );

			if( is_array( $buttons ) ) {
				foreach( $buttons as $button ) {
					$groupElement .= $this->renderButton( $button, true );
				}
			}

			$groupElement.= Xml::closeElement('div');
		}

		if( $toString ) 
			return $groupElement;
		else {
			$fragment = $this->doc->createDocumentFragment();
			$fragment->appendXml( $groupElement );
			return $fragment;
		}
	}

	/**
	* Render button.
	*
	* @return DOMDocumentFragment
	*	@ingroup Skins
	*/
	private function renderButton( $btn, $toString = false ) {

		$btnElement = Xml::openElement( $btn['tag'], $btn['attr'] ); 
		$btnElement .= $btn['contents'];
		foreach( $btn['children'] as $key => $val) {
			$btnElement .= Xml::tags( 
				$key,
				$val['attr'],
				$val['contents']
			);
		}
		$btnElement .= Xml::closeElement( $btn['tag'] );

		if( $toString ) 
			return $btnElement;
		else {
			$fragment = $this->doc->createDocumentFragment();
			$fragment->appendXml( $btnElement);
			return $fragment;
		}

	}

	/*
	*	Render an unordered list of link items from Wiki personal url's.
	*
	*	@return DOMDocumentFragment
	*	@ingroup Skins
	*/
	private function renderUserTools() {
		// create document fragment of user tool list items 
		$userTools = $this->skin->getPersonalTools();
		array_shift( $userTools );
		$fragment = $this->renderDataLinks( $userTools );

		return $fragment;
	}	

	/**
	* Render Wiki link array to lists.
	*
	* @param Array
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function renderDataLinks( $links ) {
		$fragment = $this->doc->createDocumentFragment();
		
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

	private function renderUserPageLink() {
		$fragment = $this->doc->createDocumentFragment();
		// user page link
		$userPage = array_shift( $this->skin->getPersonalTools() );
		foreach( $userPage['links'] as $key => $val ) {
			$userLink .= $this->skin->makeLink( $key, $val );
		}
		$fragment->appendXml( $userLink );
		return $fragment;
	}

	/**
	* Render the dropdown list items and dropdown sub-menus
	*
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function renderDropdowns() {
		$finder = new DOMXPath( $this->doc );
		$dropdownMenuPath = '//*[contains(@class,"dropdown-toggle")]/../ul';
			
		// create togglable dropdown menus
		$dropdownMenus = $finder->query( $dropdownMenuPath );
		foreach( $dropdownMenus as $dropdownMenu ) {

			// create dropdown menu
			$dropdownMenu->setAttribute( 'class', 'dropdown-menu' );

			// set parent node attributes if needed 
			if( $dropdownMenu->parentNode->nodeName == 'li' ) 
				$dropdownMenu->parentNode->setAttribute( 'class', 'dropdown' );

		}

	}

	/*
	*	Iterate through nodes of a given XPath path and create/replace each node with a togglable HTMLelements of the given tag type
	*	
	*	@params XPath path string, DOM node tag string
	*	@ingroup Skins
	*/
	private function makePathTogglable( $path, $tagType ) {
		$finder = new DOMXPath( $this->doc );

		foreach( $finder->query( $path ) as $header ) {
			
			// get the single text value 
			if( $header->nodeType != 3 ) { // if not a textnode
				$headerText = $finder->query( $path . '/text()')->item(0);
				$headerTextValue = $headerText->nodeValue;
			} else
				$headerTextValue = $header->nodeValue;

			// only toggle nodes with sibling lists 
			$ulSiblings = $finder->query( $header->getNodePath() . '/../ul' );
			if( $ulSiblings->length > 0 )
				$this->makeTogglable( $header, $headerTextValue, $tagType );
		}

	}


	/*
	*	Replace a given node with a togglable node of a given HTML tag 
	*
	*	@param DOMNode, String, DOMNode tag string
	*	@ingroup Skins
	*/
	private function makeTogglable( $node, $text, $tagType) {
		if( $node instanceof DOMNode ) {
			// if node tag is not the same as 
			//if( $node->nodeName != $tagType ) {
				if(	$this->doc instanceof DOMDocument &&
						trim($node->nodeValue) != '') {
					$fragment = $this->doc->createDocumentFragment();		

					$fragment->appendXml(
						'<' . $tagType . ' class="dropdown-toggle" data-toggle="dropdown" href="#">' .
						$text .
						'<i class="caret"> </i>' .
						'</' . $tagType . '>'
					);
	
					// replace node with $fragment
					$node->parentNode->replaceChild( $fragment, $node );
				}
			} //else {
				//$this->setToggleAttrs( $node );
			//}
		//}
	}	

	/**
	* DEPRECATE?: Make a node togglable for dropdown menu
	*
	* @param DOMNode
	* @ingroup Skins
	*/
	private function setToggleAttrs( $node ) {
		if( $node instanceof DOMNode ) {
			$node->setAttribute( 'class', 'dropdown-toggle' );
			$node->setAttribute( 'data-toggle', 'dropdown' );
			$node->setAttribute( 'href', '#' );
		}
	}	

	/**
	* TODO DEPRECATE: Render the dropdown toggle anchor.  
	*
	* @param String
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function makeToggleAnchor( $text ) {
		if( $this->doc instanceof DOMDocument ) {
			$fragment = $this->doc->createDocumentFragment();		

			$anchor = $this->doc->createElement( 'a', $text );
			$this->setToggleAttrs( $anchor );
			
			// TODO add caret to anchor
			$caret = $this->doc->createElement( 'b' );
			$caret->setAttribute( 'class', 'caret' );

			$fragment->appendChild( $anchor );

		} else $fragment = null;

		return $fragment;
	}


	/**
	* Print a DOM node (for debugging).
	* @param DOMNode
	*	@return String
	* @ingroup Skins
	*/
	private function NodeToString( $node ) {
		if( $node instanceof DOMNode ) {
			$string = '<br/><span style="color:blue">' . $node->getNodePath() . '</span>: ' . $node->nodeValue; 
			if( $node->hasChildNodes() ) {
				foreach( $node->childNodes as $child ) {
					$string .= '<br/>' . BootstrapRenderer::NodeToString( $child );
				}
			}
		}
		return $string;
	}

}

