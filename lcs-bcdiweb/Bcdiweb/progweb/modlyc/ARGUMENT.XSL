<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:msxsl="urn:schemas-microsoft-com:xslt" xmlns:bvd="http://www.bvdep.com" version="1.0" exclude-result-prefixes="bvd msxsl">

<xsl:output
	method="xml"
	omit-xml-declaration="yes"
	indent="no"
/>

<!-- overriding template for those items that have to style associated with them -->
<xsl:template match="*">
  <span style="background-color:red">
    <xsl:attribute name="title">&lt;<xsl:value-of select="local-name()"/>&gt;</xsl:attribute>
    <xsl:apply-templates/>
  </span>
</xsl:template>

<!-- root element: quatrieme -->
<xsl:template match="quatrieme">
<div class="regular" style="margin-left: 5px; margin-right: 5px"><xsl:apply-templates/></div>
</xsl:template>

<xsl:template match="presentation">
<xsl:choose>
<xsl:when test="@langue='fre' and count(//presentation) > 1"><div class="largebold">Présentation en français</div></xsl:when>
<xsl:when test="@langue='eng'"><div class="largebold">Présentation en anglais</div></xsl:when>
<xsl:when test="@langue='xxx'"><div class="largebold">Autre présentation</div></xsl:when>
</xsl:choose>
<div class="regular"><xsl:apply-templates/></div>
<div> </div>
<div align="center"><a href="javascript:window.close();">fermer cette fenêtre</a> </div>
</xsl:template>

<!-- ignore biographie de l'auteur -->
<xsl:template match="biographie" />

<!-- ignore info editeur -->
<xsl:template match="infoediteur" />

<xsl:template match="p">
<p><xsl:apply-templates/></p>
</xsl:template>

<xsl:template match="li">
<li><xsl:apply-templates/></li>
</xsl:template>

<xsl:template match="ul">
<ul><xsl:apply-templates/></ul>
</xsl:template>

<xsl:template match="ol">
<ol><xsl:apply-templates/></ol>
</xsl:template>

<xsl:template match="sup">
<sup><xsl:apply-templates/></sup>
</xsl:template>

<xsl:template match="sub">
<sub><xsl:apply-templates/></sub>
</xsl:template>
	
<xsl:template match="i">
<i><xsl:apply-templates/></i>
</xsl:template>

<xsl:template match="b">
<b><xsl:apply-templates/></b>
</xsl:template>

<xsl:template match="br">
<br /><xsl:apply-templates/>
</xsl:template>

</xsl:stylesheet>
