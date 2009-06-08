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
// $Id: phpsysinfo.js 207 2009-05-17 19:28:41Z jacky672 $
//

/*global $, jQuery */

var langxml = [], langcounter = 1, filesystemTable, cookie_language = "", cookie_template = "", plugin_liste = [];
var langarr = [];
/**
 * generate a cookie, if not exist, and add an entry to it<br><br>
 * inspired by <a href="http://www.quirksmode.org/js/cookies.html">http://www.quirksmode.org/js/cookies.html</a>
 * @param {String} name name that holds the value
 * @param {String} value value that needs to be stored
 * @param {Number} days how many days the entry should be valid in the cookie
 */
function createCookie(name, value, days) {
  var date = new Date(), expires = "";
  if (days) {
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toGMTString();
  }
  else {
    expires = "";
  }
  document.cookie = name + "=" + value + expires + "; path=/";
}

/**
 * read a value out of a cookie and return the value<br><br>
 * inspired by <a href="http://www.quirksmode.org/js/cookies.html">http://www.quirksmode.org/js/cookies.html</a>
 * @param {String} name name of the value that should be retrieved
 * @return {String}
 */
function readCookie(name) {
  var nameEQ = "", ca = [], c = '', i = 0;
  nameEQ = name + "=";
  ca = document.cookie.split(';');
  for (i = 0; i < ca.length; i += 1) {
    c = ca[i];
    while (c.charAt(0) === ' ') {
      c = c.substring(1, c.length);
    }
    if (!c.indexOf(nameEQ)) {
      return c.substring(nameEQ.length, c.length);
    }
  }
  return null;
}

/**
 * round a given value to the specified precision, difference to Math.round() is that there
 * will be appended Zeros to the end if the precision is not reached (0.1 gets rounded to 0.100 when precision is set to 3)
 * @param {Number} x value to round
 * @param {Number} n precision
 * @return {String}
 */
function round(x, n) {
  var e = 0, k = "";
  if (n < 1 || n > 14) {
    return 0;
  }
  e = Math.pow(10, n);
  k = (Math.round(x * e) / e).toString();
  if (k.indexOf('.') === -1) {
    k += '.';
  }
  k += e.toString().substring(1);
  return k.substring(0, k.indexOf('.') + n + 1);
}

/**
 * activates a given style and disables the old one in the document
 * @param {String} template template that should be activated
 */
function switchStyle(template) {
  $('link[rel*=style][title]').each(function getTitle(i) {
    if (this.getAttribute('title') === 'PSI_Template') {
      this.setAttribute('href', './templates/' + template + ".css");
    }
  });
  createCookie('template', template, 365);
}

/**
 * load the given translation an translate the entire page<br><br>retrieving the translation is done through a
 * ajax call
 * @private
 * @param {String} lang language for which the translation should be loaded
 * @param {String} plugin if plugin is given, the plugin translation file will be read instead of the main translation file
 * @param {String} plugname internal plugin name
 * @return {jQuery} translation jQuery-Object
 */
function getLanguage(lang, plugin, plugname) {
  var getLangUrl = "";
  if (lang) {
    getLangUrl = 'language/language.php?lang=' + cookie_language;
    if (plugin) {
      getLangUrl += "&plugin=" + plugin;
    }
  }
  else {
    getLangUrl = 'language/language.php';
    if (plugin) {
      getLangUrl += "?plugin=" + plugin;
    }
  }
  $.ajax({
    url: getLangUrl,
    type: 'GET',
    dataType: 'xml',
    timeout: 100000,
    async: false,
    error: function error() {
      $.jGrowl("Error loading language!");
    },
    success: function buildblocks(xml) {
      var idexp;
      langxml[plugname] = xml;
      if (langarr[plugname] === undefined) {
        langarr.push(plugname);
        langarr[plugname] = [];
      }
      $("expression", langxml[plugname]).each(function langstore(id) {
        idexp = $("expression", xml).get(id);
        langarr[plugname][this.getAttribute('id')] = $("exp", idexp).text().toString();
      });
    }
  });
}

/**
 * internal function to get a given translation out of the translation file
 * @param {Number} langID id of the translation expression
 * @param {String} [plugin] name of the plugin
 * @return {String} translation string
 */
function getTranslationString(langId, plugin) {
  var plugname = cookie_language + "_";
  if (plugin === undefined) {
    plugname += "phpSysInfo";
  }
  else {
    plugname += plugin;
  }
  if (langxml[plugname] === undefined) {
    langxml.push(plugname);
    getLanguage(cookie_language, plugin, plugname);
  }
  return langarr[plugname][langId.toString()];
}

/**
 * generate a span tag with an unique identifier to be html valid
 * @param {Number} id translation id in the xml file
 * @param {Boolean} generate generate lang_id in span tag or use given value
 * @param {String} [plugin] name of the plugin for which the tag should be generated
 * @return {String} string which contains generated span tag for translation string
 */
function genlang(id, generate, plugin) {
  var html = "", idString = "", plugname = "";
  if (plugin === undefined) {
    plugname = "";
  }
  else {
    plugname = plugin.toLowerCase();
  }
  if (id < 100) {
    if (id < 10) {
      idString = "00" + id.toString();
    }
    else {
      idString = "0" + id.toString();
    }
  }
  else {
    idString = id.toString();
  }
  if (plugin) {
    idString = "plugin_" + plugname + "_" + idString;
  }
  if (generate) {
    html += "<span id=\"lang_" + idString + "-" + langcounter.toString() + "\">";
    langcounter += 1;
  }
  else {
    html += "<span id=\"lang_" + idString + "\">";
  }
  html += getTranslationString(idString, plugin) + "</span>";
  return html;
}

/**
 * translates all expressions based on the translation xml file<br>
 * translation expressions must be in the format &lt;span id="lang???"&gt;&lt;/span&gt;, where ??? is
 * the number of the translated expression in the xml file<br><br>if a translated expression is not found in the xml
 * file nothing would be translated, so the initial value which is inside the span tag is displayed
 * @param {String} [plugin] name of the plugin
 */
