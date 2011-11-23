<?php 
if ( ! file_exists('../../inc/currentVersion.inc.php') )
{
    // if this file doesn't exist, the current version is < claroline 1.6
    // in 1.6 we need a $platform_id for session handling
    $platform_id =  md5(realpath('../../inc/conf/def/CLMAIN.def.conf.inc.php'));
}

// Initialise Upgrade
require 'upgrade_init_global.inc.php';


// Define display
DEFINE ('DISPLAY_WELCOME_PANEL', __LINE__);
DEFINE ('DISPLAY_RESULT_ERROR_PANEL', __LINE__);
DEFINE ('DISPLAY_RESULT_SUCCESS_PANEL', __LINE__);
DEFINE ('ERROR_WRITE_FAILED', __LINE__);
$display = DISPLAY_WELCOME_PANEL;
echo '<h2>Etape 1/4: Maj des fichiers de configuration en version '.$new_version.'</h2>'. "\n";
/*=====================================================================
  Main Section
 =====================================================================*/

$error = FALSE;

if ( isset($_REQUEST['verbose']) ) $verbose = true;

$cmd ='run';

if ( $cmd == 'run' )
{
    // Create module, platform, tmp folders
    if ( !file_exists(get_path('rootSys') . 'module/') )        claro_mkdir(get_path('rootSys') . 'module/', CLARO_FILE_PERMISSIONS, true);
    if ( !file_exists(get_path('rootSys') . 'platform/') )      claro_mkdir(get_path('rootSys') . 'platform/', CLARO_FILE_PERMISSIONS, true);
    if ( !file_exists(get_path('rootSys') . 'platform/conf/') ) claro_mkdir(get_path('rootSys') . 'platform/conf/', CLARO_FILE_PERMISSIONS, true);
    if ( !file_exists(get_path('rootSys') . 'tmp/') )           claro_mkdir(get_path('rootSys') . 'tmp/', CLARO_FILE_PERMISSIONS, true);

    // Create folder to backup configuration files
    $backupRepositorySys = get_path('rootSys') .'platform/bak.'.date('Y-z-B').'/';
    claro_mkdir($backupRepositorySys, CLARO_FILE_PERMISSIONS, true);

    $output = '<h3>Configuration file</h3>' . "\n" ;

    $output.= '<ol>' . "\n" ;

    /*
     * Generate configuration file from definition file
     */

    $config_code_list = get_config_code_list();
    $config_code_list = array_merge($config_code_list,array('CLANN','CLCAL','CLFRM','CLCHT','CLDOC','CLDSC','CLUSR','CLLNP','CLQWZ','CLWRK','CLWIKI'));

    if ( is_array($config_code_list) )
    {
        // Build table with current values in configuration files
        $current_property_list = array();

        foreach ( $config_code_list as $config_code )
        {
            // new config object
            $config = new ConfigUpgrade($config_code);
            $config->load();
            $this_property_list = $config->get_property_list();
            $current_property_list = array_merge($current_property_list, $this_property_list);
            unset($config);
        }

        // Set platform_id if not set in current claroline version (new in 1.6)
        if ( ! isset($current_property_list['platform_id']) )
        {
            $current_property_list['platform_id'] = $platform_id;
        }

        // Old variables from 1.5
        if ( isset($current_property_list['administrator']) )
        {
            $current_property_list['administrator_name'] = $administrator['name'];
            $current_property_list['administrator_phone'] = $administrator['phone'];
            $current_property_list['administrator_email'] = $administrator['email'];
        }

        // Old variables from 1.5
        if ( isset($current_property_list['institution']) )
        {
            $current_property_list['institution_name'] = $current_property_list['institution']['name'];
            $current_property_list['institution_url'] = $current_property_list['institution']['url'];
        }


        // UPDATE for  1.9
        // split defaultVisibilityForANewCourse in 2 new var
        // 'acceptedValue' => array ('0'=>'Private&nbsp;+ New registration denied'
        //                          ,'1'=>'Private&nbsp+ New Registration allowed'
        //                          ,'2'=>'Public&nbsp;&nbsp;+ New Registration allowed'
        //                          ,'3'=>'Public&nbsp;&nbsp;+ New Registration denied'
        if ( isset($current_property_list['defaultVisibilityForANewCourse']) )
        {
            $current_property_list['defaultAccessOnCourseCreation']    = (bool) ( $current_property_list['defaultVisibilityForANewCourse'] == 2 or $current_property_list['defaultVisibilityForANewCourse'] == 3 );
            $current_property_list['defaultRegistrationOnCourseCreation'] = (bool) ( $current_property_list['defaultVisibilityForANewCourse'] == 1 or $current_property_list['defaultVisibilityForANewCourse'] == 2 );
        }
        
        // UPDATE for 1.9
        // css should point to a theme not to a stylesheet
        $current_property_list['claro_stylesheet'] = 'classic';
        
        // Browse definition file and build them

        reset( $config_code_list );

        foreach ( $config_code_list as $config_code )
        {
            $config = new ConfigUpgrade($config_code);

            // load and initialise the config
            if ( $config->load() )
            {
                $config_filename = $config->get_config_filename();

                $output .= '<li>'. htmlspecialchars(basename($config_filename))
                        .  '<ul >' . "\n";

                // Backup current file
                $output .= '<li>Validate property : ' ;

                if ( $config->validate($current_property_list) )
                {
                    $output .= '<span class="success">Succeeded</span></li>';

                    if ( !file_exists($config_filename) )
                    {
                        // Create a file empty if not exists
                        touch($config_filename);
                    }
                    else
                    {
                        // Backup current file
                        $output .= '<li>Backup old file : ' ;

                        $fileBackup = $backupRepositorySys . basename($config_filename);

                        if ( !@copy($config_filename, $fileBackup) )
                        {
                            $output .= '<span class="warning">Failed</span>';
                        }
                        else
                        {
                            $output .= '<span class="success">Succeeded</span>';
                        }
                        $output .= '</li>' . "\n" ;

                        // Change permission of the backup file
                        @chmod( $fileBackup, CLARO_FILE_PERMISSIONS );
                        @chmod( $fileBackup, CLARO_FILE_PERMISSIONS );
                    }

                    $output .= '<li>Upgrade file : ';

                    if ( $config->save() )
                    {
                        $output .= '<span class="success">Succeeded</span>';
                    }
                    else
                    {
                        $output .= '<span class="warning">Failed : ' . $config->backlog->output() . '</span>';
                        $error = true ;
                    }

                    $output .= '</li>'."\n";
                }
                else
                {
                    $output .= '<span class="warning">Failed : ' . $config->backlog->output() . '</span></li>' . "\n";
                    $error = true ;
                }

                $output .= '</ul>' . "\n"
                     . '</li>' . "\n";

            } // end if config->load()

        } // end browse definition file and build them

    } // end if is_array def file list

    /**
     * Config file to undist
     */

    $arr_file_to_undist = array ( get_path('incRepositorySys').'/conf/drivers.auth.conf.php' => get_path('rootSys').'platform/conf' );

    foreach ( $arr_file_to_undist as $undistFile => $undistPath )
    {
        $output .= '<li>'. basename ($undistFile) . "\n"
                . '<ul><li>Undist : ' . "\n" ;

        if ( claro_undist_file($undistFile, $undistPath) )
        {
            $output .= '<span class="success">Succeeded</span>';
        }
        else
        {
            $output .= '<span class="warning">Failed</span>';
            $error = TRUE;
        }
        $output .= '</li>' . "\n" . '</ul>' . "\n"
                 . '</li>' . "\n";
    }

    $output .= '</ol>' . "\n";

    if ( !$error )
    {
        $display = DISPLAY_RESULT_SUCCESS_PANEL;

        // Update current version file
        save_current_version_file($new_version,$currentDbVersion);
    }
    else
    {
        $display = DISPLAY_RESULT_ERROR_PANEL;
    }

} // end if run

