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
			global $wgUser, $wgVersion;
			$renderer = new BootstrapRenderer( $this, $this->data );
		
			// Suppress warnings to prevent notices about missing indexes in $this->data
			wfSuppressWarnings();

			$this->html( 'headelement' ); ?>

			<!-- ===== Navbar ===== -->
			<?php $renderer->renderNavbar(); ?>

			<!-- ===== Page ===== -->
			<div class="container-fluid">
				<div class="row-fluid">
					
					<!-- ===== Sidebar ===== -->
					<aside class="span3">
						<?php $renderer->renderSidebar(); ?>
					</aside>

					<!-- ===== Content ===== -->
					<article class="span9">
						<div class="page-header">
							<h1>
								<?php $this->html( 'title' ) ?>
								<small><?php $this->html( 'subtitle' ) ?></small>
							</h1>
						</div>	
						<?php $this->html( 'bodycontent' ); ?>
						<?php $this->html( 'catlinks' ); ?>
						<?php $this->html( 'dataAfterContent' ); ?>
					</article>
				</div> <!-- row -->
			</div> <!-- container -->

			<!-- ===== Footer ===== -->
			<?php $renderer->renderFooter(); ?>

			<?php $this->printTrail(); ?>

			</body>
			</html>
			<?php wfRestoreWarnings(); 
	}
}