function changeLanguage(plugin) {
  var langId = "", langStr = "";
  $('span[id*=lang_]').each(function translate(i) {
    langId = this.getAttribute('id').substring(5);
    if (langId.indexOf('-') !== -1) {
      langId = langId.substring(0, langId.indexOf('-')); //remove the unique identifier
    }
    langStr = getTranslationString(langId, plugin);
    if (langStr !== undefined) {
      if (langStr.length > 0) {
        this.innerHTML = langStr;
      }
    }
  });
}

/**
 * generate the filesystemTable and activate the dataTables plugin on it
 */
function filesystemtable() {
  var html = "";
  html += "        <table id=\"filesystemTable\" cellspacing=\"0\">\n";
  html += "          <thead>\n";
  html += "            <tr>\n";
  html += "              <th style=\"width:100px;\">" + genlang(31, false) + "</th>\n";
  html += "              <th style=\"width:50px;\">" + genlang(34, false) + "</th>\n";
  html += "              <th style=\"width:120px;\">" + genlang(32, false) + "</th>\n";
  html += "              <th>" + genlang(33, false) + "</th>\n";
  html += "              <th class=\"right\" style=\"width:100px;\">" + genlang(35, true) + "</th>\n";
  html += "              <th class=\"right\" style=\"width:100px;\">" + genlang(36, true) + "</th>\n";
  html += "              <th class=\"right\" style=\"width:100px;\">" + genlang(37, true) + "</th>\n";
  html += "            </tr>\n";
  html += "          </thead>\n";
  html += "          <tfoot>\n";
  html += "            <tr style=\"font-weight : bold\">\n";
  html += "              <td>&nbsp;</td>\n";
  html += "              <td>&nbsp;</td>\n";
  html += "              <td>" + genlang(38, false) + "</td>\n";
  html += "              <td id=\"s_fs_total\"></td>\n";
  html += "              <td class=\"right\"><span id=\"s_fs_tfree\"></span></td>\n";
  html += "              <td class=\"right\"><span id=\"s_fs_tused\"></span></td>\n";
  html += "              <td class=\"right\"><span id=\"s_fs_tsize\"></span></td>\n";
  html += "            </tr>\n";
  html += "          </tfoot>\n";
  html += "          <tbody>\n";
  html += "          </tbody>\n";
  html += "        </table>\n";
  
  $("#filesystem").append(html);
  
  filesystemTable = $("#filesystemTable").dataTable({
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": false,
    "bSort": true,
    "bInfo": false,
    "bProcessing": true,
    "bAutoWidth": false,
    "bStateSave": true,
    "aoColumns": [{
      "sType": 'span-string',
      "sWidth": "100px"
    }, {
      "sType": 'span-string',
      "sWidth": "50px"
    }, {
      "sType": 'span-string',
      "sWidth": "120px"
    }, {
      "sType": 'span-number'
    }, {
      "sType": 'span-number',
      "sWidth": "100px",
      "sClass": "right"
    }, {
      "sType": 'span-number',
      "sWidth": "100px",
      "sClass": "right"
    }, {
      "sType": 'span-number',
      "sWidth": "100px",
      "sClass": "right"
    }]
  });
}

/**
 * fill all errors from the xml in the error div element in the document and show the error icon
 * @param {jQuery} xml phpSysInfo-XML
 */
function populateErrors(xml) {
  var errors = 0, error;
  errors = $("Error", xml).length;
  if (errors > 0) {
    $("#errorlist").empty();
    $("Error", xml).each(function getError(id) {
      error = $("Error", xml).get(id);
      $("#errorlist").append("<b>" + $("Function", error).text().toString() + "</b><br/><br/><pre>" + $("Message", error).text().toString() + "</pre><hr>");
    });
    $("#warn").css("display", "inline");
  }
}

/**
 * show the page
 * @param {jQuery} xml phpSysInfo-XML
 */
function displayPage(xml) {
  var options, versioni = "", showPickListLang = "", showPickListTemplate = "";
  if (cookie_template !== null) {
    $("#template").val(cookie_template);
  }
  if (cookie_language !== null) {
    $("#lang").val(cookie_language);
  }
  $("#loader").hide();
  $('.stripeMe tr:nth-child(even)').addClass('odd');
  $("#container").fadeIn("slow");
  versioni = $("Generation", xml).attr("version").toString();
  $("#version").html(versioni);
  
  $("Options", xml).each(function getOptions(id) {
    options = $("Options", xml).get(id);
    showPickListLang = $("showPickListLang", options).text().toString();
    showPickListTemplate = $("showPickListTemplate", options).text().toString();
    if (showPickListTemplate === 'false') {
      $('#template').hide();
      $('span[id=lang_044]').hide();
    }
    if (showPickListLang === 'false') {
      $('#lang').hide();
      $('span[id=lang_045]').hide();
    }
  });
}

/**
 * format seconds to a better readable statement with days, hours and minutes
 * @param {Number} sec seconds that should be formatted
 * @return {String} html string with no breaking spaces and translation statements
 */
function formatUptime(sec) {
  var txt = "", intMin = 0, intHours = 0, intDays = 0;
  intMin = sec / 60;
  intHours = intMin / 60;
  intDays = Math.floor(intHours / 24);
  intHours = Math.floor(intHours - (intDays * 24));
  intMin = Math.floor(intMin - (intDays * 60 * 24) - (intHours * 60));
  if (intDays) {
    txt += intDays.toString() + "&nbsp;" + genlang(48, false) + "&nbsp;";
  }
  if (intHours) {
    txt += intHours.toString() + "&nbsp;" + genlang(49, false) + "&nbsp;";
  }
  return txt + intMin.toString() + "&nbsp;" + genlang(50, false);
}

