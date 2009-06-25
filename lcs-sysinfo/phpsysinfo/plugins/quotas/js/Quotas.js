/***************************************************************************
 *   Copyright (C) 2008 by phpSysInfo - A PHP System Information Script    *
 *   http://phpsysinfo.sourceforge.net/                                    *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 *   This program is distributed in the hope that it will be useful,       *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         *
 *   GNU General Public License for more details.                          *
 *                                                                         *
 *   You should have received a copy of the GNU General Public License     *
 *   along with this program; if not, write to the                         *
 *   Free Software Foundation, Inc.,                                       *
 *   59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             *
 ***************************************************************************/
//
// $Id: Quotas.js 178 2009-03-31 13:52:43Z bigmichi1 $
//

/*global $, jQuery, buildBlock, datetime, plugin_translate, genlang, formatBytes, createBar, appendcss */

var quotas_show = false, quotas_table;

appendcss("./plugins/quotas/css/quotas.css");

/**
 * insert content into table
 * @param {jQuery} xml plugin-XML
 */
function quotas_populate(xml) {
  var plugins, quotas, quota, user = "", bused = 0, bsoft = 0, bhard = 0, bpuse = 0, fpuse = 0, fused = 0, fsoft = 0, fhard = 0;
  
  quotas_table.fnClearTable();
  
  $("Plugins", xml).each(function quotas_getplugins(id) {
    plugins = $("Plugins", xml).get(id);
    $("Plugin_Quotas", plugins).each(function quotas_getplugin(idp) {
      quotas = $("Plugin_Quotas", plugins).get(idp);
      $("Quota", quotas).each(function quotas_getquota(idq) {
        quota = $("Quota", quotas).get(idq);
        user = $("User", quota).text().toString();
        bused = parseInt($("ByteUsed", quota).text().toString(), 10);
        bsoft = parseInt($("ByteSoft", quota).text(), 10);
        bhard = parseInt($("ByteHard", quota).text(), 10);
        bpuse = parseInt($("BytePercentUsed", quota).text(), 10);
        fused = parseInt($("FileUsed", quota).text(), 10);
        fsoft = parseInt($("FileSoft", quota).text(), 10);
        fhard = parseInt($("FileHard", quota).text(), 10);
        fpuse = parseInt($("FilePercentUsed", quota).text(), 10);
        
        quotas_table.fnAddData(["<span style=\"display:none;\">" + user + "</span>" + user, "<span style=\"display:none;\">" + bused + "</span>" + formatBytes(bused, xml), "<span style=\"display:none;\">" + bsoft + "</span>" + formatBytes(bsoft, xml), "<span style=\"display:none;\">" + bhard + "</span>" + formatBytes(bhard, xml), "<span style=\"display:none;\">" + bpuse + "</span>" + createBar(bpuse), "<span style=\"display:none;\">" + fused + "</span>" + fused, "<span style=\"display:none;\">" + fsoft + "</span>" + fsoft, "<span style=\"display:none;\">" + fhard + "</span>" + fhard, "<span style=\"display:none;\">" + fpuse + "</span>" + createBar(fpuse)]);
      });
    });
    quotas_show = true;
  });
}

/**
 * fill the plugin block with table structure
 */
function quotas_buildTable() {
  var html = "";
  
  html += "<table id=\"Plugin_QuotasTable\" cellspacing=\"0\">\n";
  html += "  <thead>\n";
  html += "    <tr>\n";
  html += "      <th>" + genlang(3, false, "Quotas") + "</th>\n";
  html += "      <th class=\"right\">" + genlang(4, false, "Quotas") + "</th>\n";
  html += "      <th class=\"right\">" + genlang(5, false, "Quotas") + "</th>\n";
  html += "      <th class=\"right\">" + genlang(6, false, "Quotas") + "</th>\n";
  html += "      <th>" + genlang(7, false, "Quotas") + "</th>\n";
  html += "      <th class=\"right\">" + genlang(8, false, "Quotas") + "</th>\n";
  html += "      <th class=\"right\">" + genlang(9, false, "Quotas") + "</th>\n";
  html += "      <th class=\"right\">" + genlang(10, false, "Quotas") + "</th>\n";
  html += "      <th>" + genlang(11, false, "Quotas") + "</th>\n";
  html += "    </tr>\n";
  html += "  </thead>\n";
  html += "  <tbody>\n";
  html += "  </tbody>\n";
  html += "</table>\n";
  
  $("#Plugin_Quotas").append(html);
  
  quotas_table = $("#Plugin_QuotasTable").dataTable({
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": false,
    "bSort": true,
    "bInfo": false,
    "bProcessing": true,
    "bAutoWidth": false,
    "bStateSave": true,
    "aoColumns": [{
      "sType": 'span-string'
    }, {
      "sType": 'span-number'
    }, {
      "sType": 'span-number'
    }, {
      "sType": 'span-number'
    }, {
      "sType": 'span-number'
    }, {
      "sType": 'span-number'
    }, {
      "sType": 'span-number'
    }, {
      "sType": 'span-number'
    }, {
      "sType": 'span-number'
    }]
  });
}

/**
 * load the xml via ajax
 */
function quotas_request() {
  $.ajax({
    url: "xml.php?plugin=Quotas",
    dataType: "xml",
    error: function quotas_error() {
      $.jGrowl("Error loading XML document for Plugin quotas!");
    },
    success: function quotas_buildblock(xml) {
      quotas_populate(xml);
      if (quotas_show) {
        plugin_translate("Quotas");
        $("#Plugin_Quotas").show();
      }
    }
  });
}

$(document).ready(function quotas_buildpage() {
  $("#footer").before(buildBlock("Quotas", 1, true));
  $("#Plugin_Quotas").css("width", "915px");
  
  quotas_buildTable();
  
  quotas_request();
  
  $("#Reload_QuotasTable").click(function quotas_reload(id) {
    quotas_request();
    $("#DateTime_Quotas").html("(" + genlang(2, true, "Quotas") + ":&nbsp;" + datetime() + ")");
  });
});