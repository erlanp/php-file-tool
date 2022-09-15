<?php
use HJSON\HJSONParser;
use HJSON\HJSONStringifier;
use Symfony\Component\Yaml\Yaml;
use Yosymfony\Toml\Toml;

class MY_Controller extends CI_Controller {

	public function __construct() {
		parent::__construct();
		
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->helper('html');
	}
	
	protected function _file_to_post($load='load') {
		$tmp_name = ! empty($_FILES[$load]['tmp_name']) ? $_FILES[$load]['tmp_name'] : '';
		$name = ! empty($_FILES[$load]['name']) ? $_FILES[$load]['name'] : '';
		$pos = strrpos($name, '.');
		$file_type = substr($name, $pos+1);
		$tmp = file_get_contents($tmp_name);
		$_POST = $this->_to_array($tmp, $file_type);
	}
	
	protected function _output_post() {
		$save_name = $this->input->post('save_name');
		$save_type = $this->input->get_post('save_type') ?: 'xml';
		$save_name = $save_name ?: 'test';
		$file_name = "{$save_name}.{$save_type}";
		header("Content-Type: application/{$save_type}");
		// header("Content-type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Content-Disposition: attachment; filename=" . $file_name);
		echo $this->_array_to($_POST, $save_type);
		exit;
	}
	
	
	protected function _to_array($data, $type) {
		switch ($type) {
			case 'json':
				return json_decode($data, TRUE);
			break;
			case 'xml':
				return EasyXml::to_array($data);
			break;
			case 'ini':
				return EasyIni::to_array($data);
			break;
			
			default:
		}
	}
	
	protected function _array_to($data, $type) {
		switch ($type) {
			case 'json':
				return json_decode($data, TRUE);
			break;
			case 'ini':
				return EasyIni::array_to($data);
			break;
			case 'xml':
			default:
				return EasyXml::array_to($data);
		}
		
	}
	
	protected function _decode_fn($keys=NULL) {
		$nothing = 'strval';
		$base64 = 'base64_decode';
		$toml = static function($str) {
			return Toml::Parse($str);
		};
		$hey = static function($str) {
			$str = str_replace("\r\n", "\n", $str);
			$arr = explode("\n", $str);
			$return = array_unique($arr);
			sort($return);
			return $return;
		};
		$csv = $this->csv_fn("\t");
		$csv_kv = $this->csv_kv_fn("\t");
		$tsv = $this->csv_fn();
		$tsv_kv = $this->csv_kv_fn();
		$json = static function($str) {
			return json_decode($str, TRUE);
		};
		$yaml = static function($str) {
			return Yaml::parse($str);
		};
		$json5 = static function($str) {
			return json5_decode($str, TRUE, 4095);
		};
		$hjson = static function($str) {
			$parser = new HJSONParser();
			return json_decode(str_replace('\\\\u', '\\u', json_encode($parser->parse($str))), TRUE);
		};
		$json_un = static function($str) {
			return json_decode($str, TRUE, JSON_UNESCAPED_UNICODE);
		};
		$parse = static function($str) {
			parse_str($str, $return);
			return $return;
		};
		$var_export = static function($str) {
			return eval("return {$str};");
		};
		$serialize = 'unserialize';
		$xml = static function($str) {
			return EasyXml::to_array($str);
		};
		
		$ini = static function($str) {
			return EasyIni::to_array($str);
		};
		
		$keys = $keys ?: [
			'nothing', 'base64', 'toml', 'hey', 'csv_kv', 'csv', 'tsv_kv', 'tsv', 'json', 
			'json_un', 'json5', 'hjson', 'yaml', 'parse', 'var_export', 
			'serialize', 'ini', 'xml',
		];
		return compact($keys);
	}

	private function csv_kv_fn($delimiter = ',', $enclosure = '"', $escape = '\\') {
		return static function($str) use($delimiter, $enclosure, $escape) {
			$str = str_replace("\r\n", "\n", $str);
			$arr = explode("\n", $str);
			
			$return = [];
			if (isset($arr[0])) {
				for ($i=0; isset($arr[$i]); $i++) {
					$tmp = [];
					$v = str_getcsv($arr[$i], $delimiter, $enclosure, $escape);
					$tmp_k = array_shift($v);
					if (isset($v[1])) {
						$tmp[$tmp_k] = $v;
					} else {
						$tmp[$tmp_k] = pos($v);
					}
					
					$return[] = $tmp;
				}
			}
			return $return;
		};
	}

	private function csv_fn($delimiter = ',', $enclosure = '"', $escape = '\\') {
		return static function($str) use($delimiter, $enclosure, $escape) {
			$str = str_replace("\r\n", "\n", $str);
			$arr = explode("\n", $str);
			
			$return = [];
			if (isset($arr[0])) {
				$title = str_getcsv($arr[0], $delimiter, $enclosure, $escape);
				for ($i=1; isset($arr[$i]); $i++) {
					$tmp = [];
					foreach (str_getcsv($arr[$i], $delimiter, $enclosure, $escape) as $k=>$v) {
						if (isset($title[$k])) {
							$tmp[$title[$k]] = $v;
						} else {
							$tmp[] = $v;
						}
					}
					$return[] = $tmp;
				}
			}
			return $return;
		};
	}
	
	protected function _encode_fn($keys = NULL) {
		$nothing = static function($obj) {
			if (is_string($obj)) {
				return $obj;
			} else {
				return var_export($obj, TRUE);
			}
		};
		$base64 = 'base64_encode';
		$json = 'json_encode';
		$yaml = static function($obj, $maxLevel = 2) {
			return Yaml::dump($obj, $maxLevel);
		};
		$yaml_level = static function($obj) {
			return EasyYaml::array_to($obj);
		};
		$json_un = static function($obj) {
			return json_encode($obj, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
		};
		$hjson = static function($obj) {
			$stringifier = new HJSONStringifier;
			return($stringifier->stringifyWsc((object)$obj));
		};
		$parse =  'http_build_query';
		$var_export = static function($obj) {
			return var_export($obj, TRUE);
		};
		$serialize = 'serialize';
		$xml = static function($obj) {
			return EasyXml::array_to($obj);
		};
		$ini = static function($obj) {
			return EasyIni::array_to($obj);
		};
		$echo = static function($obj) {
			return ($obj);
		};
		
		$keys = $keys ?: [
			'nothing', 'base64', 'json', 
			'json_un', 'hjson', 'yaml', 'yaml_level',
			'parse', 'var_export', 
			'serialize', 'ini', 'xml', 'echo',
		];
		return compact($keys);
	}
}