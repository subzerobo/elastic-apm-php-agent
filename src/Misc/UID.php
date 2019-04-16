<?php
/**
 * Created by PhpStorm.
 * User: alikaviani
 * Date: 2019-04-14
 * Time: 10:23
 */

namespace Subzerobo\ElasticApmPhpAgent\Misc;


class UID
{
    /**
     * Generate Very Strong Random String
     * @param $min
     * @param $max
     *
     * @return int
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-02-23 13:13
     */
    public static function crypto_rand_secure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    /**
     * Generate Strong Random UID
     *
     * @param int  $length
     *
     * @return string
     * @author alikaviani <a.kaviani@sabavision.ir>
     * @since  2019-02-23 13:13
     */
    public static function Generate ($length = 16)
    {
        $token = "";
        $codeAlphabet = "0123456789abcdef";;
        $max = strlen($codeAlphabet);
        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[self::crypto_rand_secure(0, $max-1)];
        }
        return $token;
    }
}