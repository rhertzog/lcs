<?php

class Monitor {

    const MONITORVERSION = '0.0.1';

    private $config;
    private $tokenServer;
    private $xml;
    private $genre = '';
    private $ip;

    public function __construct() {
        $this->config = new ConfigLoader();

        if(CommonFunctions::executeProgram("ifconfig", $this->config->ifnetwork, $buffer)) {
            $lines = explode("\n", $buffer);
            $datas = explode("HWaddr", $lines[0]);
            if(count($datas) == 2)
                $this->tokenServer = md5(trim($datas[1]));
        }
    }

    public function SendToPlateforme() {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($curl, CURLOPT_CRLF, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt ($curl, CURLOPT_URL, $this->config->urlHelpDesk.'AjaxAPI/ServerCronDiagnostique');
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'xml='.urlencode($this->xml));

        $response = curl_exec($curl);
        $this->error = curl_error($curl);
        //list($headers, $content) = explode("\n\r\n",$response);
        //$headers = explode("\n",trim($headers));

        echo $response;

        curl_close($curl);
    }

    public function GenerateToXML() {
        $this->xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $this->xml .= '<GestEtabDiagnostique>'."\n";
        fwrite(STDERR, "Informations de diagnostique\n");
        $this->xml .= $this->GetDiagInformations();
        fwrite(STDERR, "Informations générales\n");
        $this->xml .= $this->GetGeneralInformations();

        if($this->genre == 'LCS') {
            fwrite(STDERR, "Informations LCS\n");
            $this->xml .= $this->GetLCSInformations();
        }
        elseif($this->genre == 'SE3') {
            fwrite(STDERR, "Informations SE3\n");
            $this->xml .= $this->GetSE3Informations();
        }

        fwrite(STDERR, "Informations de mémoire\n");
        $this->xml .= $this->GetMemoryInformations();
        fwrite(STDERR, "Informations des processus\n");
        $this->xml .= $this->GetProcessInformations();
        fwrite(STDERR, "Informations du réseau\n");
        $this->xml .= $this->GetNetworkInformations();
        fwrite(STDERR, "Informations du CPU\n");
        $this->xml .= $this->GetCPUInformations();
        fwrite(STDERR, "Informations du filesystem\n");
        $this->xml .= $this->GetDetailFilesystem();
        fwrite(STDERR, "Informations sur les modules\n");
        $this->xml .= $this->GetModulesInstalled();
        $this->xml .= '</GestEtabDiagnostique>';
        fwrite(STDERR, "Fin de diagnostique\n");
        $this->xml = utf8_encode($this->xml);
        return $this->xml;
    }