/*=====================================================================
  Display Section
 =====================================================================*/


// Display Content

switch ($display)
{
       case DISPLAY_RESULT_ERROR_PANEL :
        echo '<h2>Etape 1/4: Maj des fichiers de configuration - ECHEC </h2>'. "\n";
        echo $output;
        exit(1);        
        break;

    case DISPLAY_RESULT_SUCCESS_PANEL :
       echo '<h3>Etape 1/4 : OK <h3>'. "\n";
       break;
}

//etape 2 : on pousuit par la maj de la bdd si pas erreur
if ( !$error )
    {
    
    require_once 'upgrade_init_global.inc.php';
    require_once $includePath . '/lib/module/manage.lib.php';
/*
 * Initialize version variables
 */

// Current Version
$current_version = get_current_version();
$currentClarolineVersion = $current_version['claroline'];
$currentDbVersion = $current_version['db'];

// New Version
$this_new_version = get_new_version();
$new_version = $this_new_version['complete'];
$new_version_branch = $this_new_version['branch'];
// Define display
DEFINE('DISPLAY_WELCOME_PANEL', 1);
DEFINE('DISPLAY_RESULT_PANEL',  2);
/*=====================================================================
  Main Section
 =====================================================================*/

/**
 * Create Upgrade Status table
 */

$tbl_mdb_names = claro_sql_get_main_tbl();
$tbl_upgrade_status = $tbl_mdb_names['upgrade_status'];

$sql = "CREATE TABLE IF NOT EXISTS `" . $tbl_upgrade_status . "` (
`id` INT NOT NULL auto_increment ,
`cid` VARCHAR( 40 ) NOT NULL ,
`claro_label` VARCHAR( 8 ) ,
`status` TINYINT NOT NULL ,
PRIMARY KEY ( `id` )
)";

