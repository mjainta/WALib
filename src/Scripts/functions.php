<?php
/**
 * PHP functions which should be available in the whole application.
 *
 * @package WALib
 */

/**
 * Returns an string with converted HTML entities.
 *
 * Converts single and double quotes, too.
 *
 * @param string $string
 * @return string
 */
function lib_htmlspecialchars($string)
{
    return htmlspecialchars($string, ENT_QUOTES);
}