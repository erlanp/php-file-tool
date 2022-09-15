<?php 
class EasyYaml extends EasyCode {
	
	public static function array_to($object, $maxDeep = 2) {
		return self::dump($object, $maxDeep, 0, false);
	}

	private static function dump($object, $maxLevel, $currLevel, $dumpMap) {
		if (is_scalar($object)) {
			return json_encode($object, JSON_UNESCAPED_UNICODE);
		}
		if ($currLevel > $maxLevel) {
			return self::jsonEncode($object);
		}

		$strBlank = self::getStrBlank($currLevel);
		$arr = [];
		if (self::isArray($object)) {
			foreach ($object as $k => $v) {
				$tmp = self::dump($v, $maxLevel, $currLevel + 1, false);
				if (!$dumpMap && $k === 0) {
					$arr[] = "- {$tmp}";
				} else {
					$arr[] = "{$strBlank}- {$tmp}";
				}
			}
		} else if (is_array($object) || $object instanceof ArrayObject || $object instanceof stdClass) {
			$i = 0;
			foreach ($object as $k => $v) {
				$tmp = self::dump($v, $maxLevel, $currLevel + 1, true);
				$tmp_k = self::getKey($k);
				if (!$dumpMap && $i === 0) {
					$arr[] = "{$tmp_k}: {$tmp}";
				} else {
					$arr[] = "{$strBlank}{$tmp_k}: {$tmp}";
				}
				$i++;
			}
		}
		
		if (empty($arr)) {
			return json_encode($object, JSON_UNESCAPED_UNICODE);
		}
		if ($dumpMap) {
			array_unshift($arr, "");
		}
		return implode("\n", $arr);
	}

	private static function getStrBlank($currLevel) {
		return str_repeat('  ', $currLevel);
	}

	private static function getKey($value) {
		if (!is_string($value)) {
			return $value;
		}
		$len = strlen($value);
		for ($i = 0; $i < $len; $i++) {
			$tmp = $value[$i];
			if (!($tmp === '_' || $tmp === '.' || ($tmp >= '0' && $tmp <= '9') || ($tmp >= 'a' && $tmp <= 'z') || ($tmp >= 'A' && $tmp <= 'Z'))) {
				return json_encode($value, JSON_UNESCAPED_UNICODE);
			}
		}
		return $value;
	}

	private static function jsonEncode($object) {
		return json_encode($object, JSON_UNESCAPED_UNICODE);
	}

	private static function isArray($object) {
		if (is_array($object) || $object instanceof ArrayObject) {
			$i = 0;
			foreach ($object as $k => $v) {
				if ($k !== $i) {
					return false;
				}
				$i++;
			}
			return true;
		}
		return false;
	}
}
/*  */