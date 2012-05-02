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
		
			// Suppress warnings to prevent notices about missing indexes in $this->data
			wfSuppressWarnings();

			$this->html( 'headelement' ); ?>

			<!-- ===== Navbar ===== -->
			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<div class="container">
						<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>

						<!-- Title & logo -->
						<a href="<?php $this->data['nav_urls']['mainpage']['href'];?>" class="brand">
							<?php if( !isset($this->data['sitename'] )) {
								global $wgSitename;
								$this->set( 'sitename', $wgSitename );
							}?>
							<?php $this->text( 'sitename' ); ?>
<?php echo $wgVersion ?>
						</a>
						<!-- /Title & logo -->

						<!-- Collapsible nav -->
						<div class="nav-collapse">
									<ul class="nav secondary-nav">

										<!-- MediaWiki:Sidebar links -->
										<?php foreach( $this->data['sidebar']['navigation'] as $name => $content ) {
											if( $content == false ) continue;
											echo "\n<!-- {$name} -->\n"; 
										?>
											
											<li class="dropdown" data-dropdown="dropdown">
												<a href="<?php echo $content['href']; ?>"
 class="dropdown-toggle">
													<?php echo $content['text'];?>
												</a>
											<li>
										<?php } ?>
										<!-- /MediaWiki:Sidebar links -->

										<!-- user personal tools -->
										<li class="dropdown" data-dropdown="dropdown">
											<a href="#" data-toggle="dropdown" class="dropdown-toggle">
												<?php echo $wgUser->getName(); ?>
												<b class="caret"></b>
											</a>	
											<ul class="dropdown-menu no-collapse">
												<?php foreach( $this->data['personal_urls'] as $item ): ?>
													<li class="dropdown" data-dropdown="dropdown">
														<a href="<?php echo htmlspecialchars($item['href']) ?>"<?php echo $item['key']?>>
															<?php echo htmlspecialchars($item['text']); ?>
														</a>
													</li>
												<?php endforeach; ?>
											</ul>
										</li>
										<!-- /user personal tools -->

									</ul>
						</div>
						<!-- /Collapsible nav -->

					</div>
				</div>
			</div>		

			<!-- ===== Page ===== -->
			<div class="container-fluid">
				<div class="row-fluid">
					
					<!-- ===== Sidebar ===== -->
					<div class="span3">
						<?php BootstrapRenderer::renderSidebar(); ?>
					</div>

					<!-- ===== Content ===== -->
					<div class="span9">
						<div class="page-header">
							<h1>
								<?php $this->html( 'title' ) ?>
								<small><?php $this->html( 'subtitle' ) ?></small>
							</h1>
						</div>	
						<?php $this->html( 'bodycontent' ); ?>
						<?php $this->html( 'catlinks' ); ?>
						<?php $this->html( 'dataAfterContent' ); ?>
						<?php $this->printTrail(); ?>
					</div>
				</div>
			</div>

			</body>
			</html>
			<?php wfRestoreWarnings(); 
	}
}