claro_sql_query($sql);

$sql = "ALTER IGNORE TABLE `" . $tbl_upgrade_status . "` CHANGE `claro_label` `claro_label` VARCHAR(50) ";

claro_sql_query($sql);

/**
 * Initialise variables
 */

if ( isset($_REQUEST['verbose']) ) $verbose = true;


$display = DISPLAY_WELCOME_PANEL;

/**
 * Define display
 */

if ($cmd == 'run')
{
    // include sql to upgrade the main Database

    require_once('./upgrade_main_db_16.lib.php');
    require_once('./upgrade_main_db_17.lib.php');
    require_once('./upgrade_main_db_18.lib.php');
    require_once('./upgrade_main_db_19.lib.php');
    require_once('./upgrade_main_db_110.lib.php');

    $display = DISPLAY_RESULT_PANEL;

} // if ($cmd=="run")

/*=====================================================================
  Display Section
 =====================================================================*/


switch ( $display )
{
    
    case DISPLAY_RESULT_PANEL :

        // Initialise
        $nbError = 0;

        // Display upgrade result

         echo '<h2>Etape 2/4: Maj des tables de ' . $mainDbName .' en version '.$new_version.'</h2>' . "\n" ;

        if ( ! preg_match('/^1.8/',$currentDbVersion) )
        {
            // repair tables
            sql_repair_main_database();
        }

        /*---------------------------------------------------------------------
          Upgrade 1.5 to 1.6
         ---------------------------------------------------------------------*/

        if ( preg_match('/^1.5/',$currentDbVersion) )
        {
            $function_list = array('upgrade_main_database_to_16');

            foreach ( $function_list as $function )
            {
                $step = $function();
                if ( $step > 0 )
                {
                    echo 'Error : ' . $function . ' at step . ' . $step . '<br />';
                    $nbError++;
                }
            }

            if ( $nbError == 0 )
            {
                // Upgrade 1.5 to 1.6 Succeed
                echo '<p class="success">The claroline main tables have been successfully upgraded to 1.6</p>' . "\n";
                clean_upgrade_status();

                // Database version is 1.6
                $currentDbVersion = '1.6';

                // Update current version file
                save_current_version_file($currentClarolineVersion, $currentDbVersion) ;
            }
        } // end upgrade 1.5 to 1.6

        /*---------------------------------------------------------------------
        Upgrade 1.6 to 1.7
        ---------------------------------------------------------------------*/

        if ( preg_match('/^1.6/',$currentDbVersion) )
        {
            $function_list = array('upgrade_main_database_to_17');

            foreach ( $function_list as $function )
            {
                $step = $function();
                if ( $step > 0 )
                {
                    echo 'Error : ' . $function . ' at step . ' . $step . '<br />';
                    $nbError++;
                }
            }

            if ( $nbError == 0 )
            {
                // Upgrade 1.6 to 1.7 Succeed
                echo '<p class="success">The claroline main tables have been successfully upgraded to 1.7</p>' . "\n";
                clean_upgrade_status();

                // Database version is 1.7
                $currentDbVersion = '1.7';

                // Update current version file
                save_current_version_file($currentClarolineVersion, $currentDbVersion);
            }
        } // End of upgrade 1.6 to 1.7

        /*---------------------------------------------------------------------
        Upgrade 1.7 to 1.8
        ---------------------------------------------------------------------*/

        if ( preg_match('/^1.7/',$currentDbVersion) )
        {
            $function_list = array('upgrade_main_database_course_to_18',
                                   'upgrade_main_database_rel_course_user_to_18',
                                   'upgrade_main_database_course_category_to_18',
                                   'upgrade_main_database_user_to_18',
                                   'upgrade_main_database_course_class_to_18',
                                   'upgrade_main_database_right_to_18',
                                   'upgrade_main_database_module_to_18',
                                   'upgrade_main_database_user_property_to_18',
                                   'upgrade_main_database_tracking_to_18'
                                    );

            foreach ( $function_list as $function )
            {
                $step = $function();
                if ( $step > 0 )
                {
                    echo 'Error : ' . $function . ' at step . ' . $step . '<br />';
                    $nbError++;
                }
            }

            if ( $nbError == 0 )
            {
                // Upgrade 1.7 to 1.8 Succeed
                echo '<p class="success">The claroline main tables have been successfully upgraded to 1.8</p>' . "\n";
                clean_upgrade_status();

                // Database version is 1.8
                $currentDbVersion = $new_version;

                // Update current version file
                save_current_version_file($currentClarolineVersion, $currentDbVersion);
            }
        } // End of upgrade 1.7 to 1.8

        /*---------------------------------------------------------------------
        Upgrade 1.8 to 1.9
        ---------------------------------------------------------------------*/

        if ( preg_match('/^1.8/',$currentDbVersion) )
        {
            $function_list = array('upgrade_main_database_course_to_19',
                                   'upgrade_main_database_user_property_to_19',
                                   'upgrade_main_database_desktop_to_19',
                                   'upgrade_main_database_module_to_19',
                                   'upgrade_main_database_messaging_to_19',
                                   'upgrade_main_database_tracking_to_19',
                                   'upgrade_chat_to_19'
                                    );
                                    
            if( isset($_SESSION['upgrade_tracking_data']) && $_SESSION['upgrade_tracking_data'])
            {
                $function_list[] = 'upgrade_main_database_tracking_data_to_19';
            }
            
            foreach ( $function_list as $function )
            {
                $step = $function();
                if ( $step > 0 )
                {
                    echo 'Error : ' . $function . ' at step . ' . $step . '<br />';
                    $nbError++;
                }
            }

            if ( $nbError == 0 )
            {
                // Upgrade 1.8 to 1.9 Succeed
                echo '<p class="success">The claroline main tables have been successfully upgraded to version 1.9</p>' . "\n";
                clean_upgrade_status();

                // Database version is 1.9
                $currentDbVersion = $new_version;

                // Update current version file
                save_current_version_file($currentClarolineVersion, $currentDbVersion);
            }
        } // End of upgrade 1.8 to 1.9
        
        /*---------------------------------------------------------------------
        Upgrade 1.9 to 1.10
        ---------------------------------------------------------------------*/

        if ( preg_match('/^1.9/',$currentDbVersion) )
        {
            $function_list = array('upgrade_category_to_110',
                                   'upgrade_session_course_to_110',
                                   'upgrade_course_to_110',
                                   'upgrade_cours_user_to_110',
                                   'upgrade_coursehomepage_to_110',
                                   'upgrade_event_resource_to_110'
                                    );
            
            
            foreach ( $function_list as $function )
            {
                $step = $function();
                if ( $step > 0 )
                {
                    echo 'Error : ' . $function . ' at step . ' . $step . '<br />';
                    $nbError++;
                }
            }

            if ( $nbError == 0 )
            {
                // Upgrade 1.9 to 1.10 Succeed
                echo '<p class="success">The claroline main tables have been successfully upgraded to version 1.10</p>' . "\n";
                clean_upgrade_status();

                // Database version is 1.10
                $currentDbVersion = $new_version;

                // Update current version file
                save_current_version_file($currentClarolineVersion, $currentDbVersion);
            }
        } // End of upgrade 1.9 to 1.10
        
        

        if ( $nbError == 0 )
        {

            if ( preg_match('/^1.10/',$currentDbVersion) )
            {
                echo '<h3>Etape 2/4 : OK </h3>'. "\n";
            }
            else echo '<p class="error">Db version unknown : ' . $currentDbVersion . '</p>';

        }
        else
        {
            echo '<p class="error">' . sprintf(" %d errors found",$nbError) . '</p>' . "\n";
           exit(1);
        }

        break;

    default :
        die('Display unknow');
    }//fin step2

