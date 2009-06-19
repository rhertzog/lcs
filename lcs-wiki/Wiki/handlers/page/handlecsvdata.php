<?php
/*
* This library is intended to hold functions which deal with data from and to *.csv files.
*
* version 0.3.1 (2005/02/01)
*
* It includes the following functions:
*
* function GetCsvData($file, $separator=",") - Reads all data of a *.csv file.
* function StripHtml($data) - strips html from csv-data.
* function PrintCsvTable($data, $header="off", $tableclass="csvtable") - prints out a html table based on csv-data.
* function ArrayOneDimensionToTwo($onedimension,$itemsperline) - transforms an one-dimensional array into a two-dimensional one.
* function WriteCsvFile($filename,$data,$separator) - writes a two-dimensional array into a csv-file.
*
*/

/**
* Reads all data of a *.csv file.
*
* Based on a given filename every line of a file is read out and put into an array.
* Within this process, everything inside < and > is stripped off.
*
* Based on a given type of separator, every line is exploded and every entry of the line
* put into the second dimension of the array, leading to the following structure:
* array[line][entry]
*
* @package Library
* @subpackage HandleCsvData
*
* @author {@link http://it.php.net/manual/en/function.fgetcsv.php mjwilco at yahoo dot com} (basic code)
* @author {@link http://wikka.jsnx.com/NilsLindenberg Nils Lindenberg} (error-handling and stripping html)
*
* @param ? $file mandatory: name and path of the csv-file
* @param char $separator optional: the separator used for dividing the entries
* standard: ","
*
* @return either a two-dimensional array containing the data of the file, or, in case of an error, FALSE
*
* @todo - excel seem to make different *.csv files?
* - error-handling for $rows = Striphtml
* - detecting if $separator is only one char
*/
function GetCsvData($file, $separator=",")
{
   $endingtest = explode(".", $file);
    if ($endingtest[count($endingtest)-1] != "csv_")  //checks if the ending of the file is *.csv
    {
            echo 'This file does not seem to be an csv-file. Please check the extension of it ('.$file.')'; # i18n
            return FALSE;
    } 
    if (file_exists($file))
    {
        $id = fopen($file, "r");
        if(!$id)
        {
            echo 'The file you specified could not be opend. Please check the reading permission for the file ('.$file.')'; # i18n
            return FALSE;
        }
        while ($data = fgetcsv($id, filesize($file), $separator)) //put each line into its own entry in the $rows array
        {         
            $rows[] = StripHtml($data);
        }
        fclose($id);
        return($rows);
    }
    echo 'The file you specified was not found. Please check you input ('.$file.')'; # i18n
    return FALSE;
}

function StripHtml($data)
{
    if (is_array($data))
    {
        for ($i=0;$i<count($data);$i++)
        {
        while($safe[$i] != strip_tags($data[$i]))
            {$safe[$i] = strip_tags($data[$i]);}
        } 
        return($safe);
    }
    else echo 'The data transferred to StripHtmlinCsvData was no array and could therefore not be handeld! ('.$data.')'; # i18n
    return FALSE;     
}

/**
* Prints a html-table based on the content of a 2-dimensional array.
*
* Based on a given 2-dimensional array, with the structure [line][line-entry] a html table is printed.
* When the parameter $header is set to on, the entries of the first line will be used as columm-headers.
* Standard of this feature is off.
*
* You can determine the look of the table via css. The parameter $tableclass awaits the name of your css entry.
* To determine the defaults, add a csvtable-class to your style-sheet, like the example below:
* .csvtable  { border =1;}
*
*
* @package    Library
* @subpackage    HandleCsvData
*
* @author        {@link http://wikka.jsnx.com/NilsLindenberg Nils Lindenberg}
*
* @param        array $data mandatory: a twodimensional array with the data for the table
* @param        string $header optional: columm-header "on" (anything else will be "off"). Standard is "off".
* @param        string $tableclass optional: css-class for the table. Standard is "csvtable". 
*               
*
* @return        either nothing, or, in case of an error, FALSE.
*
*/
function PrintCsvTable($data, $header="off", $tableclass="csvtable")
{
    if (is_array($data))
    {
        echo "<table class=\"".$tableclass."\">\n";

        //first entry handeld seperate, possible header
    echo "<tr>\n";
    for ($j = 0; $j < count($data[0]); $j++)
    {
        if ($header == 'on') echo "<th>";
        else echo "<td>";
        echo $data[0][$j];
        if ($header == 'on') echo "</th>\n"; //50
        else echo "</td>\n";
    }
    echo "</tr>\n";

    for($i=1;$i<count($data);$i++)
    {
    echo '<tr>';
    for ($j=0; $j < count($data[$i]); $j++)
    {
      echo '<td>';
      echo $data[$i][$j];
      echo "</td>\n";
              }
             echo "</tr>\n";
        }
        echo "</table>\n";
    }
    else echo 'The table could not be drawn because the data given to PrintCsvTable was no array.'; # i18n
}

/**
* Transforms a one-dimensional array into a two-dimensional.
*
* The given one-dimensional array is changed into a two-dimensional array
* with n=$itemsperline items in the second dimension.
*
* @package    Library
* @subpackage    HandleCsvData
*
* @author        {@link http://wikka.jsnx.com/NilsLindenberg Nils Lindenberg}
*
* @param        array $onedimension mandatory: the array which will be transformed
* @param         int $itemsperline mandatory: number of entries in the second dimension
*
* @return        either a two-dimensional array, or, in case of an error, FALSE
*
* @todo        - errorhandling
*
*/
function ArrayOneDimensionToTwo($onedimension,$itemsperline)
{
    $i=0;
     $j=0;
     $k=0;
     for ($i=0;$i<count($onedimension);$i++)
     {
          $twodimensions[$j][$k] = $onedimension[$i];
          $k++;
          if ($k == ($itemsperline)) //if we reach the limit of items per line, start new line
          {
               $k=0;
               $j++;
          }

     }
     return $twodimensions;
}

function WriteCsvFile($filename,$data,$separator)
{
    if (!$filename)
     {
          echo 'You must name a file in which the data should be stored'; # i18n
          return FALSE;
     }
     if (!$data)
     {
          echo 'The file could not be written. There was no data for it'; # i18n
          return FALSE;
     }
     if (!$separator) $separator=",";
     $filename=fopen($filename,'a');
     for ($i=0;$i<count($data);$i++)
     {
          $line='';
          for ($j=0;$j<count($data[$i]);$j++)
          {
               $line .= $data[$i][$j];
               $line .= $separator;
          }
          $line .="\n";
          fputs($filename,$line);
     }
     fclose($filename);
     if ($filename)
     {
          echo 'File written successfully'; # i18n
          return TRUE;
     }
     return FALSE;
}
?> 
