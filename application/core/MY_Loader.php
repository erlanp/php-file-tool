<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Jenssegers\Blade\Blade;

require(FCPATH.'/vendor/autoload.php');

class Auto_Loader {
	public static function action($class) {
		$file = "{$class}.php";
		if (!strpos($class, '\\')) {
			require(APPPATH."third_party/{$file}");
		}
	}
}

spl_autoload_register(array('Auto_Loader', 'action'));

class MY_Loader extends CI_Loader {
	public function blade($view = NULL, $data = [], $mergeData = []) {
		$_ci_CI =& get_instance();
		isset($mergeData['load']) OR $mergeData['load'] = $this;
		isset($mergeData['input']) OR $mergeData['input'] = $_ci_CI->input;

		$cachePath = APPPATH.'cache_views';	 // 编译文件缓存目录

		$blade = new Blade(VIEWPATH, $cachePath);

		return $blade->make($view, $data, $mergeData)->render();
	}
}