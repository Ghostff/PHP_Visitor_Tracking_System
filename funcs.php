<?php
require_once('conn.inc');
class stalk
{
	public function __construct($track_uniq, $track_page, $indv_page)
	{
		global $mysqli;
		if(!session_id())
			session_start();
		define('TRACK_UNIQ_ONLY', $track_uniq);
		define('TRACK_PAGES', $track_page);
		define('INDIV_PAGES', $indv_page);
		//make table
		//echo ($this->make_table($mysqli))?'table created':'error';
		$this->render($mysqli);
	}
	//delete this function after creating the table
	//connect to DB
	//$mysqli = @new mysqli(DB_NAME, DB_USER, DB_PASS, DB_TBLE);
	//create this table in DB with  
	//make_table($mysqli)
	private function make_table($connection)
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `visitors` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `session_token` int(11) NOT NULL,
				  `is_user` int(11) DEFAULT NULL,
				  `ip` varchar(80) NOT NULL,
				  `page` varchar(250) NOT NULL,
				  `browser` varchar(250) NOT NULL,
				  `browser_version` varchar(10) NOT NULL,
				  `OS` varchar(250) NOT NULL,
				  `country` varchar(300) NOT NULL,
				  `state` varchar(300) NOT NULL,
				  `city` varchar(300) NOT NULL,
				  `date` int(11) NOT NULL,
				  PRIMARY KEY (`session_token`),
				  KEY `id` (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
		return ($connection->query($sql) === TRUE)? true : false;
	}
	//returns use IP address
	//*note runing this on localhost you may come up with this error 
	//"Trying to get property of non-object in"
	//thats because the is no ip address
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
		return $ip;
	}
	//return absolute working dir
	//eg sitename.com/me/you/ return you
	//also sitename.com/me/you/?cnt=9ja return you
	private function pagename()
	{
		$url   = $_SERVER['PHP_SELF'];
		$stack = explode('/', $url);
		array_shift($stack);
		array_pop($stack);
		if(count($stack) > 0)
			$page = trim(str_replace('.php', '', end($stack)));
		else{
			$page = trim(str_replace(basename($url, '.php')));
			if($page == 'index'){
				$page = 'home';
			}
		}
		return @$page;
	}
	//return the following
	//browsers name
	//browser version
	//opprating system
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
		$pattern = '#(?<browser>' . join('|', $known) .')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)){}
		$i = count($matches['browser']);
		if ($i != 1){
			 $version = (strripos($u_agent,"Version") < strripos($u_agent,$ub))? $version= $matches['version'][0]:$version= $matches['version'][1];
		}
		else{
			$version= $matches['version'][0];
		}
		if ($version==null || $version == ""){
			$version = "?";
		}
		return array(
					'name' => $bname, 
					'version' => $version, 
					'platform' => $platform
					);
	}
	//return true if infomation was sucessfully saved
	//else to trubleshoot your statment for error
	//place this code right below prepare and above bind_param
	//var_dump(htmlspecialchars($connection->error));
	private function track($is_user = null, $ip, $page, $browser, $browser_version, $OS, $country, $state, $city, $date, $connection)
	{
		if(!TRACK_PAGES){
			if(!isset($_SESSION['track_status'])){
					while(!TRACK_PAGES){
						$token = mt_rand();
						$stmt = $connection->prepare("SELECT id FROM visitors WHERE session_token = ? LIMIT 1");
						$stmt->bind_param("i", $token);	
						$stmt->execute();
						$stmt->bind_result($id);
						$num_returns = $stmt->num_rows;
						$stmt->close();
						if($num_returns == 0){
							$_SESSION['track_status'] = $token;
							break;
						}else continue;
					}
			}
			else $token = false;
		}
		else{
			if(TRACK_UNIQ_ONLY){
				if(!isset($_SESSION['track_status'])){
					while(TRACK_UNIQ_ONLY){
						$token = mt_rand();
						$stmt = $connection->prepare("SELECT id FROM visitors WHERE session_token = ? LIMIT 1");
						$stmt->bind_param("i", $token);	
						$stmt->execute();
						$stmt->bind_result($id);
						$num_returns = $stmt->num_rows;
						$stmt->close();
						if($num_returns == 0){
							$_SESSION['track_status'] = $token;
							break;
						}else{
							continue;
						}
					}
				}
				else{
					if(INDIV_PAGES && isset($_SESSION['ARDYVSTD'])){
						$pages = chop($_SESSION['ARDYVSTD'], ' * ');
						$pages = explode(' * ', $pages);
						if(in_array($page, $pages)){
							$token = false;
						}
						else{
							$token = true;
						}
					}
					else{
						$token = $_SESSION['track_status'];
					}
				}
			}
			else{
				while(!TRACK_UNIQ_ONLY){
						$token = mt_rand();
						$stmt = $connection->prepare("SELECT id FROM visitors WHERE session_token = ? LIMIT 1");
						$stmt->bind_param("i", $token);	
						$stmt->execute();
						$stmt->bind_result($id);
						$num_returns = $stmt->num_rows;
						$stmt->close();
						if($num_returns == 0){
							$_SESSION['track_status'] = $token;
							break;
						}else continue;
					}
			}
		}
		if($token){
			$stmt = $connection->prepare("INSERT INTO visitors 
										(session_token, is_user, ip, page, browser, browser_version, OS, country, state, city, `date`)
										VALUES 
										(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
										ON DUPLICATE KEY UPDATE 
										page = CONCAT(page, ',', VALUES(page)), is_user = VALUES(is_user)
										");
			$stmt->bind_param("iissssssssi", $token, $is_user, $ip, $page, $browser, $browser_version, $OS, $country, $state, $city, $date);
			@$success = $stmt->execute();
			$stmt->close();
			@$_SESSION['ARDYVSTD'] .= $page.' * ';
			return (!$success)? false : true;
		}
		return true;
	}
	//using Maxmind Geocity 
	//go to http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz and download database
	//extract and put GeoLiteCity.dat inside models dir
	//the first parameter of track function is set to null. 
	//use can replace it with the user id. if you wonna track your users to
	//eg (track((note: logged_in_user_id instead of null), $ip, pagename(null), $browser['name'], $browser['version'], $browser['platform'], $record->country_name, $state, $record->city, time(), $db_con))
	private function render($db_con){
		include("models/geoipcity.inc");
		include("models/geoipregionvars.php");
		$gi 	 = geoip_open("models/GeoLiteCity.dat", GEOIP_STANDARD);
		$ip		 = $this->getUserIP();
		$record  = geoip_record_by_addr($gi, $ip);
		$state   = $GEOIP_REGION_NAME[$record->country_code][$record->region];
		$browser = $this->getBrowser();
		$this->track(null, //<-- replace with user_id users shld be tracked 2
						$ip, 
							$this->pagename(null), 
								$browser['name'], 
									$browser['version'], 
										$browser['platform'], 
											$record->country_name, 
												$state, 
													$record->city, 
														time(), 
															$db_con
					);
		geoip_close($gi);
		//session_destroy(); for test purpose
	}
}


?>
