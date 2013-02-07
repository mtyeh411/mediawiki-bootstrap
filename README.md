## Description ##
This is a customizable responsive [Bootstrap](https://github.com/twitter/bootstrap) [MediaWiki](http://www.mediawiki.org) skin.  With it, your MediaWiki skin can take advantage of some of the great features from Bootstrap:
* Responsive design
* Cross-browser support
* Custom jQuery plugins
* HTML5 and CSS3
* style customization through LESS CSS (requires LESS compiler)

It is compatible with most MediaWiki extensions, such as those contained in the [Semantic Bundle](http://www.mediawiki.org/wiki/SemanticBundle).  If you notice any incompatibilities, please feel free to report a bug.

This skin is based loosely off the MediaWiki Vector skin and similar MediaWiki Bootstrap projects form [Aaronpk](https://github.com/aaronpk/Bootstrap-Skin) and [Borkweb](https://github.com/borkweb/bootstrap-mediawiki).

## Requirements ##
* [MediaWiki 1.18+](http://www.mediawiki.org/wiki/Download)
* 'MediaWiki:bootstrap-navbar', 'MediaWiki:bootstrap-sidebar', and 'MediaWiki:bootstrap-footer' wiki pages (unless you choose to override [default navigation values](https://github.com/mtyeh411/mediawiki-bootstrap/#navigation-links)) 
* ___DEPRECATION WARNING___ 'Bootstrap:Navbar', 'Bootstrap:Sidebar', 'Bootstrap:Footer' are no longer used for this skin.  Please _delete_ those pages from your wiki.

## Installation ##
To install, please clone this project to the MediaWiki skins directory. Because this project relies on external repos, you will need to initialize and update the project [submodules](http://git-scm.com/book/en/Git-Tools-Submodules#Cloning-a-Project-with-Submodules).  Install with the following commands:
    
    git clone https://github.com/mtyeh411/mediawiki-bootstrap.git
    git submodule init
    git submodule update

or
    
    git clone --recursive https://github.com/mtyeh411/mediawiki-bootstrap.git
    

In ```LocalSettings.php```, add
    
    require_once( "$IP/skins/mediawiki-bootstrap/bootstrap.php");
where ```$IP``` represents your MediaWiki root directory and ```skins/mediawiki-bootstrap``` is the cloned repository path.

NOTE: Due to the way ResourceLoader handles relative paths in CSS files, Font Awesome assumes that your Bootstrap skin is located in ```skins/mediawiki-bootstrap```.  If you have a different path, you will need to change the path in ```assets/font-awesome.css```.

## Overriding skin styles ##
The ```assets``` directory contains site-specific rules and behavior which you may use to customize the Bootstrap skin to your wiki site should you choose not to use MediaWiki:Common.css or MediaWiki:Common.js (see ```assets/site.css``` and ```assets/site.js```).  This may be the case should your customized skins and behavior be specific to your instance of the Bootstrap MediaWiki skin.

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

    * Foo
      ** Foo1
      ** Foo2
    * Bar
    * [[Baz]]


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
All dependencies are located in their own directories.  The ```bootstrap``` and ```Font-Awesome``` directories are [git submodules](http://git-scm.com/docs/git-submodule), allowing those projects to remain independent. 

## Screenshots ##
Full screen resolution view:
![Full screen example](http://db.tt/ye7ULcKC)

Mobile responsive view (with opened navbar):
![Small screen responsive example](http://db.tt/0llWKCrz)