/**
 * format a given MHz value to a better readable statement with the right suffix
 * @param {Number} mhertz mhertz value that should be formatted
 * @return {String} html string with no breaking spaces and translation statements
 */
function formatHertz(mhertz) {
  if (mhertz && mhertz < 1000) {
    return mhertz.toString() + "&nbsp;" + genlang(92, true);
  }
  else {
    if (mhertz && mhertz >= 1000) {
      return round(mhertz / 1000, 2) + "&nbsp;" + genlang(93, true);
    }
    else {
      return "";
    }
  }
}

/**
 * format the byte values into a user friendly value with the corespondenting unit expression<br>support is included
 * for binary and decimal output<br>user can specify a constant format for all byte outputs or the output is formated
 * automatically so that every value can be read in a user friendly way
 * @param {Number} bytes value that should be converted in the corespondenting format, which is specified in the config.php
 * @param {jQuery} xml phpSysInfo-XML
 * @return {String} string of the converted bytes with the translated unit expression
 */
function formatBytes(bytes, xml) {
  var byteFormat = "", show = "", options;
  
  $("Options", xml).each(function getByteFormat(id) {
    options = $("Options", xml).get(id);
    byteFormat = $("byteFormat", options).text().toString();
  });
  
  switch (byteFormat) {
  case "PiB":
    show += round(bytes / Math.pow(1024, 5), 2);
    show += "&nbsp;" + genlang(90, true);
    break;
  case "TiB":
    show += round(bytes / Math.pow(1024, 4), 2);
    show += "&nbsp;" + genlang(86, true);
    break;
  case "GiB":
    show += round(bytes / Math.pow(1024, 3), 2);
    show += "&nbsp;" + genlang(87, true);
    break;
  case "MiB":
    show += round(bytes / Math.pow(1024, 2), 2);
    show += "&nbsp;" + genlang(88, true);
    break;
  case "KiB":
    show += round(bytes / Math.pow(1024, 1), 2);
    show += "&nbsp;" + genlang(89, true);
    break;
  case "PB":
    show += round(bytes / Math.pow(1000, 5), 2);
    show += "&nbsp;" + genlang(91, true);
    break;
  case "TB":
    show += round(bytes / Math.pow(1000, 4), 2);
    show += "&nbsp;" + genlang(85, true);
    break;
  case "GB":
    show += round(bytes / Math.pow(1000, 3), 2);
    show += "&nbsp;" + genlang(41, true);
    break;
  case "MB":
    show += round(bytes / Math.pow(1000, 2), 2);
    show += "&nbsp;" + genlang(40, true);
    break;
  case "KB":
    show += round(bytes / Math.pow(1000, 1), 2);
    show += "&nbsp;" + genlang(39, true);
    break;
  case "auto_decimal":
    if (bytes > Math.pow(1000, 5)) {
      show += round(bytes / Math.pow(1000, 5), 2);
      show += "&nbsp;" + genlang(91, true);
    }
    else {
      if (bytes > Math.pow(1000, 4)) {
        show += round(bytes / Math.pow(1000, 4), 2);
        show += "&nbsp;" + genlang(85, true);
      }
      else {
        if (bytes > Math.pow(1000, 3)) {
          show += round(bytes / Math.pow(1000, 3), 2);
          show += "&nbsp;" + genlang(41, true);
        }
        else {
          if (bytes > Math.pow(1000, 2)) {
            show += round(bytes / Math.pow(1000, 2), 2);
            show += "&nbsp;" + genlang(40, true);
          }
          else {
            show += round(bytes / Math.pow(1000, 1), 2);
            show += "&nbsp;" + genlang(39, true);
          }
        }
      }
    }
    break;
  default:
    if (bytes > Math.pow(1024, 5)) {
      show += round(bytes / Math.pow(1024, 5), 2);
      show += "&nbsp;" + genlang(90, true);
    }
    else {
      if (bytes > Math.pow(1024, 4)) {
        show += round(bytes / Math.pow(1024, 4), 2);
        show += "&nbsp;" + genlang(86, true);
      }
      else {
        if (bytes > Math.pow(1024, 3)) {
          show += round(bytes / Math.pow(1024, 3), 2);
          show += "&nbsp;" + genlang(87, true);
        }
        else {
          if (bytes > Math.pow(1024, 2)) {
            show += round(bytes / Math.pow(1024, 2), 2);
            show += "&nbsp;" + genlang(88, true);
          }
          else {
            show += round(bytes / Math.pow(1024, 1), 2);
            show += "&nbsp;" + genlang(89, true);
          }
        }
      }
    }
  }
  return show;
}

/**
 * format a celcius temperature to fahrenheit and also append the right suffix
 * @param {String} degreeC temperature in celvius
 * @param {jQuery} xml phpSysInfo-XML
 * @return {String} html string with no breaking spaces and translation statements
 */
function formatTemp(degreeC, xml) {
  var tempFormat = "", degree = 0, options;
  
  $("Options", xml).each(function getOptions(id) {
    options = $("Options", xml).get(id);
    tempFormat = $("tempFormat", options).text().toString().toLowerCase();
  });
  
  if (isNaN(degreeC)) {
    return "---";
  }
  else {
    degree = parseFloat(degreeC);
  }
  
  switch (tempFormat) {
  case "f":
    return round((((9 * degree) / 5) + 32), 1) + "&nbsp;" + genlang(61, true);
  case "c":
    return round(degree, 1) + "&nbsp;" + genlang(60, true);
  case "c-f":
    return round(degree, 1) + "&nbsp;" + genlang(60, true) + "&nbsp;(" + round((((9 * degree) / 5) + 32), 1) + "&nbsp;" + genlang(61, true) + ")";
  case "f-c":
    return round((((9 * degree) / 5) + 32), 1) + "&nbsp;" + genlang(61, true) + "&nbsp;(" + round(degree, 1) + "&nbsp;" + genlang(60, true) + ")";
  }
}