    private function GetLCSInformations() {
        $results = array();

        // Lit la config
        if (CommonFunctions::rfts('/etc/lcs/lcs.conf', $bufr)) {
            $lines = explode("\n", $bufr);
            foreach($lines as $line) {
                $chars = str_split($line);
                $inString = false;
                $stringSeparator = '';
                $propName = '';
                $inProp = true;
                $escapeNext = false;
                $value = '';

                foreach($chars as $char) {
                    if(!$inString && $char == '#') {
                        break;
                    }

                    if(!$inString && ($char == '\'' || $char == '"')) {
                        $inString = true;
                        $stringSeparator = $char;
                        continue;
                    }

                    if($inString && !$escapeNext && $char == $stringSeparator) {
                        break;
                    }

                    if(!$inString && $char == " ") {
                        continue;
                    }

                    if($inProp && $char == '=') {
                        $inProp = false;
                        continue;
                    }

                    if(!$escapeNext && $char == "\\") {
                        $escapeNext = true;
                        continue;
                    }

                    if($escapeNext && $char == $stringSeparator) {
                        $escapeNext = false;
                    }

                    if($inProp) {
                        $propName .= $char;
                    }
                    else {
                        $value .= $char;
                    }
                }

                $propName = trim($propName);
                if($propName != '') {
                    $results['Conf'][$propName] = $value;
                    if($propName == 'LDAP_SERVER' && (strtolower($value) == '127.0.0.1' || strtolower($value) == 'localhost' || strtolower($value) == strtolower($this->ip))) {
                        $this->xml = str_replace('<AnnuThere>0</AnnuThere>', '<AnnuThere>1</AnnuThere>', $this->xml);
                    }
                }
            }
        }

        // Lit la BDD
        $results['BDD'] = array();
        try {
            $dbh = new PDO('mysql:host=localhost;dbname=lcs_db', 'root', $results['Conf']['MYSQLPW']);
            foreach($dbh->query('SELECT * FROM params') as $row) {
                if(trim($row['name']) != '') {
                    $results['BDD'][trim($row['name'])] = $row['value'];
                }
            }
            $dbh = null;
        } catch (PDOException $e) {
            $results['BDD'] = "Erreur !: " . $e->getMessage() . "<br/>";
        }

        return '<LCSInformations>
'.CommonFunctions::ArrayToXML($results).'
</LCSInformations>';
    }

private function GetSE3Informations() {

        $user = exec("cat /var/www/se3/includes/config.inc.php | grep \"dbuser=\" | cut -d\"=\" -f2 | cut -d\";\" -f1");
        $pass = exec("cat /var/www/se3/includes/config.inc.php | grep \"dbpass=\" | cut -d\"=\" -f2 | cut -d\";\" -f1");
        $host = exec("cat /var/www/se3/includes/config.inc.php | grep \"dbhost=\" | cut -d\"=\" -f2 | cut -d\";\" -f1");
        $name = exec("cat /var/www/se3/includes/config.inc.php | grep \"dbname=\" | cut -d\"=\" -f2 | cut -d\";\" -f1");


        $user = str_replace('"','',$user);
        $pass = str_replace('"','',$pass);
        $host = str_replace('"','',$host);
        $name = str_replace('"','',$name);
        // Lit la BDD
        $results['BDD'] = array();
        try {
            $dbh = new PDO('mysql:host='.$host.';dbname='.$name, $user, $pass);
            foreach($dbh->query('SELECT * FROM params') as $row) {
                if(trim($row['name']) != '') {
                    $results['BDD']['se3_'.trim($row['name'])] = $row['value'];
                }
            }
            $dbh = null;
        } catch (PDOException $e) {
            $results['BDD'] = "Erreur !: " . $e->getMessage() . "<br/>";
        }

        return '<SE3Informations>'.CommonFunctions::ArrayToXML($results).'</SE3Informations>';


    }

