<?php 

class EasyCode {
	public static $conf_path = APPPATH.'config/';
	public static function load($path, $conf_path=NULL) {
		$conf_path === NULL && $conf_path = static::$conf_path;
		return static::to_array(file_get_contents("{$conf_path}{$path}"));
	}
	
	public static function typeLoad($path, $conf_path=NULL) {
		$conf_path === NULL && $conf_path = static::$conf_path;
		$pos = strrpos($path, '.');
		if ($pos === FALSE) {
			return FALSE;
		}
		$type =  strtolower(substr($path, $pos+1));
		$data = file_get_contents("{$conf_path}{$path}");
		switch ($type) {
			case 'ini':
				return EasyIni::to_array($data);
				break;
			default:
				return FALSE;
				break;
		}
		
	}
	
	public static function save($path, $data, $conf_path=NULL) {
		$conf_path === NULL && $conf_path = static::$conf_path;
		return file_put_contents("{$conf_path}{$path}", static::array_to($data));
	}
	
}
/*  */