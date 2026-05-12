<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\helpers;

class AnnotationHelper
{
    /**
     * Copied from PHPUnit 3.7.29, Util/Test.php
     * @param string $docblock Full method docblock
     * @return array Array of arrays.
     *               Key is the "@"-name like "param",
     *               each value is an array of the rest of the @-lines
     */
    public static function parseAnnotations($docblock): array
    {
        $annotations = [];
        // Strip away the docblock header and footer
        // to ease parsing of one line annotations
        $docblock = substr($docblock, 3, -2);
        $re = '/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m';
        if (preg_match_all($re, $docblock, $matches)) {
            $numMatches = count($matches[0]);

            for ($i = 0; $i < $numMatches; ++$i) {
                $annotations[$matches['name'][$i]][] = $matches['value'][$i];
            }
        }

        return $annotations;
    }

}
