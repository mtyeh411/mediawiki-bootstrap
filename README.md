## Description ##
This is a customizable responsive [Bootstrap](https://github.com/twitter/bootstrap) [MediaWiki](http://www.mediawiki.org) skin.  With it, your MediaWiki skin can take advantage of some of the great features from Bootstrap:
* Responsive design
* Cross-browser support
* Custom jQuery plugins
* HTML5 and CSS3
* style customization through LESS CSS (requires LESS compiler)

Initial tests have confirmed proper compatibility with MediaWiki extensions, such as those contained in the [Semantic Bundle](http://www.mediawiki.org/wiki/SemanticBundle).

This skin is based loosely off the MediaWiki Vector skin and similar MediaWiki Bootstrap projects form [Aaronpk](https://github.com/aaronpk/Bootstrap-Skin) and [Borkweb](https://github.com/borkweb/bootstrap-mediawiki).

## Requirements ##
* [MediaWiki 1.18+](http://www.mediawiki.org/wiki/Download)
* 'MediaWiki:bootstrap-navbar', 'MediaWiki:bootstrap-sidebar', and 'MediaWiki:bootstrap-footer' wiki pages (unless you choose to override [default navigation values](https://github.com/mtyeh411/mediawiki-bootstrap/#navigation-links)) 
* ___DEPRECATION WARNING___ 'Bootstrap:Navbar', 'Bootstrap:Sidebar', 'Bootstrap:Footer' are no longer used for this skin.  Please _delete_ those pages from your wiki.

## Installation ##
To install, please clone this project to the MediaWiki skins directory.

In ```LocalSettings.php```, add
    require_once( "$IP/skins/mediawiki-bootstrap/bootstrap.php");
where ```$IP``` represents your MediaWiki root directory and ```skins/mediawiki-bootstrap``` is the cloned repository path.

## Overriding Bootstrap styles ##
The ```assets``` directory contains site-specific rules and behavior which you may use to customize the Bootstrap skin to your wiki site (see ```assets/css/site.css``` and ```assets/js/site.js```).

Work is currently being done to provide LESS templates for site overrides.

## Usage and Customization ##
The ```bootstrap.php``` file in the Bootstrap skin directory contains configurable options for your skin output.

### Customizable components ###
Currently, this skin allows customization on your navbar, sidebar, and footer.  Each of those three components are controlled by configuration mappings:
* ```sgNavbarOptions``` for navbar
* ```sgSidebarOptions``` for sidebar
* ```sgFooterOptions``` for footer

### Navigation links ###
This skin allows you to create your navigation, sidebar, and footer from any MediaWiki page on your wiki that you specify.  Please change the mapping for the ```page``` option for your component.

By default, the skin sets the page values for each customizable component to 'Bootstrap:Navbar', 'Bootstrap:Sidebar', and 'Bootstrap:Footer'.

Please note that navigation pages assume links (and section headers) are in a single unordered list, such as:
```
    * Foo
      ** Foo1
      ** Foo2
    * Bar
    * [[Baz]]
```

Currently, two special keywords are handled:
* SEARCH
* TOOLBOX

Work is being done to handle MediaWiki Message objects as navigable links.

### Navigation menu types ###
Bootstrap supports three [types of menus](http://twitter.github.com/bootstrap/components.html#navs): 
* Pills
* Tabs
* Nav lists

You may define the type of menu you'd like each component to have by changing the ```type``` configuration.  

By default, the value is 'pills'.  Currently, only the sidebar supports this feature.

### Dropdown menus ###
The nested list items of your navigation links page are used for the dropdown menu.  The parents of those items are the dropdown toggles.

Bootstrap dropdown menus can be turned on or off by setting the ```dropdown``` configuration mapping to ```true```. 

By default, dropdowns for all components are turned on.

## Directory structure ##
All Bootstrap dependencies are located in the ```bootstrap``` directory.  The ```bootstrap``` directory is a [git submodule](http://git-scm.com/docs/git-submodule), allowing the Bootstrap project to remain independent.

The ```assets``` directory contains javascript, css, and images for Bootstrap.  These are artifacts of Bootstraps Makefile.  Please be sure to run Bootstrap's Makefile into the ```assets``` directory.  For those without the LESS compiler, you may use the existing assets. 

## Screenshots ##
Full screen resolution view:
![Full screen example](http://db.tt/ye7ULcKC)

Mobile responsive view (with opened navbar):
![Small screen responsive example](http://db.tt/0llWKCrz)
