<?php 
class EasyIni extends EasyCode {
	
	public static function array_to($arr, $k_='', $n=0) {
		if ($n===1) {
			$ini_str = "[{$k_}]\r\n";
		} elseif ($n===3) {
			die('ini不能超过三层');
		} else {
			$ini_str = '';
		}
		

		foreach ($arr as $k => $v) {
			
			if (is_scalar($v)) {
				if (isset($v[0]) && isset($v[strcspn($v, "!$^&()=\";'\n")])) {
					if (strpos($v, '"') !== FALSE) {
						$v = json_encode($v, JSON_UNESCAPED_UNICODE);
					} else {
						$v = '"'.$v.'"';
					}
				}
				
				if ($n > 1) {
					$ini_str .= "{$k_}[{$k}]={$v}\r\n";
				} else {
					$ini_str .= "{$k}={$v}\r\n";
				}
			} else {
				$ini_str .= self::array_to($v, $k, $n+1);
			}
		}
		return $ini_str;
	}
	
	public static function to_array($str) {
		if (empty($str)) {
			return [];
		} else {
			return parse_ini_string($str, TRUE);
		}
	}
	
	// protected static function 
}
/*  */