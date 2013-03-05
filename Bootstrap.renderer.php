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

	/**
	*	Render category links.
	*
	*	@return DOMDocument
	*	@ingroup Skins
	*/
	public function renderCatLinks() {
		$result = false;

		if( $this->skin->data['catlinks'] ) {
			$doc = DOMDocument::loadXML( 
				Xml::openElement( 'footer' ) .
				$this->skin->data[ 'catlinks' ] .
				Xml::closeElement( 'footer' )
			);

			$finder = new DOMXPath( $doc );
			$container = $finder->query('//div[contains(@class,"catlinks")]')->item(0);
			$container->setAttribute( 'class', $container->getAttribute('class') . 
				' well' );
	
			$result = $doc->saveHTML();
			echo $result;
		}

		return $result;
	}

	/*
	*
	* Print footer.
	*
	* @ingroup Skins
	*
	*/
	public function renderFooter() {
		global $sgFooterOptions;

		// parse and transform
		$footer = $this->parseAndTransform( $sgFooterOptions['page'], DIRNAME(__FILE__) .'/xsl/footer.xsl'); 

		$output = new DOMDocument();
		$output->loadXML( $footer );

		// add footer links 
		foreach( $this->skin->getFooterLinks() as $category => $links ) {	
			$footerLinks = $output->createElement('ul');
			$footerLinks->setAttribute('class', 'horizontal');
			foreach( $links as $link ) {
				$footerLink = $output->createDocumentFragment();
				$footerLink->appendXML(
					"<li>" .
					$this->skin->data[$link] .
					"</li>"
				);
				$footerLinks->appendChild( $footerLink );	
			}
		}
		$output->appendChild( $footerLinks );

		// add footer icons
		foreach( $this->skin->getFooterIcons("icononly") 
			as $blockName => $icons ) {
			$footerIcons = $output->createElement('ul');
			$footerIcons->setAttribute('class', 'horizontal pull-right');
			foreach( $icons as $icon ) {
				$footerIcon = $output->createDocumentFragment();
				$footerIcon->appendXML(
					"<li>" .
					$this->skin->getSkin()->makeFooterIcon( $icon ) .
					"</li>"
				);
				$footerIcons->appendChild( $footerIcon );
			}
		}	
		$output->appendChild( $footerIcons );

		// output as HTML
		if( $output ) echo $output->saveHTML();
	}


	/**
	* 
	* Print the sidebar.
	*
	* @ingroup Skins
	*/
	public function renderSidebar() {
		global $sgSidebarOptions;
		$type = 'nav-' . $sgSidebarOptions['type'];

		// parse and transform
		$sidebar = $this->parseAndTransform( $sgSidebarOptions['page'], DIRNAME(__FILE__) .'/xsl/sidebar.xsl', array('nav-type'=>$type)); 

		// output as HTML
		$output = DOMDocument::loadXML( $sidebar );
		if( $output ) echo $output->saveHTML();
	}

	/*
	*
	* Print the navbar. 
	*
	* @ingroup Skins
	*/
	public function renderNavbar() {
		global $sgNavbarOptions, $wgUser;
		$navbar = $this->parseAndTransform( $sgNavbarOptions['page'], DIRNAME(__FILE__) .'/xsl/navbar.xsl'); 

		$output = DOMDocument::loadXML( $navbar );

		// continue rendering...
		if( $output ) {
			$finder = new DOMXPath( $output );

			// setup brand link
			$brand = $finder->query('//a[contains(@class,"brand")]')->item(0);
			if( $brand ) {
				$brand->setAttribute( 'href',
					$this->skin->data['nav_urls']['mainpage']['href']
				);
				$siteName = new DOMText( $GLOBALS['wgSitename'] );
				$brand->appendChild( $siteName );
			}

			// create the shared dropdown element 
			$dropdownBtn = $output->createElement('button');
			$dropdownBtn->setAttribute('class', 'btn dropdown-toggle');
			$dropdownBtn->setAttribute('data-toggle', 'dropdown');
			$dropdownIcon = $output->createElement('span');
			$dropdownIcon->setAttribute('class', 'caret');
			$dropdownBtn->appendChild( $dropdownIcon );	

			// setup user tools
			$userTool = $finder->query('//div[@id="user"]')->item(0);
			if( $userTool ) {
				// get user links
				$userLinks = $this->skin->getPersonalTools();
				$user = ( $wgUser->isLoggedIn() ) ? array_shift($userLinks) : array_pop($userLinks); //'userpage' or 'anonlogin', respectively
				$userLink = $user['links'][0];
		
				// create the user button
				$userTxt = new DOMText( $userLink['text'] . ' ' );
				$userBtn = $output->createElement('a');	
				$userBtn->setAttribute('href', $userLink['href']);
				$userBtn->setAttribute('class', 'btn btn-warning');
				$userBtn->appendChild( $userTxt );

				$userIcon = $output->createElement('icon');
				$icon = ( $wgUser->isLoggedIn() ) ? 'icon-user' : 'icon-signin';
				$userIcon->setAttribute('class', $icon . ' icon-white');

				$userBtn->appendChild( $userIcon );
			
				// create user dropdown	
				$userCaret = $dropdownBtn->cloneNode( true );
				$userCaret->setAttribute('class', $dropdownBtn->getAttribute('class') . ' btn-warning');

				// create the dropdown links
				$userDropdown = $this->renderDataLinks( $userLinks, 'dropdown-menu' );

				// put it all together
				$userTool->appendChild( $userBtn );
				$userTool->appendChild( $userCaret);
				$userTool->appendChild( $output->importNode($userDropdown, true) );
			}

			// update page tools
			$pageTool = $finder->query('//div[@id="page"]')->item(0);
			if( $pageTool ) {
				// create the button
				$pageBtn = $dropdownBtn->cloneNode( true );
				$pageBtn->setAttribute('class', $dropdownBtn->getAttribute('class') . ' btn-info');
				$pageIcon = $output->createElement('icon', ' ');
				$pageIcon->setAttribute('class', 'icon-file icon-white');
				$pageBtn->insertBefore( $pageIcon, $pageBtn->firstChild);

				// create dropdown links 
				$pageDropdown = $this->renderDataLinks( $this->skin->data['content_actions'], 'dropdown-menu' );

				// put it all together
				$pageTool->appendChild( $pageBtn );
				$pageTool->appendChild( $output->importNode($pageDropdown, true) );
			}

			// output as HTML
			echo $output->saveHTML();
		}
	}

	/**
	*
	* Parse the $wikipage, replace the special words, and then transform the resulting HTML fragment using XSLT $stylesheet 
	*
	* @param String
	* @param String
	* @param Array 
	*
	* @ingroup Skins
	* @return String
	**/
	private function parseAndTransform( $wikipage, $stylesheet, $params=array() ) 	{
		$html = new DOMDocument();

		// parse wikitext
		$content = BootstrapRenderer::parsePage( $wikipage );
		if( $content ) {
			$html->loadXML( $content->getText() );

			$this->addSpecial( $html );		

			// load $stylesheet
			$xslDoc = new DOMDocument();
			$xslDoc->load( $stylesheet, LIBXML_NOENT|LIBXML_DTDLOAD);

			// transform
			$xslt = new XSLTProcessor();
			if( isset($params) ) {
				foreach( $params as $param=>$value ) {
					$xslt->setParameter('', $param, $value);
				}
			} 
			$xslt->importStyleSheet( $xslDoc );
			return $xslt->transformToXML( $html );	
		} else 
			return '';
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
	*	Render special Wiki words from DOMDocument.
	*
	* @ingroup Skins
	*/
	private function addSpecial( $doc ) { 
		// look for SPECIAL words in all list items
		$finder = new DOMXPath( $doc );
		$headerTextNodes = $finder->query( '//li/text()' );

		// replace SPECIAL words
		foreach( $headerTextNodes as $headerTextNode ) {
			switch( trim( $headerTextNode->nodeValue ) ) {
				case 'SEARCH':
					$fragment= $this->renderSearch( $doc );
					$headerTextNode->parentNode->replaceChild( $fragment, $headerTextNode );
					break;
				case 'TOOLBOX': 
					$fragment= $this->renderDataLinks( $this->skin->getToolbox() );
					$headerTextNode->parentNode->appendChild( $doc->importNode($fragment,true) );
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
	private function renderSearch( $doc ) { 
		$fragment = $doc->createDocumentFragment();

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

	/**
	* Render Wiki link array to lists.
	*
	* @param Array
	* @return DOMDocumentFragment
	* @ingroup Skins
	*/
	private function renderDataLinks( $links, $class='' ) {
		$doc = new DOMDocument();
		$fragment = $doc->createDocumentFragment();
		
		if( is_array( $links ) ) {
			$xml = '<ul class="' . $class .'">';
			foreach( $links as $key => $val ) {
				$xml .= $this->skin->makeListItem( $key, $val );
			}
			$xml .= '</ul>';
		}
		$fragment->appendXml( $xml );

		return $fragment;
	}

}

