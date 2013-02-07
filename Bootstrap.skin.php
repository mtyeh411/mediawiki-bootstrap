<?php
/**
 * Skin file for skin Bootstrap.
 *
 * @file
 * @ingroup Skins
 */

 	/**
 	* SkinTemplate class for Bootstrap skin
 	* @ingroup Skins
 	*/
 	class SkinBootstrap extends SkinTemplate {
		
		var $skinname = 'bootstrap', $stylename = 'bootstrap',
			$template = 'BootstrapTemplate', $useHeadElement = true;

		/**
		* @param $out OutputPage object
		*/
		public function initPage( OutputPage $out ) {
			parent::initPage( $out );
			$out->addModuleScripts( 'skins.bootstrap' );
			$out->addMeta("viewport", "width=device-width, initial-scale=1.0");
			$out->addScriptFile( "http://html5shiv.googlecode.com/svn/trunk/html5.js" );
		}

		/**
		* @param $out OutputPage object
		*/
		function setupSkinUserCss( OutputPage $out ) {
			parent::setupSkinUserCss( $out );
			$out->addModuleStyles( 'skins.bootstrap' );
		}
	}

	/**
	* BaseTemplate class for Bootstrap skin
	* @ingroup Skins
	*/
	class BootstrapTemplate extends BaseTemplate {
		
		/**
		*	Outputs the entire context of the page
		*/
		public function execute() {
			global $wgUser, $wgVersion, $sgSidebarOptions;
			$renderer = new BootstrapRenderer( $this, $this->data );
		
			// Suppress warnings to prevent notices about missing indexes in $this->data
			wfSuppressWarnings();

			$this->html( 'headelement' ); ?>

			<!-- Deprecation warning -->
			<?php	$context = $this->data['skin']->getContext(); 
						$oldNavbarArticle = Article::newFromTitle(Title::newFromText( 'Bootstrap:Navbar'), $context );
						$oldSidebarArticle = Article::newFromTitle(Title::newFromText( 'Bootstrap:Sidebar'), $context );
						$oldFooterArticle = Article::newFromTitle(Title::newFromText( 'Bootstrap:Footer'), $context );
						if( (method_exists($oldNavbarArticle,"getPage") && $oldNavbarArticle->getPage()->exists()) ||
								(method_exists($oldSidebarArticle,"getPage") && $oldSidebarArticle->getPage()->exists()) ||
								(method_exists($oldFooterArticle,"getPage") && $oldFooterArticle->getPage()->exists()) ) {
							print( "DEPRECATION WARNING from MediaWiki-Bootstrap: delete Bootstrap:Navbar, Bootstrap:Sidebar, and Bootstrap:Footer, and place contents in MediaWiki:bootstrap-navbar, MediaWiki:bootstrap-sidebar, and MediaWiki:bootstrap-footer");
						}
				?>

			<!-- ===== Navbar ===== -->
			<?php $renderer->renderNavbar(); ?>

			<!-- ===== Page ===== -->
			<div id="page" class="container container-fluid">

				<!-- ===== Site notice ===== -->
					<?php if($this->data['sitenotice']) { ?>
						<header class="row-fluid">
							<div id="siteNotice" class="alert alert-info span12">
								<button class="close" data-dismiss="alert">x</button>
								<?php $this->html('sitenotice') ?>
							</div>
						</header>
					<?php } ?>

				<div class="row-fluid">
					<!-- ===== Sidebar ===== -->
					<?php $sidebarArticle = Article::newFromTitle(Title::newFromText( $sgSidebarOptions['page']), $this->data['skin']->getContext() );
						if( $sidebarArticle->getContent() != '' ) { ?>
							<aside class="span3">
								<?php $renderer->renderSidebar(); ?>
							</aside>
					<?php $contentSpanSize = "8"; } ?>
	
					<!-- ===== Article ===== -->	
					<article id="content" class="span<?php echo $contentSpanSize?>" >
						<div class="page-header">
							<h1>
								<?php $this->html( 'title' ) ?>
								<small><?php $this->html( 'subtitle' ) ?></small>
							</h1>
						</div>	
						<?php $this->html( 'bodycontent' ); ?>
						<?php $renderer->renderCatLinks(); ?>
						<?php $this->html( 'dataAfterContent' ); ?>
					</article>
				</div>

				<!-- ===== Footer ===== -->
				<?php $renderer->renderFooter(); ?>

			</div> <!-- #page .container-fluid -->

			<?php $this->printTrail(); ?>

			</body>
			</html>
			<?php wfRestoreWarnings(); 
	}
}
