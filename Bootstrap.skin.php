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
			$out->addModuleScripts( 'skins.bootstrap' );
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
			global $wgUser;
		
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
						</a>

						<!-- Collapsible nav -->
						<div class="nav-collapse">
									<ul class="nav secondary-nav">
										<li class="dropdown" data-dropdown="dropdown">
											<a href="#" class="dropdown-toggle">
												<?php echo $wgUser->getName(); ?>
											</a>	
											<ul class="dropdown-menu">
												<?php foreach( $this->data['personal_urls'] as $item ): ?>
													<li class="dropdown" data-dropdown="dropdown">
														<a href="<?php echo htmlspecialchars($item['href']) ?>"<?php echo $item['key']?>>
															<?php echo htmlspecialchars($item['text']); ?>
														</a>
													</li>
												<?php endforeach; ?>
											</ul>
										</li>
									</ul>
						</div>
					</div>
				</div>
			</div>		

			<!-- ===== Content ===== -->
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span3">

					</div>
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
