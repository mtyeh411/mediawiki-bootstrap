<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
    <!ENTITY % entities SYSTEM "entities.dtd">
    %entities;
]>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    version="1.0">
    
    <!-- add nav class to root ul element -->
    <xsl:template match="/">
        <nav class="navbar">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="&collapse-data-toggle;" data-target=".&nav-collapse-class;">
                        <span class="&icon-bar-class;"></span>
                        <span class="&icon-bar-class;"></span>
                        <span class="&icon-bar-class;"></span>
                    </a>
                    
                    <a class="brand"></a>
                    
                    <div id="page" class="&site-tool-group;"></div>
                    
                    <div id="user" class="&site-tool-group;"></div>
                    
                    <div class="&nav-collapse-class;">
                        <xsl:apply-templates select="ul[1]" mode="topLevel">
                            <xsl:with-param name="class">&navbar-nav;</xsl:with-param>
                        </xsl:apply-templates>
                    </div>
                </div>
            </div>
        </nav>
    </xsl:template>
    
    <xsl:include href="dropdown.xsl"/>
    
</xsl:stylesheet>