/**
 * create a visual HTML bar from a given size, the layout of that bar can be costumized through the bar css-class
 * @param {Number} size
 * @return {String} HTML string which contains the full layout of the bar
 */
function createBar(size) {
  return "<div class=\"bar\" style=\"float:left; width: " + size + "px;\">&nbsp;</div>&nbsp;" + size + "%";
}

/**
 * read device information for the hardware block for the given type and append the resulting list to the given table
 * @param {String} table table to which the information should be appended
 * @param {String} type type of the devices that the list should be generated for
 * @param {jQuery} xml phpSysInfo-XML
 */
function popDevices(table, type, xml) {
  var text = "", alldev, dev, capacity = 0;
  $(type, xml).each(function getDevices(id) {
    alldev = $(type, xml).get(id);
    $("Device", alldev).each(function getDevice(id) {
      dev = $("Device", alldev).get(id);
      text += "<li>" + $("Name", dev).text().toString();
      capacity = $("Capacity", dev).length;
      if (capacity > 0) {
        text += "&nbsp;(" + formatBytes(parseInt($("Capacity", dev).text().toString(), 10), xml) + ")";
      }
      text += "</li>";
    });
  });
  $("#" + table).empty();
  if (text === "") {
    $("#" + table).append("<tr><td><ul style=\"margin-left:10px;\"><li>" + genlang(42, false) + "</li></ul></td></tr>");
  }
  else {
    $("#" + table).append("<tr><td><ul style=\"margin-left:10px;\">" + text + "</ul></td></tr>");
  }
}

