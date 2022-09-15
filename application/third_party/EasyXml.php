<?php 
class EasyXml extends EasyCode {
	
	public static function array_to($arr) {
		$xml = '';
		foreach ($arr as $k => $v) {
			$pos = strpos($k, ' ');
			if ($pos) {
				$k_end = substr($k, 0, $pos);
			} else {
				$k_end = $k;
			}
			if (is_scalar($v)) {
				if (is_string($v)) {
					$xml .= "<{$k}><![CDATA[{$v}]]></{$k_end}>\n";
				} else {
					$xml .= "<{$k}>{$v}</{$k_end}>\n";
				}
			} else {
				$xml .= self::array2xml($v, $k, $k_end);
			}
		}
		return "<xml>$xml</xml>";
	}
	
	public static function array2xml($arr, $base_k, $base_k_end) {
		$xml = '';
		
		$pos_key = key($arr);
		if ( ! isset($pos_key[0]) /* 不是字符串类型 */) {
			foreach ($arr as $k => $v) {
				if (is_scalar($v)) {
					if (is_string($v)) {
						$xml .= "<{$base_k}><![CDATA[{$v}]]></{$base_k_end}>\n";
					} else {
						$xml .= "<{$base_k}>{$v}</{$base_k_end}>\n";
					}
				} else {
					$xml .= self::array2xml($v, $base_k, $base_k_end);
				}
			}
			return "{$xml}\n";
		} else {
			foreach ($arr as $k => $v) {
				$pos = strpos($k, ' ');
				if ($pos) {
					$k_end = substr($k, 0, $pos);
				} else {
					$k_end = $k;
				}
				if (is_scalar($v)) {
					if (is_string($v)) {
						$xml .= "<{$k}><![CDATA[{$v}]]></{$k_end}>\n";
					} else {
						$xml .= "<{$k}>{$v}</{$k_end}>\n";
					}
				} else {
					$xml .= self::array2xml($v, $k, $k_end);
				}
			}
			return "<{$base_k}>{$xml}</{$base_k_end}>\n";
		}
	}
	
	public static function to_array($str) {
		if (empty($str)) {
			return [];
		} else {
			$xml = simplexml_load_string($str, 'SimpleXMLElement', LIBXML_NOCDATA);
			
			$data = json_decode(json_encode($xml), TRUE);
			return self::_empty_to_str($data);
		}
	}
	
	private static function _empty_to_str(array $data) {
		foreach ($data as $k => $v) {
			if (empty($v) && $v !== 0) {
				$data[$k] = '';
			} elseif (is_array($v)) {
				$data[$k] = self::_empty_to_str($v);
			}
		}
		return $data;
	}
}
/*  */