<?php
class CommonFunctions
{

    /**
     * Find a system program, do also path checking when not running on WINNT
     * on WINNT we simply return the name with the exe extension to the program name
     *
     * @param string $strProgram name of the program
     *
     * @return string complete path and name of the program
     */
    private static function _findProgram($strProgram)
    {
        $arrPath = array ('/bin', '/sbin', '/usr/bin', '/usr/sbin', '/usr/local/bin', '/usr/local/sbin');

        // If open_basedir defined, fill the $open_basedir array with authorized paths,. (Not tested when no open_basedir restriction)
        foreach ($arrPath as $strPath) {
            // To avoid "open_basedir restriction in effect" error when testing paths if restriction is enabled
            if (!is_dir($strPath)) {
                continue;
            }
            $strProgrammpath = $strPath."/".$strProgram;
            if (is_executable($strProgrammpath)) {
                return $strProgrammpath;
            }
        }
    }

    /**
     * Execute a system program. return a trim()'d result.
     * does very crude pipe checking.  you need ' | ' for it to work
     * ie $program = CommonFunctions::executeProgram('netstat', '-anp | grep LIST');
     * NOT $program = CommonFunctions::executeProgram('netstat', '-anp|grep LIST');
     *
     * @param string  $strProgramname name of the program
     * @param string  $strArgs        arguments to the program
     * @param string  &$strBuffer     output of the command
     * @param boolean $booErrorRep    en- or disables the reporting of errors which should be logged
     *
     * @return boolean command successfull or not
     */
    public static function executeProgram($strProgramname, $strArgs, & $strBuffer, $booErrorRep = true)
    {
        $strBuffer = '';
        $strError = '';
        $pipes = array ();
        $strProgram = self::_findProgram($strProgramname);
        $error = Error::singleton();
        if (!$strProgram) {
            if ($booErrorRep) {
                $error->addError('find_program('.$strProgramname.')', 'program not found on the machine');
            }
            return false;
        }
        // see if we've gotten a |, if we have we need to do path checking on the cmd
        if ($strArgs) {
            $arrArgs = split(' ', $strArgs);
            for ($i = 0, $cnt_args = count($arrArgs); $i < $cnt_args; $i++) {
                if ($arrArgs[$i] == '|') {
                    $strCmd = $arrArgs[$i + 1];
                    $strNewcmd = self::_findProgram($strCmd);
                    $strArgs = ereg_replace("\| ".$strCmd, "| ".$strNewcmd, $strArgs);
                }
            }
        }
        $descriptorspec = array (0=> array ("pipe", "r"), 1=> array ("pipe", "w"), 2=> array ("pipe", "w"));
        $process = proc_open($strProgram." ".$strArgs, $descriptorspec, $pipes);
        if (is_resource($process)) {
            $strBuffer .= self::_timeoutfgets($pipes, $strBuffer, $strError);
            $return_value = proc_close($process);
        }
        $strError = trim($strError);
        $strBuffer = trim($strBuffer);
        if (! empty($strError) && $return_value <> 0) {
            if ($booErrorRep) {
                $error->addError($strProgram, $strError."\nReturn value: ".$return_value);
            }
            return false;
        }
        if (! empty($strError)) {
            if ($booErrorRep) {
                $error->addError($strProgram, $strError."\nReturn value: ".$return_value);
            }
            return true;
        }
        return true;
    }

    /**
     * find duplicate entrys and count them, show this value befor the duplicated name
     *
     * @param array $arrInput source array that should be checked for duplicated names
     *
     * @return array array with duplicate entries removed and a appended value, how many times the entry has appeared
     */
    public static function finddups($arrInput)
    {
        $arrResult = array ();
        if (is_array($arrInput)) {
            $arrBuffer = array_count_values($arrInput);
            foreach ($arrBuffer as $strKey=>$intValue) {
                if ($intValue > 1) {
                    $arrResult[] = "(".$intValue."x) ".$strKey;
                } else {
                    $arrResult[] = $strKey;
                }
            }
        }
        return $arrResult;
    }

    /**
     * read a file and return the content as a string
     *
     * @param string  $strFileName name of the file which should be read
     * @param string  &$strRet     content of the file (reference)
     * @param integer $intLines    control how many lines should be read
     * @param integer $intBytes    control how many bytes of each line should be read
     * @param boolean $booErrorRep en- or disables the reporting of errors which should be logged
     *
     * @return boolean command successfull or not
     */
    public static function rfts($strFileName, & $strRet, $intLines = 0, $intBytes = 4096, $booErrorRep = true)
    {
        $strFile = "";
        $intCurLine = 1;
        $error = Error::singleton();
        if (file_exists($strFileName)) {
            if ($fd = fopen($strFileName, 'r')) {
                while (!feof($fd)) {
                    $strFile .= fgets($fd, $intBytes);
                    if ($intLines <= $intCurLine && $intLines != 0) {
                        break;
                    } else {
                        $intCurLine++;
                    }
                }
                fclose($fd);
                $strRet = $strFile;
            } else {
                if ($booErrorRep) {
                    $error->addError('fopen('.$strFileName.')', 'file can not read by phpsysinfo');
                }
                return false;
            }
        } else {
            if ($booErrorRep) {
                $error->addError('file_exists('.$strFileName.')', 'the file does not exist on your machine');
            }
            return false;
        }
        return true;
    }

