<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xsl:stylesheet [
    <!ENTITY % entities SYSTEM "entities.dtd">
    %entities;
]>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    version="1.0">
    
    <!-- ul templates -->
    <xsl:template match="ul" mode="topLevel">   
        <xsl:param name="class"></xsl:param>
        <xsl:copy>
            <xsl:attribute name="class">
                <xsl:value-of select="$class"/>
            </xsl:attribute>
            <xsl:apply-templates select="li"/>
        </xsl:copy>
    </xsl:template>
    <xsl:template match="ul" mode="dropdown">
        <xsl:copy>
            <xsl:attribute name="class">&dropdown-menu-class;</xsl:attribute>
            <xsl:copy-of select="*"/>
        </xsl:copy>
    </xsl:template>
    
    <!-- li templates -->
    <xsl:template match="li[child::ul]">
        <xsl:copy>
            <xsl:attribute name="class">&dropdown-class;</xsl:attribute>
            <a class="&dropdown-toggle-class;" data-toggle="&dropdown-data-toggle;" href="#">
                <!-- content -->
                <xsl:copy-of select="*[not(self::ul)] | text()"/>
                
                <i class="&icon-caret-class;"></i>
            </a>
            <xsl:apply-templates select="ul" mode="dropdown"/>
        </xsl:copy>
    </xsl:template>
    <xsl:template match="li">
        <xsl:copy-of select="."/>
    </xsl:template>
 
</xsl:stylesheet>