/**
 * (re)fill the vitals block with the values from the given xml
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshVitals(xml) {
  var vital, hostname = "", ip = "", kernel = "", distro = "", icon = "", uptime = "", users = 0, loadavg = "", cpuload = 0;
  $("Vitals", xml).each(function getVitals(id) {
    vital = $("Vitals", xml).get(id);
    hostname = $("Hostname", vital).text().toString();
    ip = $("IPAddr", vital).text().toString();
    kernel = $("Kernel", vital).text().toString();
    distro = $("Distro", vital).text().toString();
    icon = $("Distroicon", vital).text().toString();
    uptime = formatUptime(parseInt($("Uptime", vital).text().toString(), 10));
    users = parseInt($("Users", vital).text().toString(), 10);
    loadavg = $("LoadAvg", vital).text().toString();
    if ($("CPULoad", vital).length === 1) {
      cpuload = parseInt($("CPULoad", vital).text().toString(), 10);
      loadavg = loadavg + "<br/>" + createBar(cpuload);
    }
    document.title = "System information: " + hostname + " (" + ip + ")";
    $("#s_hostname_title").html(hostname);
    $("#s_ip_title").html(ip);
    $("#s_hostname").html(hostname);
    $("#s_ip").html(ip);
    $("#s_kernel").html(kernel);
    $("#s_distro").html("<img src='./gfx/images/" + icon + "' alt='Icon' height='16' width='16' style='vertical-align:middle;' />&nbsp;" + distro);
    $("#s_uptime").html(uptime);
    $("#s_users").html(users);
    $("#s_loadavg").html(loadavg);
  });
}

/**
 * (re)fill the hardware block with the values from the given xml
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshHardware(xml) {
  var hardware, cpu, num = 0, model = "", speed = 0, bus = 0, cache = 0, bogo = 0;
  $("Hardware", xml).each(function getHardware(id) {
    hardware = $("Hardware", xml).get(id);
    $("CPU", hardware).each(function getCpu(id) {
      cpu = $("CPU", hardware).get(id);
      num = parseInt($("Number", cpu).text().toString(), 10);
      model = $("Model", cpu).text().toString();
      speed = parseInt($("Cpuspeed", cpu).text().toString(), 10);
      bus = parseInt($("Busspeed", cpu).text().toString(), 10);
      cache = parseInt($("Cache", cpu).text().toString(), 10);
      bogo = parseInt($("Bogomips", cpu).text().toString(), 10);
      $("#s_num").html(num);
      $("#s_model").html(model);
      $("#s_speed").html(speed.toString() === "NaN" ? 0 : formatHertz(speed));
      $("#s_bus").html(bus.toString() === "NaN" ? 0 : formatHertz(bus));
      $("#s_cache").html(cache.toString() === "NaN" ? 0 : formatBytes(cache));
      $("#s_bogo").html(bogo.toString() === "NaN" ? 0 : bogo.toString());
    });
    popDevices('pciTable', 'PCI', hardware);
    popDevices('ideTable', 'IDE', hardware);
    popDevices('scsiTable', 'SCSI', hardware);
    popDevices('usbTable', 'USB', hardware);
  });
}

/**
 *(re)fill the network block with the values from the given xml
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshNetwork(xml) {
  var network, device, name = "", rx = 0, tx = 0, errors = 0, drops = 0;
  $("#tbody_network").empty();
  $("Network", xml).each(function getDevices(id) {
    network = $("Network", xml).get(id);
    $("NetDevice", network).each(function getDevice(did) {
      device = $("NetDevice", network).get(did);
      name = $("Name", device).text().toString();
      rx = parseInt($("RxBytes", device).text().toString(), 10);
      tx = parseInt($("TxBytes", device).text().toString(), 10);
      errors = parseInt($("Err", device).text().toString(), 10);
      drops = parseInt($("Drops", device).text().toString(), 10);
      $("#tbody_network").append("<tr><td>" + name + "</td><td class=\"right\">" + formatBytes(rx, xml) + "</td><td class=\"right\">" + formatBytes(tx, xml) + "</td><td class=\"right\">" + errors.toString() + "/" + drops.toString() + "</td></tr>");
    });
  });
}

/**
 * (re)fill the memory block with the values from the given xml
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshMemory(xml) {
  var vital, alldev, dev, devname, name = "", free = 0, total = 0, used = 0, percent = 0, app = 0, appp = 0, buff = 0, buffp = 0, cached = 0, cachedp = 0;
  $("#tbody_memory").empty();
  $("Memory", xml).each(function getMemory(id) {
    vital = $("Memory", xml).get(id);
    free = parseInt($("Free", vital).text().toString(), 10);
    used = parseInt($("Used", vital).text(), 10);
    total = parseInt($("Total", vital).text(), 10);
    percent = parseInt($("Percent", vital).text(), 10);
    $("#tbody_memory").append("<tr><td style=\"width:200px;\">" + genlang(28, false) + "</td><td style=\"width:285px;\">" + createBar(percent) + "</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(free, xml) + "</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(used, xml) + "</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(total, xml) + "</td></tr>");
    
    if ($("App", vital).length > 0) {
      app = parseInt($("App", vital).text().toString(), 10);
      appp = parseInt($("AppPercent", vital).text().toString(), 10);
      buff = parseInt($("Buffers", vital).text().toString(), 10);
      buffp = parseInt($("BuffersPercent", vital).text().toString(), 10);
      cached = parseInt($("Cached", vital).text().toString(), 10);
      cachedp = parseInt($("CachedPercent", vital).text().toString(), 10);
      $("#tbody_memory").append("<tr><td style=\"width:184px;padding-left:26px;\">" + genlang(64, false) + "</td><td style=\"width:285px;\">" + createBar(appp) + "</td><td class=\"right\" style=\"width:100px;\">&nbsp;</td><td class=\"right\" style=\"width:100px\">" + formatBytes(app, xml) + "</td><td class=\"right\" style=\"width:100px;\">&nbsp;</td></tr>");
      $("#tbody_memory").append("<tr><td style=\"width:184px;padding-left:26px;\">" + genlang(65, false) + "</td><td style=\"width:285px\">" + createBar(buffp) + "</td><td class=\"rigth\" style=\"width:100px;\">&nbsp;</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(buff, xml) + "</td><td class=\"right\" style=\"width:100px;\">&nbsp;</td></tr>");
      $("#tbody_memory").append("<tr><td style=\"width:184px;padding-left:26px;\">" + genlang(66, false) + "</td><td style=\"width:285px;\">" + createBar(cachedp) + "</td><td class=\"right\" style=\"width:100px;\">&nbsp;</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(cached, xml) + "</td><td class=\"right\" style=\"width:100px;\">&nbsp;</td></tr>");
    }
  });
  
  $("Swap", xml).each(function getSwap(id) {
    vital = $("Swap", xml).get(id);
    if ($("Total", vital).length > 0) {
      free = parseInt($("Free", vital).text().toString(), 10);
      used = parseInt($("Used", vital).text().toString(), 10);
      total = parseInt($("Total", vital).text().toString(), 10);
      percent = parseInt($("Percent", vital).text().toString(), 10);
      $("#tbody_memory").append("<tr><td style=\"width:200px;\">" + genlang(29, false) + "</td><td style=\"width:285px;\">" + createBar(percent) + "</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(free, xml) + "</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(used, xml) + "</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(total, xml) + "</td></tr>");
      
      if ($("Swapdevices", xml).length > 0) {
        $("Swapdevices", xml).each(function getDevices(id) {
          alldev = $("Swapdevices", xml).get(id);
          $("Mount", alldev).each(function getDevice(id) {
            dev = $("Mount", alldev).get(id);
            free = parseInt($("Free", dev).text().toString(), 10);
            used = parseInt($("Used", dev).text().toString(), 10);
            total = parseInt($("Size", dev).text().toString(), 10);
            percent = parseInt($("Percent", dev).text().toString(), 10);
            $("Device", dev).each(function getName(id) {
              devname = $("Device", dev).get(id);
              name = $("Name", devname).text().toString();
            });
            $("#tbody_memory").append("<tr><td style=\"width:184px;padding-left:26px;\">" + name + "</td><td style=\"width:285px;\">" + createBar(percent) + "</td><td class=\"right\" style=\"width:100px\">" + formatBytes(free, xml) + "</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(used, xml) + "</td><td class=\"right\" style=\"width:100px;\">" + formatBytes(total, xml) + "</td></tr>");
          });
        });
      }
    }
  });
}

/**
 * (re)fill the filesystems block with the values from the given xml<br><br>
 * appends the filesystems (each in a row) to the filesystem table in the tbody<br>before the rows are inserted the entire
 * tbody is cleared
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshFilesystems(xml) {
  var total_usage = 0, total_used = 0, total_free = 0, total_size = 0, inodes_text = "", filesystem, mount, dev, name = "", mpid = 0, mpoint = "", type = "", percent = 0, free = 0, used = 0, size = 0, inodes = 0;
  
  filesystemTable.fnClearTable();
  
  $("FileSystem", xml).each(function getFilesystems(id) {
    filesystem = $("FileSystem", xml).get(id);
    $("Mount", filesystem).each(function getMountPoint(mid) {
      mount = $("Mount", filesystem).get(mid);
      $("Device", mount).each(function getName(did) {
        dev = $("Device", mount).get(did);
        name = $("Name", dev).text().toString();
      });
      mpid = parseInt($("MountPointID", mount).text().toString(), 10);
      mpoint = $("MountPoint", mount).text().toString();
      type = $("Type", mount).text().toString();
      percent = parseInt($("Percent", mount).text().toString(), 10);
      free = parseInt($("Free", mount).text().toString(), 10);
      used = parseInt($("Used", mount).text().toString(), 10);
      size = parseInt($("Size", mount).text().toString(), 10);
      inodes = parseInt($("Inodes", mount).text().toString(), 10);
      
      if (mpoint === "") {
        mpoint = mpid;
      }
      if (!isNaN(inodes.toString())) {
        inodes_text = "<span style=\"font-style:italic\">&nbsp;(" + inodes.toString() + "%)</span>";
      }
      filesystemTable.fnAddData(["<span style=\"display:none;\">" + mpoint + "</span>" + mpoint, "<span style=\"display:none;\">" + type + "</span>" + type, "<span style=\"display:none;\">" + name + "</span>" + name, "<span style=\"display:none;\">" + percent.toString() + "</span>" + createBar(percent) + inodes_text, "<span style=\"display:none;\">" + free.toString() + "</span>" + formatBytes(free, xml), "<span style=\"display:none;\">" + used.toString() + "</span>" + formatBytes(used, xml), "<span style=\"display:none;\">" + size.toString() + "</span>" + formatBytes(size, xml)]);
      
      total_used += used;
      total_free += free;
      total_size += size;
      total_usage = round((total_used / total_size) * 100, 2);
    });
    
    $("#s_fs_total").html(createBar(total_usage));
    $("#s_fs_tfree").html(formatBytes(total_free, xml));
    $("#s_fs_tused").html(formatBytes(total_used, xml));
    $("#s_fs_tsize").html(formatBytes(total_size, xml));
  });
}

/**
 * (re)fill the temperature block with the values from the given xml<br><br>
 * build the block content for the temperature block, this includes normal temperature information in the XML
 * and also the HDDTemp information, if there are no information in both subtrees the entire table will be removed
 * to avoid HTML warnings
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshTemp(xml) {
  var temp, item, label = "", value = "", model = "", limit = "", values = false;
  $("#tempTable").empty();
  if ($("Temperature", xml).length > 0) {
    $("Temperature", xml).each(function getTemperatures(id) {
      temp = $("Temperature", xml).get(id);
      $("Item", temp).each(function getTemperature(iid) {
        item = $("Item", temp).get(iid);
        label = $("Label", item).text().toString();
        value = $("Value", item).text().toString();
        limit = $("Limit", item).text().toString();
        value = value.replace(/\+/g, "");
        limit = limit.replace(/\+/g, "");
        $("#tempTable").append("<tr><td>" + label + "</td><td class=\"right\">" + formatTemp(value, xml) + "</td><td class=\"right\">" + formatTemp(limit, xml) + "</td></tr>");
        values = true;
      });
    });
  }
  if ($("HDDTemp", xml).length > 0) {
    $("HDDTemp", xml).each(function getTemperatures(id) {
      temp = $("HDDTemp", xml).get(id);
      $("Item", temp).each(function getTemperature(iid) {
        item = $("Item", temp).get(iid);
        label = $("Label", item).text().toString();
        value = $("Value", item).text().toString();
        model = $("Model", item).text().toString();
        if (value !== 'NA') {
          $("#tempTable").append("<tr><td>" + model + "</td><td class=\"right\">" + formatTemp(value, xml) + "</td><td>&nbsp;</td></tr>");
          values = true;
        }
      });
    });
  }
  if (values) {
    $("#temp").show();
  }
  else {
    $("#temp").remove();
  }
}

/**
 * (re)fill the voltage block with the values from the given xml<br><br>
 * build the voltage information into a separate block, if there is no voltage information available the
 * entire table will be removed to avoid HTML warnings
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshVoltage(xml) {
  var voltage, item, label = "", value = "", max = "", min = "";
  $("#voltageTable").empty();
  if ($("Voltage", xml).length > 0) {
    $("Voltage", xml).each(function getVoltages(id) {
      voltage = $("Voltage", xml).get(id);
      $("Item", voltage).each(function getVoltage(iid) {
        item = $("Item", voltage).get(iid);
        label = $("Label", item).text().toString();
        value = $("Value", item).text().toString();
        max = $("Max", item).text().toString();
        min = $("Min", item).text().toString();
        $("#voltageTable").append("<tr><td>" + label + "</td><td class=\"right\">" + round(value, 2) + "&nbsp;" + genlang(62, true) + "</td><td class=\"right\">" + round(min, 2) + "&nbsp;" + genlang(62, true) + "</td><td class=\"right\">" + round(max, 2) + "&nbsp;" + genlang(62, true) + "</td></tr>");
      });
    });
    $("#voltage").show();
  }
  else {
    $("#voltage").remove();
  }
}

/**
 * (re)fill the fan block with the values from the given xml<br><br>
 * build the fan information into a separate block, if there is no fan information available the
 * entire table will be removed to avoid HTML warnings
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshFan(xml) {
  var fan, item, label = "", value = "", min = "";
  $("#fanTable").empty();
  if ($("Fans", xml).length > 0) {
    $("Fans", xml).each(function getFans(id) {
      fan = $("Fans", xml).get(id);
      $("Item", fan).each(function getFan(iid) {
        item = $("Item", fan).get(iid);
        label = $("Label", item).text().toString();
        value = $("Value", item).text().toString();
        min = $("Min", item).text().toString();
        $("#fanTable").append("<tr><td>" + label + "</td><td class=\"right\">" + value + "&nbsp;" + genlang(63, true) + "</td><td class=\"right\">" + min + "&nbsp;" + genlang(63, true) + "</td></tr>");
      });
    });
    $("#fan").show();
  }
  else {
    $("#fan").remove();
  }
}

/**
 * (re)fill the ups block with the values from the given xml<br><br>
 * build the ups information into a separate block, if there is no ups information available the
 * entire table will be removed to avoid HTML warnings
 * @param {jQuery} xml phpSysInfo-XML
 */
