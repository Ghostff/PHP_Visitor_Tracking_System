<?php

class Stalk
{
    private static $ip = null;
	
	private static $instantiated = null;
    
    public static function __callStatic($name, $arg)
    {
		
		if ( ! self::$instantiated) {
			$stalk = new Stalk;
			self::$instantiated = $stalk->get($arg);
		}
        $stalk = self::$instantiated;
        
        
        $error = null;
        if ($name == 'all') {
            return $stalk;
        }

        if (property_exists($stalk, $name)) {
            
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
    
    public static function ip($ip = null)
    {
        if ($ip) {
            self::$ip = $ip;
        }
        elseif (self::$ip) {
            return self::$ip;
        }
        else{
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
            return $ip;
        }
       
    }
    
    public static function browser($type = null, $object = false)
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
        
        $browse_data = array(
            'name'       => $bname, 
            'version'    => $version, 
            'OS'         => $platform
        );
        
        if ($type) {
            
            if (array_key_exists($type, $browse_data)) {
                return $browse_data[$type];
            }
            else {
                return 'they key:' . $type . ' does not exists in browsers data';
            }
        }

        return ($object) ? (object) $browse_data : $browse_data;
    }
    
    public function get($as_array = false)
    {
        define('DIR', __DIR__ .'/lib/');
        include  DIR . 'geoipcity.inc';
        include  DIR . 'geoipregionvars.php';
		
		
		//delete (if u have maxmind downloaded) start ->
		if ( ! file_exists(DIR . 'GeoLiteCity.dat')) {
			die('You need to <a 
				href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz"
				style="font-weight:bold; color:#0188B1;">download</a>
				and extract the <code>GeoLiteCity.dat</code> file to <code>src/lib</code> directory'
			);
		} // <- end
		
        $gi      = geoip_open(DIR . 'GeoLiteCity.dat', GEOIP_STANDARD);
        $record  = geoip_record_by_addr($gi, self::ip());
        
        $record->state   = $GEOIP_REGION_NAME[$record->country_code][$record->region];
        $record->ip      = self::ip();    
        $record->browser = self::browser(null, ($as_array) ? false : true);
        
        return ($as_array) ? (array) $record : $record;
    }
}