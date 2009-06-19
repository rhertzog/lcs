<?php

if (!isset($order))
{
   $order = $default->default_sort_column;
}
if (!isset($sortname) or strlen($sortname) > 4)
{
   $sortname = $default->default_sort_order;
}

if (!isset($sortver) or strlen($sortver) > 24)
{
   $sortver = "ASC, minor_revision ASC";
}
if (!isset($sortcheckedout) or strlen($sortcheckedout) > 4)
{
   $sortcheckedout = $default->default_sort_order;
}
if (!isset($sortfilename) or strlen($sortfilename) > 4)
{
   $sortfilename = $default->default_sort_order;
}
if (!isset($sortsize) or strlen($sortsize) > 4)
{
   $sortsize = $default->default_sort_order;
}
if (!isset($sortposted) or strlen($sortposted) > 4)
{
   $sortposted = $default->default_sort_order;
}
if (!isset($sortupdator) or strlen($sortupdator) > 4)
{
   $sortupdator = $default->default_sort_order;
}
if (!isset($sortmod) or strlen($sortmod) > 4)
{
   $sortmod = $default->default_sort_order;
}
if (!isset($sort) or strlen($sort) > 4)
{
   $sort = $default->default_sort_order;
}

switch ($order)
{
   case "id":
      $sortorder = 'id';
      $sort = $sortid;
      break;
   case "name":
      $sortorder = 'sortname';
      $sort = $sortname;
      break;
   case "major_minor_revision":
      $sortorder = 'sortver';
      $sort = $sortver;
      break;
   case "filename" :
      $sortorder = 'sortfilename';
      $sort = $sortfilename;
      break;
   case "f_size" :
      $sortorder = 'sortsize';
      $sort = $sortsize;
      break;
   case "updatorid" :
      $sortorder = 'sortupdator';
      $sort = $sortupdator;
      break;
   case "creatorid" :
      $sortorder = 'sortposted';
      $sort = $sortposted;
      break;
   case "smodified" :
      $sortorder = 'sortmod';
      $sort = $sortmod;
      break;
   case "checked_out":
      $sortorder = 'sortcheckedout';
      $sort = $sortcheckedout;
      break;
   default:
      $order= "name";
      $sortorder= "sortname";
      $sort = "ASC";
      break;
}
?>