//etape 3 : 
    if ( $nbError == 0 )
        {
        $new_version_branch = '';
        require_once 'upgrade_init_global.inc.php';
        require_once $includePath . '/lib/module/manage.lib.php';

/*
 * Initialize version variables
 */

// Current Version
$current_version = get_current_version();
$currentClarolineVersion = $current_version['claroline'];
$currentDbVersion = $current_version['db'];

// New Version
$this_new_version = get_new_version();
$new_version = $this_new_version['complete'];
$new_version_branch = $this_new_version['branch'];

        // Include Libraries
include ('upgrade_course_16.lib.php');
include ('upgrade_course_17.lib.php');
include ('upgrade_course_18.lib.php');
include ('upgrade_course_19.lib.php');
include ('upgrade_course_110.lib.php');
        // DB tables definition
$tbl_mdb_names = claro_sql_get_main_tbl();
$tbl_course = $tbl_mdb_names['course'];
$tbl_rel_course_user   = $tbl_mdb_names['rel_course_user'];
$tbl_course_tool       = $tbl_mdb_names['tool'];

/**
 * Displays flags
 * Using __LINE__ to have an arbitrary value
 */

DEFINE ('DISPLAY_WELCOME_PANEL', __LINE__ );
DEFINE ('DISPLAY_RESULT_PANEL', __LINE__);
$display = DISPLAY_RESULT_PANEL;
// Get start time
$mtime = microtime();
$mtime = explode(' ',$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;
$steptime =$starttime;

// count course to upgrade
$count_course_upgraded = count_course_upgraded($new_version_branch);

$count_course = $count_course_upgraded['total'];
$count_course_error = $count_course_upgraded['error'];
$count_course_upgraded = $count_course_upgraded['upgraded'];

$count_course_error_at_start = $count_course_error;
$count_course_upgraded_at_start =  $count_course_upgraded;
echo '<h2>Etape 3/4: Maj des cours en version '.$new_version.' </h2>';
if ( isset($_REQUEST['upgradeCoursesError']) )
        {
            // retry to upgrade course where upgrade failed
            claro_sql_query(" UPDATE `" . $tbl_course . "` SET `versionClaro` = '1.5' WHERE `versionClaro` = 'error-1.5'");
            claro_sql_query(" UPDATE `" . $tbl_course . "` SET `versionClaro` = '1.6' WHERE `versionClaro` = 'error-1.6'");
            claro_sql_query(" UPDATE `" . $tbl_course . "` SET `versionClaro` = '1.7' WHERE `versionClaro` = 'error-1.7'");
            claro_sql_query(" UPDATE `" . $tbl_course . "` SET `versionClaro` = '1.8' WHERE `versionClaro` = 'error-1.8'");
        }

        $sql_course_to_upgrade = " SELECT c.dbName dbName,
                                          c.code ,
                                          c.administrativeNumber ,
                                          c.directory coursePath,
                                          c.creationDate,
                                          c.versionClaro "
                               . " FROM `" . $tbl_course . "` `c` ";

        if ( isset($_REQUEST['upgradeCoursesError']) )
        {
            // retry to upgrade course where upgrade failed
            $sql_course_to_upgrade .= " WHERE c.versionClaro not like '". $new_version_branch ."%'
                                        ORDER BY c.dbName";
        }
        else
        {
            // not upgrade course where upgrade failed ( versionClaro == error* )
            $sql_course_to_upgrade .= " WHERE ( c.versionClaro not like '". $new_version_branch . "%' )
                                              and c.versionClaro not like 'error%'
                                        ORDER BY c.dbName ";
        }

        $res_course_to_upgrade = mysql_query($sql_course_to_upgrade);

        /*
         * Upgrade course
         */

        while ( ($course = mysql_fetch_array($res_course_to_upgrade) ) )
        {
            // initialise variables

            $currentCourseDbName       = $course['dbName'];
            $currentcoursePathSys      = get_path('coursesRepositorySys') . $course['coursePath'].'/';
            $currentcoursePathWeb      = get_path('coursesRepositoryWeb') . $course['coursePath'].'/';
            $currentCourseCode         = $course['code'];
            $currentCourseFakeCode     = $course['administrativeNumber'];
            $currentCourseCreationDate = $course['creationDate'];
            $currentCourseVersion      = $course['versionClaro'];
            $currentCourseDbNameGlu    = get_conf('courseTablePrefix') . $currentCourseDbName . get_conf('dbGlu'); // use in all queries

            // initialise
            $error = false;
            $upgraded = false;
            $message = '';

            echo '<p><strong>' . ( $count_course_upgraded + 1 ) . ' . </strong>
                  maj du cours :  <strong>' . $currentCourseFakeCode . '</strong></p>';

            /**
             * Make some check.
             * For next versions these test would be set in separate process and available out of upgrade
             */

            // repair tables
            sql_repair_course_database($currentCourseDbNameGlu);

            // course repository doesn't exists

            if ( !file_exists($currentcoursePathSys) )
            {
                $error = true;
                $message .= '<p class="help"><strong>Course has no repository.</strong><br />
                             <small>' .  $currentcoursePathSys . '</small> Not found</p>' . "\n";
                $message .= '<p class="comment">The upgrade tool is not able to upgrade this course.<br />
                             Fix, first, the technical problem and relaunch the upgrade tool.</p>' . "\n";
            }

            if ( ! $error )
            {
                /*---------------------------------------------------------------------
                  Upgrade 1.5 to 1.6
                 ---------------------------------------------------------------------*/

                if ( preg_match('/^1.5/',$currentCourseVersion) )
                {
                    // Function to upgrade tool to 1.6
                    $function_list = array('assignment_upgrade_to_16',
                                           'forum_upgrade_to_16',
                                           'quizz_upgrade_to_16',
                                           'tracking_upgrade_to_16' );

                    foreach ( $function_list as $function )
                    {
                        $step = $function($currentCourseCode);
                        if ( $step > 0 )
                        {
                            echo 'Error : ' . $function . ' at step . ' . $step . '<br />';
                            $error = true;
                        }
                    }

                    if ( ! $error )
                    {
                        // Upgrade succeeded
                        clean_upgrade_status($currentCourseCode);
                        $currentCourseVersion = '1.6';
                    }
                    else
                    {
                        // Upgrade failed
                        $currentCourseVersion = 'error-1.5';
                    }
                    // Save version
                    save_course_current_version($currentCourseCode,$currentCourseVersion);
                }

                /*---------------------------------------------------------------------
                  Upgrade 1.6 to 1.7
                 ---------------------------------------------------------------------*/

                if ( preg_match('/^1.6/',$currentCourseVersion) )
                {
                    // Function to upgrade tool to 1.7
                    $function_list = array( 'agenda_upgrade_to_17',
                                            'announcement_upgrade_to_17',
                                            'course_description_upgrade_to_17',
                                            'forum_upgrade_to_17',
                                            'introtext_upgrade_to_17',
                                            'linker_upgrade_to_17',
                                            'tracking_upgrade_to_17',
                                            'wiki_upgrade_to_17');

                    foreach ( $function_list as $function )
                    {
                        $step = $function($currentCourseCode);
                        if ( $step > 0 )
                        {
                            echo 'Error : ' . $function . ' at step ' . $step . '<br />';
                            $error = true;
                        }
                    }

                    if ( ! $error )
                    {
                        // Upgrade succeeded
                        clean_upgrade_status($currentCourseCode);
                        $currentCourseVersion = '1.7';
                    }
                    else
                    {
                        // Upgrade failed
                        $currentCourseVersion = 'error-1.6';
                    }
                    // Save version
                    save_course_current_version($currentCourseCode,$currentCourseVersion);

                }

                /*---------------------------------------------------------------------
                  Upgrade 1.7 to 1.8
                 ---------------------------------------------------------------------*/

                if ( preg_match('/^1.7/',$currentCourseVersion) )
                {
                    // Function to upgrade tool to 1.8
                    $function_list = array( 'course_repository_upgrade_to_18',
                                            'group_upgrade_to_18',
                                            'tool_list_upgrade_to_18',
                                            'quiz_upgrade_to_18',
                                            'tool_intro_upgrade_to_18',
                                            'tracking_upgrade_to_18',
                                            'forum_upgrade_to_18' );

                    foreach ( $function_list as $function )
                    {
                        $step = $function($currentCourseCode);
                        if ( $step > 0 )
                        {
                            echo 'Error : ' . $function . ' at step ' . $step . '<br />';
                            $error = true;
                        }
                    }

                    if ( ! $error )
                    {
                        // Upgrade succeeded
                        clean_upgrade_status($currentCourseCode);
                        $currentCourseVersion = '1.8';
                    }
                    else
                    {
                        // Upgrade failed
                        $currentCourseVersion = 'error-1.7';
                    }
                    // Save version
                    save_course_current_version($currentCourseCode,$currentCourseVersion);

                }
                
                /*---------------------------------------------------------------------
                  Upgrade 1.8 to 1.9
                 ---------------------------------------------------------------------*/

                if ( preg_match('/^1.8/',$currentCourseVersion) )
                {
                    // Function to upgrade tool to 1.8
                    $function_list = array( 'tool_list_upgrade_to_19',
                                            'tracking_upgrade_to_19',
                                            'calendar_upgrade_to_19',
                                            'chat_upgrade_to_19',
                                            'course_description_upgrade_to_19',
                                            'linker_upgrade_to_19',
                                            'quiz_upgrade_to_19',
                                            'forum_upgrade_to_19'
                                    );
                    
                    if( isset($_SESSION['upgrade_tracking_data']) && $_SESSION['upgrade_tracking_data'])
                    {
                        $function_list[] = 'tracking_data_upgrade_to_19';
                    }
            
                    foreach ( $function_list as $function )
                    {
                        $step = $function($currentCourseCode);
                        if ( $step > 0 )
                        {
                            echo 'Error : ' . $function . ' at step ' . $step . '<br />';
                            $error = true;
                        }
                    }

                    if ( ! $error )
                    {
                        // Upgrade succeeded
                        clean_upgrade_status($currentCourseCode);
                        $currentCourseVersion = '1.9';
                    }
                    else
                    {
                        // Upgrade failed
                        $currentCourseVersion = 'error-1.8';
                    }
                    // Save version
                    save_course_current_version($currentCourseCode,$currentCourseVersion);

                }
                
                /*---------------------------------------------------------------------
                  Upgrade 1.9 to 1.10
                 ---------------------------------------------------------------------*/

                if ( preg_match('/^1.9/',$currentCourseVersion) )
                {
                    // Function to upgrade tool to 1.10
                    
                    $function_list = array();
                    
                    $toolCLANN =  get_module_data('CLANN');
                    if (is_tool_activated_in_course($toolCLANN['id'],$currentCourseCode))
                    {
                        $function_list[] = 'announcements_upgrade_to_110';
                    }
                    
                    $toolCLCAL =  get_module_data('CLCAL');
                    if (is_tool_activated_in_course($toolCLCAL['id'],$currentCourseCode))
                    {
                        $function_list[] = 'calendar_upgrade_to_110';
                    }
                    
                    $function_list[] = 'tool_intro_upgrade_to_110';
                    
                    $toolCLQWZ =  get_module_data('CLQWZ');
                    if (is_tool_activated_in_course($toolCLQWZ['id'],$currentCourseCode))
                    {
                        $function_list[] = 'exercise_upgrade_to_110';
                    }
                    
                    foreach ( $function_list as $function )
                    {
                        $step = $function($currentCourseCode);
                        if ( $step > 0 )
                        {
                            echo 'Error : ' . $function . ' at step ' . $step . '<br />';
                            $error = true;
                        }
                    }

                    if ( ! $error )
                    {
                        // Upgrade succeeded
                        clean_upgrade_status($currentCourseCode);
                        $currentCourseVersion = '1.10';
                    }
                    else
                    {
                        // Upgrade failed
                        $currentCourseVersion = 'error-1.9';
                    }
                    // Save version
                    save_course_current_version($currentCourseCode,$currentCourseVersion);

                }

            }


            if ( ! $error )
            {
                if ( preg_match('/^1.10/',$currentCourseVersion) )
                {
                    $message .= '<p>Ce cours est maintenant en version '.$currentCourseVersion.' </p>';
                    // course upgraded
                    $count_course_upgraded++;
                }
                else
                {
                    // course version unknown
                    $count_course_error++;
                    $message .= '<p class="error">Course version unknown : ' . $currentCourseVersion . '</p>';
                    log_message('Course version unknown : ' . $currentCourseVersion . '(in ' . $currentCourseCode . ')');
                }
            }
            else
            {
                $count_course_error++;
                $message .= '<p class="error">Upgrade failed</p>';
            }

            // display message
            echo $message;

           

        } // end of course upgrade

        $mtime = microtime(); $mtime = explode(" ",$mtime);    $mtime = $mtime[1] + $mtime[0];    $endtime = $mtime; $totaltime = ($endtime - $starttime);

        if ( $count_course_error > 0 )
        {
            /*
             * display block with list of course where upgrade failed
             * add a link to retry upgrade of this course
             */

            $sql = "SELECT code
                    FROM `" . $tbl_course . "`
                    WHERE versionClaro like 'error-%' ";

            $result = claro_sql_query($sql);

            if ( mysql_num_rows($result) )
            {
                echo '<p  class="error">Upgrade tool is not able to upgrade the following courses : ';
                while ( ( $course = mysql_fetch_array($result)) )
                {
                    echo $course['code'] . ' ; ';
                }
                echo  '</p>';

            }

            exit(1);
        }
        else
        {
            // display next step
            echo 'bilan : '.$count_course_upgraded. ' cours mis a jour '; 
            echo '<h3 >Etape 3 : OK</h3>' . "\n";
        }
        
        //on pousuit par la maj des modules
        if ( ! $error )
        {
        $new_version_branch = '';
        $patternVarVersion = '/^1.10/';
        // Initialise Upgrade
        require_once 'upgrade_init_global.inc.php';
         

/*
 * Initialize version variables
 */

// Current Version
$current_version = get_current_version();
$currentClarolineVersion = $current_version['claroline'];
$currentDbVersion = $current_version['db'];

// New Version
$this_new_version = get_new_version();
$new_version = $this_new_version['complete'];
$new_version_branch = $this_new_version['branch'];

if ( $cmd == 'run' )
            {
             echo '<h2>Etape 4/4:Desactivation des  modules incompatibles </h2>';
             // DB tables definition
            $tbl_mdb_names       = claro_sql_get_main_tbl();
            $tbl_module          = $tbl_mdb_names['module'];
            $tbl_module_info     = $tbl_mdb_names['module_info'];
            $tbl_module_contexts = $tbl_mdb_names['module_contexts'];

            $modules = claro_sql_query_fetch_all( "SELECT label, id, name FROM `{$tbl_module}`" );

            $deactivatedModules = array();
            $readOnlyModules = array( 'CLDOC', 'CLGRP', 'CLUSR' );

            foreach ( $modules as $module )
            {
                $manifest = readModuleManifest( get_module_path($module['label']) );

                if ( $manifest )
                {
                    $version = $manifest['CLAROLINE_MAX_VERSION'];

                    if ( ! in_array( $module['label'], $readOnlyModules ) && ! preg_match( $patternVarVersion, $version ) )
                    {
                        deactivate_module($module['id']);
                        $deactivatedModules[] = $module;
                    }
                }
            }

            $display = DISPLAY_RESULT_SUCCESS_PANEL;

        }
        switch ($display)
                        {
                            
                            case DISPLAY_RESULT_ERROR_PANEL :
                                echo '<h2>Step 4 of 4: disable incompatible modules - <span class="error">Failed</span></h2>';
                                exit(1);
                                // echo $output;
                                //echo '<center><p><button onclick="document.location=\'' . $_SERVER['PHP_SELF'] . '?cmd=run\';">Relaunch</button></p></center>';
                                break;

                            case DISPLAY_RESULT_SUCCESS_PANEL :

                                if ( !empty( $deactivatedModules ) )
                                {
                                    $output = '<h3>Modules desactives : </h3>';
                                    $output .= '<ul>';

                                    foreach ( $deactivatedModules as $module )
                                    {
                                        $output .= '<li>' . $module['name'] . '</li>';
                                    }

                                    $output .= '</ul>';
                                }
                                else
                                {
                                    $output = 'Aucun'. "\n";
                                }
                                echo $output;
                                echo '<h3>Etape 4/4 : OK<h3>'. "\n";
                                echo '<p >Mise a jour de la plateforme en version '.$new_version.' terminee</p>' . "\n";
                                
                                break;
                        }
            }
        }
}
?>
