<?php
/**
 * Bootstrap Skin
 * 
 * @file
 * @ingroup Skins
 * @author Matt Yeh (http://www.github.com/mtyeh411)
 * licence http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

	if( !defined( 'MEDIAWIKI' ) ) die( "This is a skin extension to the MediaWiki package and cannot be run standalone." );

	$wgExtensionCredits['skin'][] = array(
		'path' => __FILE__,
		'name' => 'Bootstrap',
		'url' => 'http://www.github.com/mtyeh411/bootstrap-mediawiki',
		'author' => '[http://www.github.com/mtyeh411 Matt Yeh]',
		'descriptionmsg' => 'Bootstrap skin',
	);

	$wgValidSkinNames['bootstrap'] = 'Bootstrap';
	$wgAutoloadClasses['SkinBootstrap'] = dirname( __FILE__ ).'/Bootstrap.skin.php';
	$wgAutoloadClasses['BootstrapRenderer'] = dirname( __FILE__ ).'/Bootstrap.renderer.php';
	$wgExtensionMessagesFiles['Bootstrap'] = dirname( __FILE__ ).'/Bootstrap.i18n.php';

	$skinDir = array_pop( explode( "/", dirname( __FILE__ ) ) );
	$wgResourceModules['skins.bootstrap'] = array(
		'styles' => array(
			$skinDir . '/assets/css/bootstrap.css',
			$skinDir . '/assets/css/bootstrap-responsive.css',
			$skinDir . '/assets/css/site.css',
		),
		'scripts' => array(
			$skinDir . '/assets/js/site.js',
			$skinDir . '/assets/js/bootstrap.js',
		),
		'dependencies' => array(
			// jquery automatically loaded
		),
		'remoteBasePath' => &$GLOBALS['wgStylePath'],
		'localBasePath' => &$GLOBALS['wgStyleDirectory'],
	);	

	// MW 1.19 ships with jQuery 1.7.1
	if( !version_compare( $wgVersion, '1.19', '==')) {
	array_unshift($wgResourceModules['skins.bootstrap']['scripts'], $skinDir . '/assets/js/jquery-1.7.1.min.js');
	}
	
	$sgNavbarOptions['page'] = 'Bootstrap:Navbar';
	$sgNavbarOptions['dropdown'] = true; 

	$sgSubnavOptions['page'] = 'Boostrap:Subnav';
	$sgSubnavOptions['type'] = 'pills'; # tabs, pills, list
	$sgSubnavOptions['dropdown'] = true;

	$sgSidebarOptions['page'] = 'Bootstrap:Sidebar';
	$sgSidebarOptions['type'] = 'pills'; # tabs, pills, list
	$sgSidebarOptions['dropdown'] = true;

	$sgFooterOptions['page'] = 'Bootstrap:Footer';
