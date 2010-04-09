<?php
/* Custom theme for use with WebCalendar.
 *
 * Default System Settings.
 *
 * @author Ray Jones <rjones@umces.edu>
 * @copyright Craig Knudsen, <cknudsen@cknudsen.com>, http://www.k5n.us/cknudsen
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL
 * @version $Id: default_admin.php,v 1.6.2.2 2007/08/06 02:28:33 cknudsen Exp $:
 * @package WebCalendar
 */
// Define your stuff here...
// Any option in webcal_user_pref can be configured here.
// This theme will reset the System Settings to the default values from the
// installation script. This will not affect colors or options that users have
// already saved under preferences.
$webcal_theme = array ( 
  'MENU_THEME'   					 => 'lcs',
  'BGCOLOR'                          => '#f8f8ff',
  'BOLD_DAYS_IN_YEAR'                => 'Y',
  'CELLBG'                           => '#FFFFFF',
  'H2COLOR'                          => '#400040',
  'HASEVENTSBG'                      => '#8AA6FF',
  'OTHERMONTHBG'                     => '#D0D0D0',
  'POPUP_BG'                         => '#E5F1FF',
  'POPUP_FG'                         => '#000000',
  'TABLEBG'                          => '#B0B0B0',
  'TEXTCOLOR'                        => '#000086',
  'THBG'                             => '#0000A2',
  'THFG'                             => '#FFFFC0',
  'TIME_FORMAT'                      => '24',
  'TIME_SLOTS'                       => '48',
  'TIMED_EVT_LEN'                    => 'D',
  'TODAYCELLBG'                      => '#D7D7FF',
  'WEEK_START'                       => '1',
  'WEEKENDBG'                        => '#F3F3F3',
  'WORK_DAY_END_HOUR'                => '18',
  'WORK_DAY_START_HOUR'              => '8',
  'DISPLAY_WEEKENDS'      			 => 'Y',
  'STARTVIEW'                        => 'week.php',
  'SERVER_TIMEZONE'                  => 'Europe/Paris',
  'TIMEZONE'  		                 => 'Europe/Paris',
  'WEEKEND_START'                    => '6',
  'APPLICATION_NAME'                 => 'Agendas LCS',
  'DATE_FORMAT'                      => '__dd__ __month__ __yyyy__',
  'DATE_FORMAT_MD'                   => '__dd__ __month__',
  'DATE_FORMAT_MY'                   => '__month__ __yyyy__',
  'FONTS'                            => 'Arial, Helvetica, sans-serif',
  'CATEGORIES_ENABLED'               => 'Y',
  'MYEVENTS'						 => '#000066',
  'USER_SEES_ONLY_HIS_GROUPS'        => 'Y',
  'GROUPS_ENABLED'                   => 'Y',
  'WEEKNUMBER'						 => '#0000B0',
  'DISPLAY_WEEKNUMBER'               => 'N',
  'MENU_DATE_TOP' 					 => 'Y',
  'ALLOW_VIEW_OTHER'				 => 'Y',
  'NONUSER_ENABLED'					 => 'N',
  'PUBLISH_ENABLED'					 => 'N',
  'UPCOMING_EVENTS'		 			 => 'Y',
  'ALLOW_ATTACH' 					 => 'Y',
  'UPCOMING_DISPLAY_LAYERS'			 => 'Y',
  'RSS_ENABLED'						 => 'Y',
  );

include 'theme_inc.php';

?>
