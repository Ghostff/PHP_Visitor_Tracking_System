<?php

class Stalk
{
    private static $record = null;

    public static function ip($ip = null)
    {
        if (! $ip) {
            $client  = @$_SERVER['HTTP_CLIENT_IP'];
            $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
            $remote  = $_SERVER['REMOTE_ADDR'];
            if (filter_var($client, FILTER_VALIDATE_IP)) {
                $ip = $client;
            }
            elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
                $ip = $forward;
            }
            else {
                $ip = $remote;
            }
        }
        return $ip;
    }

    private static function init($ip = null)
    {
        if (self::$record) {
            return;
        }

        $lib = __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;
        include  $lib . 'geoipcity.inc';
        include  $lib . 'geoipregionvars.php';

        if (! file_exists($lib . 'GeoLiteCity.dat')) {
            throw new Exception('Missing GeoLiteCity.dat file, you can download GeoLiteCity.dat file from 
                (http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz) and extract to ' . $lib);
        }

        $ip = self::ip($ip);
        $gi      = geoip_open($lib . 'GeoLiteCity.dat', GEOIP_STANDARD);
        $record  = geoip_record_by_addr($gi, $ip);

        $record->state   = $GEOIP_REGION_NAME[$record->country_code][$record->region];
        $record->ip      = self::ip();
        $record->browser = self::browser();
    }
    
    public static function browser()
    {
        $user_agent     = $_SERVER['HTTP_USER_AGENT'];
        $platform       = 'Unknown';
        $browser_name   = 'Unknown';
        $version        = '';
        
        if (preg_match('/linux/i', $user_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $user_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $user_agent)) {
            $platform = 'windows';
        }
        if(preg_match('/MSIE/i', $user_agent) && ! preg_match('/Opera/i',$user_agent)) {
            $browser_name = 'Internet Explorer';
            $ub = 'MSIE';
        }
        elseif(preg_match('/Firefox/i', $user_agent)){
            $browser_name = 'Mozilla Firefox';
            $ub = 'Firefox';
        }
        elseif(preg_match('/Chrome/i', $user_agent)) {
            $browser_name = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i', $user_agent)){
            $browser_name = 'Apple Safari';
            $ub = 'Safari';
        }
        elseif(preg_match('/Opera/i', $user_agent)){
            $browser_name = 'Opera';
            $ub = 'Opera';
        }
        elseif(preg_match('/Netscape/i',$user_agent)){
            $browser_name = 'Netscape';
            $ub = 'Netscape';
        }
        
        $known = array('Version', $ub, 'other');
        
        $join = join('|', $known);
        $pattern = '#(?<browser>' . $join . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
                
        preg_match_all($pattern, $user_agent, $matches);
        $i = count($matches['browser']);
        
        if ($i != 1) {    
            if (strripos($user_agent,'Version') < strripos($user_agent,$ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        }
        else{
            $version= $matches['version'][0];
        }
        
        if ($version == null || $version == '') {
            $version = '?';
        }

        return (object) array(
            'name'       => $browser_name,
            'version'    => $version, 
            'OS'         => $platform
        );
    }
    
    public static function get($ip = null)
    {
        static::init($ip);
        return self::$record;
    }
}