function refreshUps(xml) {
  var upses, ups, name = "", model = "", mode = "", start_time = "", upsstatus = "", temperature = "", outages_count = "", last_outage = "", last_outage_finish = "", line_voltage = "", load_percent = "", battery_voltage = "", battery_charge_percent = "", time_left_minutes = "";
  $("#upsTable").empty();
  if ($("UPSinfo", xml).length > 0) {
    $("UPSinfo", xml).each(function getUpses(id) {
      upses = $("UPSinfo", xml).get(id);
      $("Ups", upses).each(function getUps(did) {
        ups = $("Ups", upses).get(did);
        name = $("Name", ups).text().toString();
        model = $("Model", ups).text().toString();
        mode = $("Mode", ups).text().toString();
        start_time = $("StartTime", ups).text().toString();
        upsstatus = $("Status", ups).text().toString();
        temperature = $("UPSTemperature", ups).text().toString();
        outages_count = $("OutagesCount", ups).text().toString();
        last_outage = $("LastOutage", ups).text().toString();
        last_outage_finish = $("LastOutageFinish", ups).text().toString();
        line_voltage = $("LineVoltage", ups).text().toString();
        load_percent = parseInt($("LoadPercent", ups).text().toString(), 10);
        battery_voltage = $("BatteryVoltage", ups).text().toString();
        battery_charge_percent = parseInt($("BatteryChargePercent", ups).text().toString(), 10);
        time_left_minutes = $("TimeLeftMinutes", ups).text().toString();
        
        $("#upsTable").append('<tr><th colspan="2" style="text-align: center"><strong>' + name + ' (' + mode + ')</strong></th></tr>');
        $("#upsTable").append('<tr><td style="width:160px">' + genlang(70, false) + '</td><td>' + model + '</td></tr>');
        $("#upsTable").append('<tr><td style="width:160px">' + genlang(72, false) + '</td><td>' + start_time + '</td></tr>');
        $("#upsTable").append('<tr><td style="width:160px">' + genlang(73, false) + '</td><td>' + upsstatus + '</td></tr>');
        if (temperature !== '') {
          $("#upsTable").append('<tr><td style="width:160px">' + genlang(84, false) + '</td><td>' + temperature + '</td></tr>');
        }
        if (outages_count !== '') {
          $("#upsTable").append('<tr><td style="width:160px">' + genlang(74, false) + '</td><td>' + outages_count + '</td></tr>');
        }
        if (last_outage !== '') {
          $("#upsTable").append('<tr><td style="width:160px">' + genlang(75, false) + '</td><td>' + last_outage + '</td></tr>');
        }
        if (last_outage_finish !== '') {
          $("#upsTable").append('<tr><td style="width:160px">' + genlang(76, false) + '</td><td>' + last_outage_finish + '</td></tr>');
        }
        if (line_voltage !== '') {
          $("#upsTable").append('<tr><td style="width:160px">' + genlang(77, false) + '</td><td>' + line_voltage + '&nbsp;' + genlang(82, true) + '</td></tr>');
        }
        if (load_percent !== '') {
          $("#upsTable").append('<tr><td style="width:160px">' + genlang(78, false) + '</td><td>' + createBar(load_percent) + '</td></tr>');
        }
        if (battery_voltage !== '') {
          $("#upsTable").append('<tr><td style="width:160px">' + genlang(79, false) + '</td><td>' + battery_voltage + '&nbsp;' + genlang(82, true) + '</td></tr>');
        }
        $("#upsTable").append('<tr><td style="width:160px">' + genlang(80, false) + '</td><td>' + createBar(battery_charge_percent) + '</td></tr>');
        $("#upsTable").append('<tr><td style="width:160px">' + genlang(81, false) + '</td><td>' + time_left_minutes + '&nbsp;' + genlang(83, false) + '</td></tr>');
      });
    });
    $("#ups").show();
  }
  else {
    $("#ups").remove();
  }
}

