<?php
/**
    diff.inc.php - diff functions
    Diff implemented in pure php, written from scratch.
    found at http://www.holomind.de/phpnet/diff.src.php
    Copyright (C) 2003  Daniel Unterberger <diff.phpnet@holomind.de>
    Copyright (C) 2005  Didier Loiseau
    
    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
    
    http://www.gnu.org/licenses/gpl.html

    About:
    I searched a function to compare arrays and the array_diff()
    was not specific enough. It ignores the order of the array-values.
    So I reimplemented the diff-function which is found on unix-systems
    but this you can use directly in your code and adopt for your needs.
    Simply adopt the formatline-function. with the third-parameter of arr_diff()
    you can hide matching lines. Hope someone has use for this.

    Contact: d.u.diff@holomind.de <daniel unterberger>
**/

if (!defined('WIKINI_VERSION'))
{
    exit;
}

if (defined('WIKINI_DIFF_FUNCTIONS'))
{
    return;
}
define('WIKINI_DIFF_FUNCTIONS', true);

/**
 * Computes a diff between two arrays of strings
 * @param array $f1 An array of strings (supposed to be the older text)
 * @param array $f2 An array of strings (supposed to be the newer text)
 * @param boolean $show_equal If true, the result will also contain the common lines
 * (default false)
 * @return array An array of arrays describing the differencies between $f1 and $2.
 * Each array describes one line and is structured like this:
 * array {
 *         'type' => value,    # the type of diffence
 *         0 => 'a line',         # a line from $f1 or $f2
 *         1 => 'another line'    # if usefull, a line from $f2
 * }
 * The type describes the difference and the other values depend on the type:
 *         - for a removed line: 0 is the removed line, 1 is not set
 *         + for an added line: 0 is the added line, 1 is not set
 *         = for an unchanged line (only if $show_equal): 0 is the common line, 1 is not set
 *         c for a changed line: 0 is the old line, 1 is the new one
 */

