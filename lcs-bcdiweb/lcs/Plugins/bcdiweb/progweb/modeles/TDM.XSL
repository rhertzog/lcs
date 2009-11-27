<?xml version="1.0" encoding="iso-8859-1"?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
                xmlns:msxsl="urn:schemas-microsoft-com:xslt"
                xmlns:user="http://mycompany.com/mynamespace"
                version="1.0"  exclude-result-prefixes="user msxsl">
   <xsl:output method="html" version="4.0" encoding="ISO-8859-1" omit-xml-declaration="yes" standalone="yes" indent="yes" media-type="text/html" />

<xsl:template match="tdm">

<table width="100%">
<xsl:for-each select="head">
<tr>
<xsl:choose>
<xsl:when test="@level = '0'">
<td align="center" class="tochead0"><xsl:value-of select="."/></td>
</xsl:when>

<xsl:when test="@level = '1'">
<td align="center" class="tochead1"><xsl:value-of select="."/></td>
</xsl:when>

<xsl:when test="@level = '2'">
<td align="center" class="tochead2"><xsl:value-of select="."/></td>
</xsl:when>

<xsl:otherwise>
<td align="center" class="tochead_other"><xsl:value-of select="."/></td>
</xsl:otherwise>
</xsl:choose>
</tr>
</xsl:for-each>
</table>

<table width="100%">
<xsl:for-each select="tocitem">
<tr>
<xsl:choose>
<xsl:when test="item/@level = '0'">
<td class="tocbody0"><xsl:apply-templates/></td>
<td align="right" class="tocbody0"><xsl:value-of select="page"/></td>
</xsl:when>

<xsl:when test="item/@level = '1'">
<td class="tocbody1">
&#160;&#160;
<xsl:apply-templates/>
</td>
<td align="right" class="tocbody1"><xsl:value-of select="page"/></td>
</xsl:when>

<xsl:when test="item/@level = '2'">
<td class="tocbody2">
&#160;&#160;&#160;&#160;
<xsl:apply-templates/>
</td>
<td align="right" class="tocbody2"><xsl:value-of select="page"/></td>
</xsl:when>

<xsl:when test="item/@level = '3'">
<td class="tocbody3">
&#160;&#160;&#160;&#160;
<xsl:apply-templates/>
</td>
<td align="right" class="tocbody3"><xsl:value-of select="page"/></td>
</xsl:when>

<xsl:otherwise>
<td class="tocbody_other">
&#160;&#160;&#160;&#160;&#160;&#160;
<xsl:apply-templates/>
</td>
<td align="right" class="tocbody_other"><xsl:value-of select="page"/></td>
</xsl:otherwise>
</xsl:choose>
</tr>
</xsl:for-each>
</table>
<div> </div>
<div align="center"><a href="javascript:window.close();">fermer cette fenêtre</a> </div>
</xsl:template>

<xsl:template match="item">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="page" />

<xsl:template match="br">
<br /><xsl:apply-templates/>
</xsl:template>

<xsl:template match="i">
<i><xsl:apply-templates/></i>
</xsl:template>

<xsl:template match="b">
<b><xsl:apply-templates/></b>
</xsl:template>

<xsl:template match="sup">
<sup><xsl:apply-templates/></sup>
</xsl:template>

<xsl:template match="sub">
<sub><xsl:apply-templates/></sub>
</xsl:template>



</xsl:stylesheet>
