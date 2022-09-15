<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_copy extends MY_Controller {
	public function __construct() {
		parent::__construct();
		set_time_limit(-1);
		date_default_timezone_set('Asia/Shanghai'); // 要和自己时间同步。
		$this->load->helper('directory');
		$this->load->helper('file');
		
		$this->load->helper('form');
	}
	
	public function index() {
		$input = $this->input;
		if ( ! empty($_FILES['load']['tmp_name'])) {
			$_POST = json_decode(file_get_contents($_FILES['load']['tmp_name']), TRUE);
			$this->load->view('file_copy/index');
		} elseif ($input->method() === 'post') {
			
			if ($input->post('save')) {
				$this->_output_post();
			}

			$config = array(
				'suffix' => json_decode($input->post('suffix')) ?: [],
				'black_suffix' => json_decode($input->post('black_suffix')) ?: [],
				'prohibited_to_copy' => json_decode($input->post('prohibited_to_copy')) ?: [],
				'dir_from' => ($input->post('dir_from')),
				'dir_to' => ($input->post('dir_to')),
				'date' => ($input->post('date')),
				'fn_copy' => ($input->post('fn_copy')),
			);
			
			$this->_copy($config);
		} else {
			$this->load->view('file_copy/index');
		}
	}
	
	public function copy() {
		$input = $this->input;
		if ( ! empty($_FILES['load']['tmp_name'])) {
			$_POST = json_decode(file_get_contents($_FILES['load']['tmp_name']), TRUE);
			$this->load->view('file_copy/copy');
		} elseif ($input->method() === 'post') {
			
			if ($input->post('save')) {
				$this->_output_post();
			}

			$data = array(
				'allow_to_copy' => explode("\n", trim($input->post('allow_to_copy'))) ?: [],
				'dir_from' => ($input->post('dir_from')),
				'dir_to' => ($input->post('dir_to')),
			);
			$this->selectCopy($data);
			
		} else {
			$this->load->view('file_copy/copy');
		}
	}

	private function selectCopy($data) {
		$dir_from = rtrim(rtrim(trim($data['dir_from']), '/'), '\\');
		$dir_to = rtrim(rtrim(trim($data['dir_to']), '/'), '\\');
		$err = [];
		$ok = [];
		$copy = $this->getCopyFn();
		foreach ($data['allow_to_copy'] as $file) {
			$file = ltrim(ltrim(trim($file), '/'), '\\');
			$from_file = $dir_from . '/' . $file;
			$to_file = $dir_to . '/' . $file;
			if (!file_exists($from_file)) {
				$err[] = $from_file."不存在";
				continue;
			}
			
			if ($copy($from_file, $to_file)) {
				$ok[] = $to_file;
			} else {
				$err[] = $to_file;
			}
		}
		$out = [];
		if (!empty($err)) {
			$out["失败"] = $err;
		}
		if (!empty($ok)) {
			$out["完成"] = $ok;
		}
		var_export($out);
	}

	private function getCopyFn() {
		$mkdir = function($dir) use(&$mkdir) {
			if ($dir === dirname($dir)) {
				return FALSE;
			}
			if (!is_dir($dir)) {
				if (is_dir(dirname($dir))) {
					return @mkdir($dir, 0755);
				} else {
					$result = $mkdir(dirname($dir));
					if ($result) {
						return @mkdir($dir, 0755);
					} else {
						return FALSE;
					}
				}
			}
			return TRUE;
		};
		$copy = function($from, $to) use($mkdir) {
			$dir = dirname($to);
			if ($dir === $to) {
				return FALSE;
			}
			$isDir = TRUE;
			if (!file_exists(dirname($to))) {
				$isDir = $mkdir(dirname($to));
			}
			if ($isDir) {
				return copy($from, $to);
			}
			return FALSE;
		};
		return $copy;
	}
	
	public function _copy($config) {
		extract($config);
		
		$Ob = new ObservedFile;
		// directory_map
		$Ob->fn_name_filter = function($file) use($config) {
			if (in_array($file, $config['prohibited_to_copy'])) {
				return FALSE;
			}
			$suffix = FALSE;
			$black_suffix = TRUE;
			foreach ($config['suffix'] as $type) {
				if (strcasecmp(substr($file, -strlen($type)), $type) === 0) {
					$suffix = TRUE;
				}
			}
			foreach ($config['black_suffix'] as $type) {
				if (strcasecmp(substr($file, -strlen($type)), $type) === 0) {
					$black_suffix = FALSE;
				}
			}
			return $suffix && $black_suffix;
		};
		$map = $Ob->directory_map($dir_from);
		
		$time = intval(strtotime($date));
		
		$Ob->fn_filter = function($str) use($time) {
			return filemtime($str) > $time;
		};

			// var_export([$fn_copy, $suffix]);
		if ($fn_copy) {
			$Ob->fn_copy = $fn_copy;
		} else {
			$Ob->fn_copy = $this->getCopyFn();
		}
		delete_files($dir_to, TRUE);
		$map2 = $Ob->clone_folder($map, $dir_from, $dir_to);
		
		var_export([$map]);
	}
	
	public function if_eq() {
		$input = $this->input;
		if ( ! empty($_FILES['load']['tmp_name'])) {
			$_POST = json_decode(file_get_contents($_FILES['load']['tmp_name']), TRUE);
			$this->load->view('file_copy/if_eq');
		} elseif ($input->method() === 'post') {
			
			if ($input->post('save')) {
				$this->_output_post();
			}

			$config = array(
				'suffix' => json_decode($input->post('suffix')),
				'black_suffix' => json_decode($input->post('black_suffix')),
				'prohibited_to_copy' => json_decode($input->post('prohibited_to_copy')),
				'dir_from' => ($input->post('dir_from')),
				'date' => ($input->post('date')),
			);
			
			$this->_if_eq($config);
		} else {
			$this->load->view('file_copy/if_eq');
		}
	}
	
	public function search() {
		$input = $this->input;
		if ( ! empty($_FILES['load']['tmp_name'])) {
			$_POST = json_decode(file_get_contents($_FILES['load']['tmp_name']), TRUE);
			$this->load->view('file_copy/search');
		} elseif ($input->method() === 'post') {
			
			if ($input->post('save')) {
				$this->_output_post();
			}

			$config = array(
				'suffix' => json_decode($input->post('suffix')),
				'search' => json_decode($input->post('search')),
				'black_suffix' => json_decode($input->post('black_suffix')),
				'prohibited_to_copy' => json_decode($input->post('prohibited_to_copy')),
				'dir_from' => ($input->post('dir_from')),
				'date' => ($input->post('date')),
			);
			
			$this->_search($config);
		} else {
			$this->load->view('file_copy/search');
		}
	}
	
	public function if_blank() {
		$input = $this->input;
		if ( ! empty($_FILES['load']['tmp_name'])) {
			$_POST = json_decode(file_get_contents($_FILES['load']['tmp_name']), TRUE);
			$this->load->view('file_copy/if_blank');
		} elseif ($input->method() === 'post') {
			
			if ($input->post('save')) {
				$this->_output_post();
			}

			$config = array(
				'suffix' => json_decode($input->post('suffix')),
				'prohibited_to_copy' => json_decode($input->post('prohibited_to_copy')),
				'dir_from' => ($input->post('dir_from')),
				'date' => ($input->post('date')),
				'check_blank' => ($input->post('check_blank')),
				'check_code' => ($input->post('check_code')),
			);
			
			$this->_if_blank($config);
		} else {
			$this->load->view('file_copy/if_blank');
		}
	}
	
	protected function _if_blank($config) {
		$dir_from = $config['dir_from'];
		$date = $config['date'];
		
		$Ob = new ObservedFile;
		$Ob->fn_name_filter = function($file) use($config) {return TRUE;
			foreach ($config['suffix'] as $type) {
				if (strcasecmp(substr($file, strrpos($file, '.')+1), $type) === 0)
				{
					return TRUE;
				};
			}
			return FALSE;
		};
		$map = $Ob->directory_map($dir_from);

		$Ob->fn_filter = function($str) use($config) {
			$file_data = file_get_contents($str);
			
			$pos = strrpos($file_data, '?>');
			$r_file_data = rtrim($file_data);
			if ($config['check_blank'] 
					&& $pos !== FAlSE 
					&& $file_data !== $r_file_data 
					&& $pos > strlen($r_file_data)-3) {
				return 'check_blank';
			}
			if ($config['check_code']) {
				$encode = mb_detect_encoding($file_data, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5', ));
				if (!in_array($encode, ['ASCII', 'UTF-8',])) {
					return 'check_code';
				}
			}
			return FALSE;
		};
		
		$map2 = $Ob->get_folder($map, $dir_from);
		if (empty($map2)) {
			echo ("未检测出错误格式文件");
		}
		if (!empty($map2['check_code'])) {
			echo ("检测出错误编码格式文件<br/>");
			echo str_replace(["\r\n"], '<br/>', EasyIni::array_to($map2['check_code']));
		}
		if (!empty($map2['check_blank'])) {
			echo ("检测出有多余空格的php文件<br/>");
			echo str_replace(["\r\n"], '<br/>', EasyIni::array_to($map2['check_blank']));
		}
	}
	
	protected function _search($config) {
		$dir_from = $config['dir_from'];
		$date = $config['date'];
		$arr_search = $config['search'] ?: [];
		
		$Ob = new ObservedFile;
		// directory_map
		$Ob->fn_name_filter = function($file) use($config) {
			foreach ($config['suffix'] as $type) {
				if (strcasecmp(substr($file, strrpos($file, '.')+1), $type) === 0)
				{
					return TRUE;
				};
			}
			return FALSE;
		};
		$map = $Ob->directory_map($dir_from);
		
		$retMktimest = function($dbdate) {
			return mktime(substr($dbdate, 11, 2), substr($dbdate, 14, 2), substr($dbdate, 17, 2), substr($dbdate, 5, 2), substr($dbdate, 8, 2), substr($dbdate, 0, 4));
		};
		
		$time = $retMktimest($date);
		
		$Ob->fn_filter = function($str) use($time, $arr_search) {
			$data = file_get_contents($str);

			foreach ($arr_search as $search) {
				if (strpos($data, $search) === false) {
					return false;
				}
			}
			
			return true;
		};
		
		$map2 = $Ob->get_folder($map, $dir_from);
		// var_export($map2);
		echo str_replace("\n", '<br/>', EasyIni::array_to($map2));
	}
	
	protected function _if_eq($config) {
		// 取得 if 里写了 = 号的程序
		$dir_from = $config['dir_from'];
		$date = $config['date'];
		
		$Ob = new ObservedFile;
		// directory_map
		$Ob->fn_name_filter = function($file) use($config) {
			foreach ($config['suffix'] as $type) {
				if (strcasecmp(substr($file, strrpos($file, '.')+1), $type) === 0)
				{
					return TRUE;
				};
			}
			return FALSE;
		};
		$map = $Ob->directory_map($dir_from);
		
		$retMktimest = function($dbdate) {
			return mktime(substr($dbdate, 11, 2), substr($dbdate, 14, 2), substr($dbdate, 17, 2), substr($dbdate, 5, 2), substr($dbdate, 8, 2), substr($dbdate, 0, 4));
		};
		
		$time = $retMktimest($date);
		
		$if_fn = $this->get_fn();
		$Ob->fn_filter = function($str) use($time, $if_fn) {
			if (
			filemtime($str) > $time
			) {
				$arr = $if_fn(file_get_contents($str));
				if ( ! empty($arr)) {
					var_export([$str, $arr]);
				};
				return FALSE;
				
			} else {
				return FALSE;
			}
		};
		
		$map2 = $Ob->get_folder($map, $dir_from);
		
		var_export($map2);
	}
	
	protected function _output_post() {
		$save_name = $this->input->post('save_name');
		$save_name = $save_name ?: 'test';
		$file_name = "{$save_name}.json";
		header('Content-Type: application/json');
		// header("Content-type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Content-Disposition: attachment; filename=" . $file_name);
		echo json_encode($_POST);
		exit;
	}
	
	protected function get_fn() {
		return function($str) {
			$len = strlen($str);

			$line_tmp = '';
			$line = 1;
			$arr_line = array();
			$in_php = FALSE;

			$block_comments = FALSE;
			$line_comment = FALSE;

			$single_quotes = FALSE;
			$double_quotes = FALSE;

			for ($i=0; $i<$len; $i++) {
				$s = $str[$i];
				if ($in_php === FALSE) {
					if ($s === '<' && strtolower(substr($str, $i, 5))=== '<?php') {
						$in_php = TRUE;
						$i += 4;
					}
				} else {
					if ($s === '?' && (substr($str, $i, 2)) === '?>') {
						$in_php = FALSE;
						$i += 1;
					}
					
					if ($block_comments === FALSE && $line_comment === FALSE) {
						if ($single_quotes === FALSE && $double_quotes === FALSE) {
							if ($s === '"') {
								$double_quotes = TRUE;
							} elseif ($s === "'") {
								$single_quotes = TRUE;
							} elseif ($s === '/') {
								$two = (substr($str, $i, 2));
								if ($two === '/*') {
									$i += 1;
									$block_comments = TRUE;
								} elseif ($two === '//') {
									$i += 1;
									$line_comment = TRUE;
								}
							}
						} elseif ($double_quotes === TRUE) {
							if ($s === '\\') {
								$i += 1;
								$s .= $str[$i];
							} elseif ($s === '"') {
								$double_quotes = FALSE;
							}
						} elseif ($single_quotes === TRUE) {
							if ($s === '\\') {
								$i += 1;
								$s .= $str[$i];
							} elseif ($s === "'") {
								$single_quotes = FALSE;
							}
						}
						
						if ($in_php 
								&& $block_comments === FALSE
								&& $line_comment === FALSE) {
							$line_tmp .= $s;
						}
						
					} elseif ($block_comments === TRUE) {
						if ($s === '*' && (substr($str, $i, 2)) === '*/') {
							$i += 1;
							$block_comments = FALSE;
						}
					}
					
				}
				
				if ($s === "\n") {
					$line++;
					$arr_line[$line] = $line_tmp;
					$line_tmp = '';
					$line_comment = FALSE;
				}
			}

			$single_quotes = FALSE;
			$double_quotes = FALSE;
			$brackets = 0;
			$curr_line = 1;
			$arr_brackets = array();

			$tmp = '';
			$tmp_str = '';
			foreach ($arr_line as $line => $str) {
				$len = strlen($str);
				for ($i=0; $i<$len; $i++) {
					$s = $str[$i];
					
					if ($single_quotes === FALSE && $double_quotes === FALSE) {
						if ($s === '"') {
							$double_quotes = TRUE;
						} elseif ($s === "'") {
							$single_quotes = TRUE;
						} elseif ($s === '(') {
							$brackets += 1;
							if ($brackets === 1) {
								$curr_line = $line;
							}
						} elseif ($s === ')') {
							$brackets -= 1;
							if ($brackets === 0) {
								$tmp_str .= $s;
								$tmp = str_replace('!==', '', $tmp);
								$tmp = str_replace('===', '', $tmp);
								$tmp = str_replace('!=', '', $tmp);
								$tmp = str_replace('==', '', $tmp);
								$tmp = str_replace('<=', '', $tmp);
								$tmp = str_replace('>=', '', $tmp);
								$tmp = str_replace('=>', '', $tmp);
								$tmp = str_replace('+=', '', $tmp);
								$tmp = str_replace('-=', '', $tmp);

								if (strpos($tmp, '=') !== FALSE) {
									$line_str = $arr_line[$curr_line];
									$pos_str = substr($line_str, 0, strpos($line_str, '('));
									
									if (stripos($pos_str, 'if') !== FALSE && stripos($pos_str, 'function') === FALSE) {
										$tmp = str_replace(array(' ', '	'), '', $tmp);
										$pos = strpos($tmp, '=');
										$tmp_k = $tmp[$pos + 1];
										
										if (strpbrk($tmp_k, '0123456789"\'') !== FALSE) {
											// 如果单个等于号后 is_scalar
											$caveat = '!!!';
										} elseif (strpbrk($tmp_k, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') !== FALSE) {
											// 如果单个等于号后 是常量
											$caveat = '?!!';
										} else {
											$pos2 = strpos($tmp, '=', $pos);
											if ($pos2 !== FALSE) {
												$tmp_k = $tmp[$pos2 + 1];
												if (strpbrk($tmp_k, '0123456789"\'') !== FALSE) {
													// 如果单个等于号后 is_scalar
													$caveat = '!!!!';
												} elseif (strpbrk($tmp_k, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') !== FALSE) {
													// 如果单个等于号后 是常量
													$caveat = '!?!!';
												} else {
													$caveat = '';
												}
											} else {
												$caveat = '';
											}
										}
										$arr_brackets[$curr_line] = $tmp_str.$caveat;
									}
								}
								$tmp_str = '';
								$tmp = '';
							}
						}
						
						
					} elseif ($double_quotes === TRUE) {
						if ($s === '\\') {
							$i += 1;
							$s .= $str[$i];
						} elseif ($s === '"') {
							$double_quotes = FALSE;
						}
					} elseif ($single_quotes === TRUE) {
						if ($s === '\\') {
							$i += 1;
							$s .= $str[$i];
						} elseif ($s === "'") {
							$single_quotes = FALSE;
						}
					}
					
					if ($brackets > 0) {
						$tmp_str .= $s;
						if ($single_quotes === FALSE && $double_quotes === FALSE) {
							$tmp .= $s;
						}
					}
				}
			}
		
			return $arr_brackets;
		};
	}
}

Class ObservedFile
{
	public $fn_name_filter = NULL;
	public $fn_filter = NULL;
	public $fn_copy = NULL;

	public function clone_folder($file_arr, $path_from, $path_to, $recursing = FALSE)
	{ // 根据备份文件夹的路径新建$path_to文件夹, 并返回绝对路径数组。
		static $path_from_to;
		($recursing) OR $path_from_to = array();

		$path_from = rtrim($path_from, DIRECTORY_SEPARATOR);
		$path_to = rtrim($path_to, DIRECTORY_SEPARATOR);

		if ((is_dir($path_to) === FALSE) && (@mkdir($path_to, 0755) === FALSE))
		{
			exit(json_encode($path_to) . '建目录失败');
		};

		foreach ($file_arr as $k => $val)
		{
			if (is_array($val))
			{
				if ($k[0] !== '.')
				{// 不拷贝 .svn .git 之类的文件夹
					$this->clone_folder($val, $path_from . '/' . $k, $path_to . '/' . $k, TRUE);
				}
			}
			else
			{
				if ($val[0] !== '.' OR $val === '.gitignore')
				{
					$file = $path_from . '/' . $val;
					$file2 = $path_to . '/' . $val;
					if ($this->fn_filter === NULL OR
							call_user_func($this->fn_filter, $file))
					{
						$path_from_to[] = array(date('Ymd-His', filemtime($file)), $file, $file2,);
						if ($this->fn_copy !== NULL)
						{
							call_user_func($this->fn_copy, $file, $file2);
						}
					}
				}
			};
		}

		return $path_from_to;
	}
	
	public function get_folder($file_arr, $path_from, $recursing = FALSE)
	{ // 取得符合条件文件的绝对路径。
		static $path_from_to;
		($recursing) OR $path_from_to = array();

		$path_from = rtrim($path_from, DIRECTORY_SEPARATOR);

		foreach ($file_arr as $k => $val)
		{
			if (is_array($val))
			{
				if ($k[0] !== '.')
				{// 不拷贝 .svn .git 之类的文件夹
					$this->get_folder($val, $path_from . '/' . $k, TRUE);
				}
			}
			else
			{
				if ($val[0] !== '.' OR $val === '.gitignore')
				{
					$file = $path_from . '/' . $val;
					if ($this->fn_filter === NULL) {
						$path_from_to[] = $file;
					} else {
						$reason = call_user_func($this->fn_filter, $file);
						if ($reason === TRUE) {
							$path_from_to[] = $file;
						} elseif ($reason !== FALSE) {
							$path_from_to[$reason][] = $file;
						}
					}
				}
			};
		}

		return $path_from_to;
	}
	
	public function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE)
	{
		if ($fp = @opendir($source_dir))
		{
			$filedata	= array();
			$new_depth	= $directory_depth - 1;
			$source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

			while (FALSE !== ($file = readdir($fp)))
			{
				// Remove '.', '..', and hidden files [optional]
				if ($file === '.' OR $file === '..' OR ($hidden === FALSE && $file[0] === '.'))
				{
					continue;
				}

				is_dir($source_dir.$file) && $file .= DIRECTORY_SEPARATOR;

				if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir.$file))
				{
					$filedata[$file] = $this->directory_map($source_dir.$file, $new_depth, $hidden);
				}
				elseif ($this->fn_name_filter === NULL OR
						call_user_func($this->fn_name_filter, $file))
				{
					$filedata[] = $file;
				}
			}

			closedir($fp);
			return $filedata;
		}
		return FALSE;
	}
};