function arr_diff( $f1 , $f2 , $show_equal = false )
{

    $c1         = 0 ;                   # current line of left
    $c2         = 0 ;                   # current line of right
    $max1       = count( $f1 ) ;        # maximal lines of left
    $max2       = count( $f2 ) ;        # maximal lines of right
    $hit1       = "" ;                  # hit in left
    $hit2       = "" ;                  # hit in right
    $stop        = 0;                    # stop flag
    $out        = array();                # output buffer
    $trimf1        = array();
    $trimf2        = array();
    
    
    foreach ($f1 as $key => $value)
    {
        $trimf1[$key] = trim($value);
    }
    
    foreach ($f2 as $key => $value)
    {
        $trimf2[$key] = trim($value);
    }

    while (
            $c1 < $max1                 # have next line in left
            &&                 
            $c2 < $max2                 # have next line in right
            &&
            $stop++ < 1000              # don't have more than 1000 ( loop-stopper )
          )
    {
        /*
         * ignore empty lines
         *
        if (empty($trimf1[$c1]))
        {
            $c1++;
        }
        elseif (empty($trimf2[$c2]))
        {
            $c2++;
        }*/
        
        /*
        *   is the trimmed line of the current left and current right line
        *   the same ? then this is a hit (no difference)
        * /  
        else */ if ( $trimf1[$c1] == $trimf2[$c2] )    
        {
            /*
            *   Add this line to output if "show_equal" is enabled.
            *   This is more for demonstration purpose
            */
            if ( $show_equal )  
            {
                $out[] = array('type' => '=', &$f1[ $c1 ]);
            }
            
            /**
            *   move the current-pointer in the left and right side
            */
            $c1 ++;
            $c2 ++;
        }

        /*
        *   the current lines are different so we search in parallel
        *   on each side for the next matching pair, we walk on both
        *   sided at the same time comparing with the current-lines
        *   this should be most probable to find the next matching pair
        *   we only search in a distance of 10 lines, because then it
        *   is in the same paragraph most of the time. other algos
        *   would be very complicated, to detect 'real' block movements.
        */
        else
        {
            
            $b      = array() ;
            $s1     = 0  ;      # search on left
            $s2     = 0  ;      # search on right
            $b1     = array() ;      
            $b2     = array() ;
            $fstop  = 0  ;      # distance of maximum search

            #fast search in on both sides for next match.
            while (
                    $c1 + $s1 < $max1  # we are inside of the left lines
                    &&
                    $c2 + $s2 < $max2  # and we are inside of the right lines
                    &&     
                    $fstop++  < 10          # and the distance is lower than 10 lines
                  )
            {

                /**
                *   test the left side for a hit
                *
                *   comparing current line with the searching line on the left
                *   b1 is a buffer, which collects the line which not match, to
                *   show the differences later, if one line hits, this buffer will
                *   be used, else it will be discarded later
                */
                #hit
                if (!empty($trimf1[$c1+$s1]) && $trimf1[$c1+$s1] == $trimf2[$c2] )
                {
                    $c1 += $s1 - 1    ;    # move forward the current left, so next loop hits
                    $c2--            ;    # move back the current right, so next loop hits
                    $b      = $b1    ;    # set b=output (b)uffer
                    break            ;    # stop search
                }
                #no hit: move on
                else
                {
                        /**
                        *   add current search-line to diffence-buffer
                        */
                        $b1[] = array( 'type' =>  '-', &$f1[ $c1+$s1 ] );
                }



                /**
                *   test the right side for a hit
                *
                *   comparing current line with the searching line on the right
                */
                if (!empty($trimf2[$c2+$s2]) && $trimf1[$c1] == $trimf2[$c2+$s2] )
                {
                    $c2 += $s2 - 1    ;    # move forward the current right line, so next loop hits
                    $c1--         ;     # move current left line back, so next loop hits
                    $b      = $b2 ;     # get the buffered difference
                    break;
                }
                else
                {   
                        /**
                        *   add current searchline to buffer
                        */
                        $b2[] = array( 'type' =>  '+', &$f2[ $c2+$s2 ] );
                 }

                /**
                *   search in bigger distance
                *
                *   increase the search-pointers (satelites) and try again
                */
                $s1++ ;     # increase left  search-pointer
                $s2++ ;     # increase right search-pointer  
            }

            /**
            *   add line as different on both arrays (no match found)
            */
            if ( !$b  )
            {
                $out[] = array('type' => 'c', &$f1[$c1], &$f2[$c2]);
            }
            /**
            *   add current buffer to outputstring
            */
            else
            {
                $out = array_merge($out, $b);
            }

            $c1++  ;    # move current line forward
            $c2++  ;    # move current line forward

            /**
            *   comment the lines are tested quite fast, because
            *   the current line always moves forward
            */

        } /* endif */

    }/* endwhile */
    
    // lines might juste have been removed at the end...
    if ($c1 < $max1)
    {
        for ($i = $c1; $i < $max1; $i++)
        {
            $out[] = array('type' => '-', &$f1[$i]);
        }
    }
    // ... or added
    elseif ($c2 < $max2)
    {
        for ($i = $c2; $i < $max2; $i++)
        {
            $out[] = array('type' => '+', &$f2[$i]);
        }
    }
    
    return $out;

}/* end func */

/**
 * Computes the diff between two texts, line by line.
 * The lines are supposed to be separated by a NL ("\n")
 * @param string $textA The old text
 * @param string $textB The new text
 * @param boolean $show_equal If true, the result will also contain the common lines
 * (default false)
 * @return array An array of arrays describing the diff
 * @see arr_diff for the return value
 */
function text_diff_by_lines($textA, $textB, $show_equal = false)
{
    return arr_diff(explode("\n", $textA), explode("\n", $textB), $show_equal);
}
?> 