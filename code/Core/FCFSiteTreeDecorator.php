<?php

class FirstClickFree {

	protected static
		$bot_identifying_UA_items = array(
			'Google' => array(
				'UAs' => array(
					'Googlebot'
				),
				'hosts' => array(
					'.googlebot.com'
				),
				'IPs' => array(
				)
			)
		),
		$customAgents = array();

	static function remove_bot($name) {
		if (is_array($name)) {
			foreach ($names as $name) {
				self::remove_bot($name);
			}
		}
		unset(self::$bot_identifying_UA_items[$name]);
	}

	static function add_custom_agent($name,array $agentInfo) {
		self::$customAgents[$name] = $agentInfo;
	}

	static function add_custom_agents(array $customAgents) {
		self::$customAgents = array_merge(self::$customAgents,$customAgents);
	}

	static function get_all_agents() {
		return array_merge(self::$bot_identifying_UA_items,self::$customAgents);
	}

	static function is_bot() {
		//we'll cache if the person is a bot so that this logic isn't executed
		// everytime we load a page.
		if (Session::get('_FCFisBot') === null) {
			Session::set('_FCFisBot',false);
			$ip = SS_HTTPRequest::getIP();
			$hostFromIP = gethostbyaddr($ip);
			$ipFromHost = gethostbyname($ip);
			//do the logic
			foreach (self::get_all_agents() as $botName => $botDetails) {
				if (!empty($botDetails['UAs']) && self::checkUA($botDetails['UAs'])) {
					if (
						(!empty($botDetails['IPs']) && self::checkIP($botDetails['IPs'],$ip))
						||
						(!empty($botDetails['hosts']) && self::check_host_lookups($botDetails['hosts'],$hostFromIP,$ipFromHost,$ip))
					)
						Session::set('_FCFisBot',true);
						break;
					}
				}
			}

		//if it is in the session, return true
		if (Session::get('_FCFisBot')) {
			return true;
		}
	}

	static function checkUA($match) {
		if (is_array($match)) {
			foreach ($match as $term) {
				if (self::checkUA($term)) {
					return true;
				}
			}
		}
		return strpos($_SERVER['HTTP_USER_AGENT'],$match) !== false;
	}

	static function checkIP($allowedIPs,$ip) {
		if (is_array($allowedIPs)) {
			return in_array($ip,$allowedIPs);
		}
		return $allowedIPs == $ip;
	}

	function check_host_lookups($hosts,$hostFromIP,$ipFromHost,$ip) {
		return self::checkHost($hosts,$hostFromIP) && $ipFromHost == $ip;
	}

	static function checkHost($allowedHosts,$hostFromIP) {
		//this needs to check the END of the host matches the host
		if (is_array($allowedHosts)) {
			return in_array($hostFromIP,$allowedHosts);
		}
		return $allowedHosts == $hostFromIP;
	}

}
