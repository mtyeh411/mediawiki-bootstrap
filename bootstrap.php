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
	$wgExtensionMessagesFiles['Bootstrap'] = dirname( __FILE__ ).'/Bootstrap.i18n.php';

	$wgResourceModules['skins.bootstrap'] = array(
		'styles' => array(
			'bootstrap/bootstrap/bootstrap.css',
			'bootstrap/bootstrap/site.css',
		),
		'scripts' => array(
			//'bootstrap/bootstrap/site.js',
			//'bootstrap/bootstrap/js/bootstrap-tabs.js',
			//'bootstrap/bootstrap/js/bootstrap-dropdown.js',
		),
		'dependencies' => array(
			// jquery automatically loaded
		),
		'remoteBasePath' => &$GLOBALS['wgStylePath'],
		'localBasePath' => &$GLOBALS['wgStyleDirectory'],
	);	
