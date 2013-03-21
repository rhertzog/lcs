<?php

// $Id: textzone.lib.php 14370 2013-01-30 14:52:26Z zefredz $

/**
 *
 * @version     1.11 $Revision: 14370 $
 * @copyright   (c) 2001-2012, Universite catholique de Louvain (UCL)
 * @license     http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
 * @author      Christophe Gesché <moosh@claroline.net>
 * @since       1.8.1
 * @package     KERNEL
 */
class claro_text_zone
{

    /**
     * Build the file path of a textzone in a given context
     *
     * @param string $key
     * @param array $context specify the context to build the path.
     * @param array $right specify an array of right to specify the file
     * @return file path
     */
    public static function get_textzone_file_path ( $key, $context = null, $right = null )
    {
        $textZoneFile = null;
        $key .= '.';

        if ( !is_null ( $right ) && is_array ( $right ) )
        {
            foreach ( $right as $context => $rightInContext )
            {
                if ( is_array ( $rightInContext ) )
                {
                    $key .= $context . '_';
                    
                    foreach ( $rightInContext as $rightName => $rightValue )
                    {
                        if ( is_bool ( $rightValue ) )
                        {
                            $key .= ($rightValue) ? $rightName : 'not_' . $rightName;
                        }
                        else
                        {
                            $key .= $rightName . '_' . $rightValue;
                        }
                        
                        $key .= '.';
                    }
                }
            }
        }
        
        if ( is_array ( $context ) && array_key_exists ( CLARO_CONTEXT_COURSE, $context ) )
        {
            if ( is_array ( $context ) && array_key_exists ( CLARO_CONTEXT_GROUP, $context ) )
            {
                $textZoneFile = get_conf ( 'coursesRepositorySys' ) 
                    . claro_get_course_group_path ( $context ) 
                    . '/textzone/' . $key . 'inc.html'
                    ;
            }
            else
            {
                $textZoneFile = get_conf ( 'coursesRepositorySys' ) 
                    . claro_get_course_path ( $context[ CLARO_CONTEXT_COURSE ] ) 
                    . '/textzone/' . $key . 'inc.html'
                    ;
            }
        }
        
        if ( is_null ( $textZoneFile ) )
        {
            $textZoneFile = get_path ( 'rootSys' ) 
                . 'platform/textzone/' . $key . 'inc.html'
                ;
        }

        return $textZoneFile;
    }

    /**
     * return the content
     *
     * @param coursecode $key
     * @param array $context
     * @return string : html content
     */
    public static function get_content ( $key, $context = null, $right = null )
    {
        $textZoneFile = claro_text_zone::get_textzone_file_path ( $key, $context, $right );

        if ( file_exists ( $textZoneFile ) )
        {
            $content = file_get_contents ( $textZoneFile );
        }
        else
        {
            $content = '';
        }
        
        return $content;
    }

}
