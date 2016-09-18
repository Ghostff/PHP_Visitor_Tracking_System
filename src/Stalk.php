<?php

class Stalk
{
    public static $ip = null;
    
    public static function __callStatic($name, $arg)
    {
        $stalk = new Stalk;
        $stalk = $stalk->get();
        
        $error = null;
        if ($name == 'all') {
            return $stalk;
        }
        elseif (property_exists($stalk, $name)) {
            
            if (isset($arg[0])) {
                if (is_object($stalk->{$name})) {
                    return $stalk->{$name}->{$arg[0]};
                }
                $error = '(Stalk->' . $name . '()) does not accept arguments';
            }
            else {
                return $stalk->{$name};    
            }
        }
        else {
            $error = 'Property (' . $name . ') does not exist';    
        }
        throw new Exception($error);
    }
    
    private function getUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }
        else{
            $ip = $remote;
        }
        static::$ip =  $ip;
    }
    
    private function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $platform = 'Unknown';
        $bname = 'Unknown';
        $version= "";
        
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Firefox/i',$u_agent)){
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent)){
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent)){
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent)){
            $bname = 'Netscape';
            $ub = "Netscape";
        }
        
        $known = array('Version', $ub, 'other');
        
        $join = join('|', $known);
        $pattern = '#(?<browser>' . $join . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
                
        preg_match_all($pattern, $u_agent, $matches);
        $i = count($matches['browser']);
        
        if ($i != 1) {    
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)) {
                $version = $matches['version'][0];
            }
            else {
                $version = $matches['version'][1];
            }
        }
        else{
            
            $version= $matches['version'][0];
        }
        
        if ($version == null || $version == ""){
            $version = "?";
        }
        return (object) array(
            'name' => $bname, 
            'version' => $version, 
            'OS' => $platform
        );
    }
    
    public function get()
    {
        define('DIR', __DIR__ .'/lib/');
        include  DIR . 'geoipcity.inc';
        include  DIR . 'geoipregionvars.php';

        
        $gi      = geoip_open(DIR . 'GeoLiteCity.dat', GEOIP_STANDARD);
        $record  = geoip_record_by_addr($gi, static::$ip);
        
        $record->state   = $GEOIP_REGION_NAME[$record->country_code][$record->region];
        $record->ip      = static::$ip;    
        $record->browser = $this->getBrowser();

        return $record;
    }
}