    private function GetMemoryInformations() {
        $bufr = "";
        $results['ram'] = array('total'=>0, 'free'=>0, 'used'=>0, 'percent'=>0);
        $results['swap'] = array('total'=>0, 'free'=>0, 'used'=>0, 'percent'=>0);
        $results['devswap'] = array();
        if (CommonFunctions::rfts('/proc/meminfo', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/^MemTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $results['ram']['total'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^MemFree:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $results['ram']['free'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^Cached:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $results['ram']['cached'] = $ar_buf[1] * 1024;
                } elseif (preg_match('/^Buffers:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
                    $results['ram']['buffers'] = $ar_buf[1] * 1024;
                }
            }
            $results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
            $results['ram']['percent'] = round(($results['ram']['used'] * 100) / $results['ram']['total']);
            // values for splitting memory usage
            if (isset($results['ram']['cached']) && isset($results['ram']['buffers'])) {
                $results['ram']['app'] = $results['ram']['used'] - $results['ram']['cached'] - $results['ram']['buffers'];
                $results['ram']['app_percent'] = round(($results['ram']['app'] * 100) / $results['ram']['total']);
                $results['ram']['buffers_percent'] = round(($results['ram']['buffers'] * 100) / $results['ram']['total']);
                $results['ram']['cached_percent'] = round(($results['ram']['cached'] * 100) / $results['ram']['total']);
            }
            $bufr = "";
            if (CommonFunctions::rfts('/proc/swaps', $bufr)) {
                $swaps = explode("\n", $bufr);
                for ($i = 1, $max = sizeof($swaps); $i < $max; $i++) {
                    if (trim($swaps[$i]) != "") {
                        $ar_buf = preg_split('/\s+/', $swaps[$i], 6);
                        $results['devswap'][$i - 1] = array();
                        $results['devswap'][$i - 1]['dev'] = $ar_buf[0];
                        $results['devswap'][$i - 1]['total'] = $ar_buf[2] * 1024;
                        $results['devswap'][$i - 1]['used'] = $ar_buf[3] * 1024;
                        $results['devswap'][$i - 1]['free'] = ($results['devswap'][$i - 1]['total'] - $results['devswap'][$i - 1]['used']);
                        $results['devswap'][$i - 1]['percent'] = round(($ar_buf[3] * 100) / $ar_buf[2]);
                        $results['swap']['total'] += $ar_buf[2] * 1024;
                        $results['swap']['used'] += $ar_buf[3] * 1024;
                        $results['swap']['free'] = $results['swap']['total'] - $results['swap']['used'];
                        $results['swap']['percent'] = round(($results['swap']['used'] * 100) / (($results['swap']['total'] <= 0) ? 1 : $results['swap']['total']));
                    }
                }
            }
        }

        return '<MemoryInformations>
'.CommonFunctions::ArrayToXML($results).'
</MemoryInformations>';
    }

    private function GetDiagInformations() {
        $xml = '<DiagInformations>';
        $xml .= '<MonitorVersion>'.Monitor::MONITORVERSION.'</MonitorVersion>';
        $xml .= '<TokenEtab>'.$this->config->tokenEtab.'</TokenEtab>';
        $xml .= '<TokenServer>'.$this->tokenServer.'</TokenServer>';
        $xml .= '<GenerationTime unix="'.date('U').'">'.date('r').'</GenerationTime>';
        // Cherche le cron si il existe
        $xml .= $this->CronTime();
        $xml .= '</DiagInformations>';

        return $xml;
    }

    private function GetProcessInformations() {
        $results = array();

        if(CommonFunctions::executeProgram("ps", "-eo pid,ppid,pcpu,pmem,args", $buffer)) {
            $fileContent = explode("\n", $buffer);
            array_shift($fileContent);
            foreach ($fileContent as $roworig) {
                $row = preg_split("/[\s]+/", trim($roworig), 4);
                if (count($row) != 4) {
                    break;
                }

                $results[] = array(
                    'pid' => $row[0],
                    'ppid' => $row[1],
                    'pcpu' => $row[2],
                    'pmem' => $row[3],
                    'args' => (isset($row[4]) ? $row[4] : ''),
                );
            }
        }

        return '<ProcessInformations>
'.CommonFunctions::ArrayToXML($results, 'Process').'
</ProcessInformations>';
    }

    private function GetGeneralInformations() {

        $results = array();

        // Distribution
        if(CommonFunctions::rfts('/proc/version', $distribData)) {
            $results['Distribution'] = trim($distribData);
        }

        // Ip de la machine
        if(CommonFunctions::executeProgram("ifconfig", $this->config->ifnetwork.' | grep inet | cut -d":" -f2 | cut -d" " -f1', $buffer)) {
            $results['IPMachine'] = $buffer;
        }

        // Marque
        if(CommonFunctions::executeProgram("hwinfo", '--bios | grep -m1 ^\ \ \ \ Manufacturer: | cut -f2 -d\"', $buffer)) {
            $results['Marque'] = $buffer;
        }
        else {
            $results['Marque'] = '<![CDATA['.Error::singleton()->errorsAsXML().']]>';
        }

        // Model
        if(CommonFunctions::executeProgram("hwinfo", "--bios | grep -m1 ^\ \ \ \ Product: | cut -f2 -d\\\"", $buffer)) {
            $results['Model'] = $buffer;
        }

        $results['Prod'] = trim($this->config->isProductionServer);
        $results['AnnuThere'] = 0;

        // Genre
        if(CommonFunctions::executeProgram("dpkg", "-l | grep lcs-web", $buffer)) {
            if(strlen($buffer) > 0) {
                $this->genre = 'LCS';
            }
            else {
                if(CommonFunctions::executeProgram("dpkg", "-l | grep se3", $buffer)) {
                    if(strlen($buffer) > 0) {
                        $this->genre = 'SE3';
                    }
                    else {
                        $this->genre = 'UNKNOW';
                    }

                }
            }
        }


        $results['Genre'] = $this->genre;

        //Kernel
        $results['Kernel'] = $this->kernel();

        // UpTime
        $results['Uptime'] = $this->uptime();

        return '<GeneralInformations>
'.CommonFunctions::ArrayToXML($results).'
</GeneralInformations>';
    }

    private function GetNetworkInformations() {
        $results = array();
        $bufr = "";
        if (CommonFunctions::rfts('/proc/net/dev', $bufr)) {
            $bufe = explode("\n", $bufr);
            foreach ($bufe as $buf) {
                if (preg_match('/:/', $buf)) {
                    list($dev_name, $stats_list) = preg_split('/:/', $buf, 2);
                    $dev_name = trim($dev_name);
                    $stats = preg_split('/\s+/', trim($stats_list));
                    $results[$dev_name] = array();
                    $results[$dev_name]['rx_bytes'] = $stats[0];
                    $results[$dev_name]['tx_bytes'] = $stats[8];
                    $results[$dev_name]['errs'] = $stats[2] + $stats[10];
                    $results[$dev_name]['drop'] = $stats[3] + $stats[11];

                    // Get Mac
                    if(CommonFunctions::executeProgram("ifconfig", $dev_name, $buffer)) {
                        $lines = explode("\n", $buffer);
                        $datas = explode("HWaddr", $lines[0]);
                        if(count($datas) == 2)
                            $results[$dev_name]['mac'] = trim($datas[1]);
                    }
                }
            }
        }

        return '<NetworkInformations>
'.CommonFunctions::ArrayToXML($results).'
</NetworkInformations>';
    }

    private function GetCPUInformations() {
        $bufr = "";
        $results = array("cpus"=>0);
        if (CommonFunctions::rfts('/proc/cpuinfo', $bufr)) {
            $bufe = explode("\n", $bufr);
            $results = array('cpus'=>0, 'bogomips'=>0);
            $arrBuff = array();
            foreach ($bufe as $buf) {
                $arrBuff = preg_split('/\s+:\s+/', trim($buf));
                if (count($arrBuff) == 2) {
                    $key = $arrBuff[0];
                    $value = $arrBuff[1];
                    switch ($key) {
                        case 'model name':
                            $results['model'] = $value;
                            break;
                        case 'cpu MHz':
                            $results['cpuspeed'] = sprintf('%.2f', $value);
                            break;
                        case 'cycle frequency [Hz]':
                            $results['cpuspeed'] = sprintf('%.2f', $value / 1000000); // For Alpha arch - 2.2.x
                            break;
                        case 'clock':
                            $results['cpuspeed'] = sprintf('%.2f', $value); // For PPC arch (damn borked POS)
                            break;
                        case 'cpu':
                            $results['model'] = $value; // For PPC arch (damn borked POS)
                            break;
                        case 'L2 cache':
                            $results['cache'] = $value * 1024; // More for PPC
                            break;
                        case 'revision':
                            $results['model'] .= ' ( rev: '.$value.')'; // For PPC arch (damn borked POS)
                            break;
                        case 'cpu model':
                            $results['model'] .= ' ('.$value.')'; // For Alpha arch - 2.2.x
                            break;
                        case 'cache size':
                            $results['cache'] = (preg_replace("/[a-zA-Z]/", "", $value)) * 1024;
                            break;
                        case 'bogomips':
                            $results['bogomips'] += $value;
                            break;
                        case 'BogoMIPS':
                            $results['bogomips'] += $value; // For alpha arch - 2.2.x
                            break;
                        case 'BogoMips':
                            $results['bogomips'] += $value; // For sparc arch
                            break;
                        case 'cpus detected':
                            $results['cpus'] += $value; // For Alpha arch - 2.2.x
                            break;
                        case 'system type':
                            $results['model'] .= ', '.$value.' '; // Alpha arch - 2.2.x
                            break;
                        case 'platform string':
                            $results['model'] .= ' ('.$value.')'; // Alpha arch - 2.2.x
                            break;
                        case 'processor':
                            $results['cpus'] += 1;
                            break;
                        case 'Cpu0ClkTck':
                            $results['cpuspeed'] = sprintf('%.2f', hexdec($value) / 1000000); // Linux sparc64
                            break;
                        case 'Cpu0Bogo':
                            $results['bogomips'] += $value; // Linux sparc64 & sparc32
                            break;
                        case 'ncpus probed':
                            $results['cpus'] = $value; // Linux sparc64 & sparc32
                            break;
                    }
                }
            }
            // sparc64 specific code follows
            // This adds the ability to display the cache that a CPU has
            // Originally made by Sven Blumenstein <bazik@gentoo.org> in 2004
            // Modified by Tom Weustink <freshy98@gmx.net> in 2004
            $sparclist = array('SUNW,UltraSPARC@0,0', 'SUNW,UltraSPARC-II@0,0', 'SUNW,UltraSPARC@1c,0', 'SUNW,UltraSPARC-IIi@1c,0', 'SUNW,UltraSPARC-II@1c,0', 'SUNW,UltraSPARC-IIe@0,0');
            foreach ($sparclist as $name) {
                if (CommonFunctions::rfts('/proc/openprom/'.$name.'/ecache-size', $buf, 1, 32, false)) {
                    $results['cache'] = base_convert($buf, 16, 10);
                }
            }
            // sparc64 specific code ends
            // XScale detection code
            if ($results['cpus'] == 0) {
                foreach ($bufe as $buf) {
                    $fields = preg_split('/\s*:\s*/', trim($buf), 2);
                    if (sizeof($fields) == 2) {
                        list($key, $value) = $fields;
                        switch ($key) {
                            case 'Processor':
                                $results['cpus'] += 1;
                                $results['model'] = $value;
                                break;
                            case 'BogoMIPS':
                                $results['cpuspeed'] = $value; //BogoMIPS are not BogoMIPS on this CPU, it's the speed, no BogoMIPS available
                                break;
                            case 'I size':
                                $results['cache'] = $value * 1024;
                                break;
                            case 'D size':
                                $results['cache'] += $value * 1024;
                                break;
                        }
                    }
                }
            }
        }
        $keys = array_keys($results);
        $keys2be = array('model', 'cpuspeed', 'cpus');
        while ($ar_buf = each($keys2be)) {
            if (!in_array($ar_buf[1], $keys)) {
                $results[$ar_buf[1]] = '0';
            }
        }
        if (CommonFunctions::rfts('/proc/acpi/thermal_zone/THRM/temperature', $buf, 1, 4096, false)) {
            $results['temp'] = substr($buf, 25, 2);
        }

        //$results['CPULoad'] = $this->CPULoad();

        return CommonFunctions::ArrayToXML(array($results), 'CPUInformations');
    }

    private function GetDetailFilesystem() {
        $xml = '<FileSystems>';

        $results = array ();
        $j = 0;
        $df = "";
        $df2 = "";
        $mount = "";


        if(CommonFunctions::executeProgram('df', '-kP', $df)) {
            $df = preg_split("/\n/", $df, -1, PREG_SPLIT_NO_EMPTY);

            if (CommonFunctions::executeProgram('mount', '', $mount)) {
                $mount = preg_split("/\n/", $mount, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($mount as $mount_line) {
                    if (preg_match("/\S+ on (\S+) type (.*) \((.*)\)/", $mount_line, $mount_buf)) {
                        $mount_parm[$mount_buf[1]]['fstype'] = $mount_buf[2];
                        $mount_parm[$mount_buf[1]]['options'] = $mount_buf[3];
                    } elseif (preg_match("/\S+ (.*) on (\S+) \((.*)\)/", $mount_line, $mount_buf)) {
                        $mount_parm[$mount_buf[2]]['fstype'] = $mount_buf[1];
                        $mount_parm[$mount_buf[2]]['options'] = $mount_buf[3];
                    } elseif (preg_match("/\S+ on (\S+) \((\S+)(,\s(.*))?\)/", $mount_line, $mount_buf)) {
                        $mount_parm[$mount_buf[1]]['fstype'] = $mount_buf[2];
                        $mount_parm[$mount_buf[1]]['options'] = isset ($mount_buf[4])?$mount_buf[4]:'';
                    }
                }
                unset ($mount, $mount_line, $mount_buf);
                foreach ($df as $df_line) {
                    $df_buf1 = preg_split("/(\%\s)/", $df_line, 2);
                    if (count($df_buf1) != 2) {
                        continue ;
                    }
                    preg_match("/(.*)(\s+)(([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)(\s+)([0-9]+)$)/", $df_buf1[0], $df_buf2);
                    $df_buf = array ($df_buf2[1], $df_buf2[4], $df_buf2[6], $df_buf2[8], $df_buf2[10], $df_buf1[1]);
                    if (count($df_buf) == 6) {
                        $df_buf[5] = trim($df_buf[5]);

                        $xml .= '<FileSystem>';
                        $xml .= '<Disk>'.trim($df_buf[0]).'</Disk>';


                        $xml .= '<Size>'.($df_buf[1]*1024).'</Size>';
                        $xml .= '<Used>'.($df_buf[2]*1024).'</Used>';
                        $xml .= '<Free>'.($df_buf[3]*1024).'</Free>';


                        $xml .= '<Prcent>'.(round((($df_buf[2]*1024)*100)/($df_buf[1]*1024))).'</Prcent>';
                        $xml .= '<Mount>'.($df_buf[5]).'</Mount>';
                        $xml .= '<Fstype>'.($mount_parm[$df_buf[5]]['fstype']).'</Fstype>';
                        $xml .= '<Options>'.($mount_parm[$df_buf[5]]['options']).'</Options>';
                        $xml .= '</FileSystem>';
                    }
                }

            }
        }
        $xml .= '</FileSystems>';
        return $xml;
    }

    private function GetModulesInstalled() {
        $xml = '<ModulesEnabled>
';
        if(CommonFunctions::executeProgram('dpkg-query', ' -W -f=\'<Module name="${Package}">\n<Version>${Version}</Version>\n<Size>${Installed-size}</Size>\n<Status>${Status}</Status>\n<Description><![CDATA[${Description}]]></Description>\n</Module>\n\'', $strBuffer)) {
            $xml .= $strBuffer;
        }
        $xml .= '
</ModulesEnabled>';

        return $xml;
    }

    /**
     * Canonical Host Name
     *
     @return string
     */
    private function chostname() {
        $result = "";
        if (CommonFunctions::rfts('/proc/sys/kernel/hostname', $result, 1)) {
            $result = trim($result);
            $ip = gethostbyname($result);
            if ($ip != $result) {
                $result = gethostbyaddr($ip);
            } else {
                $result = 'Unknown';
            }
        } else {
            $result = "N.A.";
        }
        return $result;
    }

    /**
     * Kernel Version
     *
     * @return string
     */
    public function kernel() {
        $strBuf = "";
        if (CommonFunctions::executeProgram('uname', '-r', $strBuf)) {
            $result = trim($strBuf);
            if (CommonFunctions::executeProgram('uname', '-v', $strBuf)) {
                if (preg_match('/SMP/', $strBuf)) {
                    $result .= ' (SMP)';
                }
            }
            if (CommonFunctions::executeProgram('uname', '-m', $strBuf)) {
                $result .= ' '.trim($strBuf);
            }
        } else {
            if (CommonFunctions::rfts('/proc/version', $strBuf, 1)) {
                if (preg_match('/version (.*?) /', $strBuf, $ar_buf)) {
                    $result = $ar_buf[1];
                    if (preg_match('/SMP/', $strBuf)) {
                        $result .= ' (SMP)';
                    }
                } else {
                    $result = "N.A.";
                }
            } else {
                $result = "N.A.";
            }
        }
        return $result;
    }

    private function CPULoad() {
        $buf = "";
        if (CommonFunctions::rfts('/proc/loadavg', $buf)) {
            $results['avg'] = preg_split("/\s/", $buf, 4);
            // don't need the extra values, only first three
            unset($results['avg'][3]);
        } else {
            $results['avg'] = array('N.A.', 'N.A.', 'N.A.');
        }

        if (CommonFunctions::rfts('/proc/stat', $buf, 1)) {
            $ab = 0;
            $ac = 0;
            $ad = 0;
            $ae = 0;
            sscanf($buf, "%*s %Ld %Ld %Ld %Ld", $ab, $ac, $ad, $ae);
            // Find out the CPU load
            // user + sys = load
            // total = total
            $load = $ab + $ac + $ad; // cpu.user + cpu.sys
            $total = $ab + $ac + $ad + $ae; // cpu.total
            // we need a second value, wait 1 second befor getting (< 1 second no good value will occour)
            sleep(1);
            CommonFunctions::rfts('/proc/stat', $buf, 1);
            sscanf($buf, "%*s %Ld %Ld %Ld %Ld", $ab, $ac, $ad, $ae);
            $load2 = $ab + $ac + $ad;
            $total2 = $ab + $ac + $ad + $ae;
            $results['cpupercent'] = (100 * ($load2 - $load)) / ($total2 - $total);
        }
        return $results;
    }

    /**
     * UpTime
     * time the system is running
     *
     * @return integer
     */
    public function uptime() {
        $buf = "";
        CommonFunctions::rfts('/proc/uptime', $buf, 1);
        $ar_buf = split(' ', $buf);
        $result = trim($ar_buf[0]);
        return $result;
    }

    private function CronTime() {
        $crons = CommonFunctions::gdc('/etc/cron.d/');
        $file = '';

        foreach($crons as $cron) {
            $path = '/etc/cron.d/'.$cron;
            if(is_file($path) && $cron == 'lcse3-monitor') {
                $file = $path;
                break;
            }
        }

        if($file == '')
            return;

        if(CommonFunctions::rfts($file, $buffer)) {
            $lines = explode("\n",$buffer);
            foreach($lines as $line) {
                if(empty($line) || $line[0] == '#' || ereg("(.*)=(.*)", $line, $assign) || $line[0] == '@')
                    continue;
                else {
                    $data = explode(" ", $line, 7);
                    break;
                }
            }

            if($data == array())
                return;

            list($minute, $heure, $jour, $mois, $jourSemaine, $user, $cmd) = $data;

            $cron = new CronParser();
            $cron->calcNextRan(implode(" ", array($minute, $heure, $jour, $mois, $jourSemaine)));
            $stamp = $cron->getLastRanUnix();

            return '<NextUpdate unix="'.$stamp.'">'.date('r',$stamp).'</NextUpdate>';
        }
    }
}
?>