/**
 * reload the page, this means all values are refreshed, except the plugins
 */
function reload() {
  $.ajax({
    url: 'xml.php',
    dataType: 'xml',
    error: function error() {
      $.jGrowl("Error loading XML document!");
    },
    success: function buildblocks(xml) {
      refreshVitals(xml);
      refreshNetwork(xml);
      refreshHardware(xml);
      refreshMemory(xml);
      refreshFilesystems(xml);
      refreshVoltage(xml);
      refreshFan(xml);
      refreshTemp(xml);
      refreshUps(xml);
      
      $('.stripeMe tr:nth-child(even)').addClass('odd');
      langcounter = 1;
    }
  });
}

/**
 * set a reload timer for the page
 * @param {jQuery} xml phpSysInfo-XML
 */
function settimer(xml) {
  var options, refresh = "";
  $("Options", xml).each(function getRefreshTime(id) {
    options = $("Options", xml).get(id);
    refresh = $("refresh", options).text().toString();
    if (refresh !== '0') {
      $.timer(refresh, reload);
    }
  });
}

cookie_language = readCookie("language");
cookie_template = readCookie("template");

if (cookie_template) {
  switchStyle(cookie_template);
}

$(document).ready(function buildpage() {
  filesystemtable();
  
  $.ajax({
    url: 'xml.php',
    dataType: 'xml',
    error: function error() {
      $.jGrowl("Error loading XML document!", {
        sticky: true
      });
    },
    success: function buildblocks(xml) {
      populateErrors(xml);
      
      refreshVitals(xml);
      refreshHardware(xml);
      refreshNetwork(xml);
      refreshMemory(xml);
      refreshFilesystems(xml);
      refreshTemp(xml);
      refreshVoltage(xml);
      refreshFan(xml);
      refreshUps(xml);
      
      changeLanguage();
      displayPage(xml);
      settimer(xml);
      
      $('.stripeMe tr:nth-child(even)').addClass('odd');
      langcounter = 1;
    }
  });
  
  $("#errors").nyroModal();
  
  $("#lang").change(function changeLang() {
    var language = "", i = 0;
    language = $("#lang").val().toString();
    createCookie('language', language, 365);
    cookie_language = readCookie('language');
    changeLanguage();
    for (i = 0; i < plugin_liste.length; i += 1) {
      changeLanguage(plugin_liste[i]);
    }
    return false;
  });
  
  $("#template").change(function changeTemplate() {
    switchStyle($("#template").val().toString());
    return false;
  });
  
  $("#sPci").click(function pciDown() {
    $("#pciTable").slideDown("slow");
    $("#sPci").hide();
    $("#hPci").show();
  });
  $("#hPci").click(function pciUp() {
    $("#pciTable").slideUp("slow");
    $("#hPci").hide();
    $("#sPci").show();
  });
  
  $("#sIde").click(function ideDown() {
    $("#ideTable").slideDown("slow");
    $("#sIde").hide();
    $("#hIde").show();
  });
  $("#hIde").click(function ideUp() {
    $("#ideTable").slideUp("slow");
    $("#hIde").hide();
    $("#sIde").show();
  });
  
  $("#sScsi").click(function scsiDown() {
    $("#scsiTable").slideDown("slow");
    $("#sScsi").hide();
    $("#hScsi").show();
  });
  $("#hScsi").click(function scsiUp() {
    $("#scsiTable").slideUp("slow");
    $("#hScsi").hide();
    $("#sScsi").show();
  });
  
  $("#sUsb").click(function usbDown() {
    $("#usbTable").slideDown("slow");
    $("#sUsb").hide();
    $("#hUsb").show();
  });
  $("#hUsb").click(function usbUp() {
    $("#usbTable").slideUp("slow");
    $("#hUsb").hide();
    $("#sUsb").show();
  });
});