    /**
     * reads a directory and return the name of the files and directorys in it
     *
     * @param string  $strPath     path of the directory which should be read
     * @param boolean $booErrorRep en- or disables the reporting of errors which should be logged
     *
     * @return array content of the directory excluding . and ..
     */
    public static function gdc($strPath, $booErrorRep = true)
    {
        $arrDirectoryContent = array ();
        $error = Error::singleton();
        if (is_dir($strPath)) {
            if ($handle = opendir($strPath)) {
                while (($strFile = readdir($handle)) !== false) {
                    if ($strFile != "." && $strFile != "..") {
                        $arrDirectoryContent[] = $strFile;
                    }
                }
                closedir($handle);
            } else {
                if ($booErrorRep) {
                    $error->addError('opendir('.$strPath.')', 'directory can not be read by phpsysinfo');
                }
            }
        } else {
            if ($booErrorRep) {
                $error->addError('is_dir('.$strPath.')', 'directory does not exist on your machine');
            }
        }
        return $arrDirectoryContent;
    }

    /**
     * Check for needed php extensions
     *
     * We need that extensions for almost everything
     * This function will return a hard coded
     * XML string (with headers) if the SimpleXML extension isn't loaded.
     * Then it will terminate the script.
     * See bug #1787137
     *
     * @return void
     */
    public static function checkForExtensions()
    {
        // TODO XSL extension is only required in case there is no javascript, we'd better test this extension in this case only to improve compatibility
        $extensions = array ('simplexml', 'pcre', 'xml', 'xsl', 'mbstring');
        $text = "";
        $error = false;
        $text .= "<?xml version='1.0'?>\n";
        $text .= "<phpsysinfo>\n";
        $text .= "  <Error>\n";
        foreach ($extensions as $extension) {
            if (!extension_loaded($extension)) {
                $text .= "    <Function>checkForExtensions</Function>\n";
                $text .= "    <Message>phpSysInfo requires the ".$extension." extension to php in order to work properly.</Message>\n";
                $error = true;
            }
        }
        $text .= "  </Error>\n";
        $text .= "</phpsysinfo>";
        if ($error) {
            header("Content-Type: text/xml\n\n");
            echo $text;
            die ();
        }
    }



    /**
     * get the content of stdout/stderr with the option to set a timeout for reading
     *
     * @param array   $pipes array of file pointers for stdin, stdout, stderr (proc_open())
     * @param string  &$out  target string for the output message (reference)
     * @param string  &$err  target string for the error message (reference)
     * @param integer $sek   timeout value in seconds
     *
     * @return void
     */
    private static function _timeoutfgets($pipes, & $out, & $err, $sek = 30)
    {
        // fill output string
        $time = $sek;
        while ($time >= 0) {
            $read = array ($pipes[1]);
            while (!feof($read[0]) && ($n = stream_select($read, $w = null, $e = null, $time)) !== false && $n > 0 && strlen($c = fgetc($read[0])) > 0) {
                $out .= $c;
            }
            --$time;
        }
        // fill error string
        $time = $sek;
        while ($time >= 0) {
            $read = array ($pipes[2]);
            while (!feof($read[0]) && ($n = stream_select($read, $w = null, $e = null, $time)) !== false && $n > 0 && strlen($c = fgetc($read[0])) > 0) {
                $err .= $c;
            }
            --$time;
        }
    }

    public static function ArrayToXML($array, $nodeName = null, $defaultName = null) {
        $result = '';

        foreach($array as $k1=>$node) {

            if(is_numeric($k1))
            $k1 = $defaultName;

            $result .= '
<'.($nodeName != null ? $nodeName : $k1).'>';
            if(is_array($node)) {
                foreach($node as $key=>$value) {
                    $key = ucfirst(str_replace(' ', '_', $key));

                    if(is_numeric($key))
                        $key = $defaultName;


                    if(is_array($value)) {
                        $result .= self::ArrayToXML($value, $key, $defaultName);
                    }
                    else {
                        $result .= '
<'.$key.'><![CDATA['.$value.']]></'.$key.'>
';
                    }
                }
            }
            else {
                $result .= $node;
            }
            $result .= '</'.($nodeName != null ? $nodeName : $k1).'>
';
        }

        return $result;
    }
}
?>