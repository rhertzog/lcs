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
// $Id: PS.js 178 2009-03-31 13:52:43Z bigmichi1 $
//

/*global $, jQuery, buildBlock, datetime, plugin_translate, appendcss, appendjs, createBar, genlang */

var ps_show = false;

appendjs("./js.php?name=jquery.treeTable&plugin=ps");
appendcss("./plugins/ps/css/jquery.treeTable.css");

/**
 * build the table where content is inserted
 * @param {jQuery} xml plugin-XML
 */
function ps_buildTable(xml) {
  var html = "", ps, pid = "", ppid = "", name = "", percent = 0;
  
  html += "<tr>\n";
  html += " <td>\n";
  html += "  <table id=\"Plugin_PSTree\">\n";
  html += "   <thead>\n";
  html += "    <th>" + genlang(3, false, "PS") + "</th>\n";
  html += "    <th style=\"width:80px;\">" + genlang(4, false, "PS") + "</span></th>\n";
  html += "    <th style=\"width:80px;\">" + genlang(5, false, "PS") + "</th>\n";
  html += "    <th style=\"width:110px;\">" + genlang(6, false, "PS") + "</th>\n";
  html += "   </thead>\n";
  html += "   <tbody>\n";
  html += "    <tr id=\"node-0\">\n";
  html += "     <td>[ROOT]</td>\n";
  html += "     <td>&nbsp;</td>\n";
  html += "     <td>&nbsp;</td>\n";
  html += "     <td>&nbsp;</td>\n";
  html += "    </tr>\n";
  
  $("Process", xml).each(function ps_getprocess(id) {
    ps = $("Process", xml).get(id);
    pid = $("PID", ps).text().toString();
    ppid = $("PPID", ps).text().toString();
    name = $("Name", ps).text().toString();
    percent = parseInt($("MemoryUsage", ps).text().toString(), 10);
    
    html += "    <tr id=\"node-" + pid + "\" class=\"child-of-node-" + ppid + "\">\n";
    html += "     <td>" + name + "</td>\n";
    html += "     <td>" + pid + "</td>\n";
    html += "     <td>" + ppid + "</td>\n";
    html += "     <td>" + createBar(percent) + "</td>\n";
    html += "    </tr>\n";
  });
  
  html += "   </tbody>\n";
  html += "  </table>\n";
  html += " </td>";
  html += "</tr>";
  return html;
}

/**
 * fill the plugin block with data from xml
 * @param {jQuery} xml plugin-XML
 */
function ps_populate(xml) {
  var plugins, ps;
  
  $("#Plugin_PSTable").empty();
  $("Plugins", xml).each(function ps_getplugins(id) {
    plugins = $("Plugins", xml).get(id);
    $("Plugin_PS", plugins).each(function ps_getplugin(id) {
      ps = $("Plugin_PS", plugins).get(id);
      $("#Plugin_PSTable").append(ps_buildTable(ps));
      $("#Plugin_PSTree").treeTable();
      ps_show = true;
    });
  });
}

/**
 * load the xml via ajax
 */
function ps_request() {
  $.ajax({
    url: "xml.php?plugin=PS",
    dataType: "xml",
    error: function ps_error() {
      $.jGrowl("Error loading XML document for Plugin PS!");
    },
    success: function ps_buildblock(xml) {
      ps_populate(xml);
      if (ps_show) {
        plugin_translate("PS");
        $("#Plugin_PS").show();
      }
    }
  });
}

$(document).ready(function ps_buildpage() {
  var html = "";
  
  $("#footer").before(buildBlock("PS", 1, true));
  html += "        <table id=\"Plugin_PSTable\" cellspacing=\"0\">\n";
  html += "        </table>\n";
  $("#Plugin_PS").append(html);
  $("#Plugin_PS").css("width", "915px");
  
  ps_request();
  
  $("#Reload_PSTable").click(function ps_reload(id) {
    ps_request();
    $("#DateTime_PS").html("(" + genlang(2, true, "PS") + ":&nbsp;" + datetime() + ")");
  });
});