jQuery.fn.dataTableExt.oSort['span-string-asc'] = function sortStringAsc(a, b) {
  var x = "", y = "";
  x = a.substring(a.indexOf(">") + 1, a.indexOf("</"));
  y = b.substring(b.indexOf(">") + 1, b.indexOf("</"));
  return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};

jQuery.fn.dataTableExt.oSort['span-string-desc'] = function sortStringDesc(a, b) {
  var x = "", y = "";
  x = a.substring(a.indexOf(">") + 1, a.indexOf("</"));
  y = b.substring(b.indexOf(">") + 1, b.indexOf("</"));
  return ((x < y) ? 1 : ((x > y) ? -1 : 0));
};

jQuery.fn.dataTableExt.oSort['span-number-asc'] = function sortNumberAsc(a, b) {
  var x = 0, y = 0;
  x = parseInt(a.substring(a.indexOf(">") + 1, a.indexOf("</")), 10);
  y = parseInt(b.substring(b.indexOf(">") + 1, b.indexOf("</")), 10);
  return ((x < y) ? -1 : ((x > y) ? 1 : 0));
};

jQuery.fn.dataTableExt.oSort['span-number-desc'] = function sortNumberDesc(a, b) {
  var x = 0, y = 0;
  x = parseInt(a.substring(a.indexOf(">") + 1, a.indexOf("</")), 10);
  y = parseInt(b.substring(b.indexOf(">") + 1, b.indexOf("</")), 10);
  return ((x < y) ? 1 : ((x > y) ? -1 : 0));
};

/**
 * generate the block element for a specific plugin that is available
 * @param {String} plugin name of the plugin
 * @param {Number} translationid id of the translated headline in the plugin translation file
 * @param {Boolean} reload controls if a reload button should be appended to the headline
 * @return {String} HTML string which contains the full layout of the block
 */
function buildBlock(plugin, translationid, reload) {
  var block = "", reloadpic = "";
  if (reload) {
    reloadpic = "<img id=\"Reload_" + plugin + "Table\" src=\"./gfx/reload.png\" alt=\"reload\" style=\"vertical-align:middle;border=0px;\" />&nbsp;";
  }
  block += "      <div id=\"Plugin_" + plugin + "\" style=\"display:none;float:left;margin:10px 0pt 0pt 10px;padding: 1px;\">\n";
  block += "        <h2>" + reloadpic + genlang(translationid, false, plugin) + "</h2>\n<span id=\"DateTime_" + plugin + "\" style=\"margin-left:10px;\"></span>";
  block += "      </div>\n";
  return block;
}

/**
 * translate a plugin and add this plugin to the internal plugin-list, this is only needed once and shouldn't be called more than once
 * @param {String} plugin name of the plugin  that should be translated
 */
function plugin_translate(plugin) {
  plugin_liste.push(plugin);
  changeLanguage(plugin);
}

/**
 * generate a formatted datetime string of the current datetime
 * @return {String} formatted datetime string
 */
function datetime() {
  var date, day = 0, month = 0, year = 0, hour = 0, minute = 0, days = "", months = "", years = "", hours = "", minutes = "";
  date = new Date();
  day = date.getDate();
  month = date.getMonth() + 1;
  year = date.getFullYear() + 1900;
  hour = date.getHours();
  minute = date.getMinutes();
  
  // format values smaller that 10 with a leading 0
  days = (day < 10) ? "0" + day.toString() : day.toString();
  months = (month < 10) ? "0" + month.toString() : month.toString();
  years = (year < 1000) ? year.toString() : year.toString();
  minutes = (minute < 10) ? "0" + minute.toString() : minute.toString();
  hours = (hour < 10) ? "0" + hour.toString() : hour.toString();
  
  return days + "." + months + "." + years + "&nbsp;" + hours + ":" + minutes;
}

/**
 * insert dynamically a js script file into the website
 * @param {String} name name of the script that should be included
 */
function appendjs(name) {
  var scrptE, hdEl;
  scrptE = document.createElement("script");
  hdEl = document.getElementsByTagName("head")[0];
  scrptE.setAttribute("src", name);
  scrptE.setAttribute("type", "text/javascript");
  hdEl.appendChild(scrptE);
}

/**
 * insert dynamically a css file into the website
 * @param {String} name name of the css file that should be included
 */
function appendcss(name) {
  var scrptE, hdEl;
  scrptE = document.createElement("link");
  hdEl = document.getElementsByTagName("head")[0];
  scrptE.setAttribute("type", "text/css");
  scrptE.setAttribute("rel", "stylesheet");
  scrptE.setAttribute("href", name);
  hdEl.appendChild(scrptE);
}
