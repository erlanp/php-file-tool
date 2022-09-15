<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use NilPortugues\Sql\QueryFormatter\Formatter;

class Code extends MY_Controller {
	public function __construct() {
		parent::__construct();
		set_time_limit(-1);
		$this->load->helper('directory');
		$this->load->helper('file');
		
		$this->load->helper('form');
	}

	public function sql() {
		$input = $this->input;
		if ($input->method() === 'post') {
			
			$data = array();
			$text = $input->post('text');
			$formatter = new Formatter();
			$this->load->view('code/sql_iframe', ['text' => $formatter->format($text)]);
			
		} else {
			$this->load->view('code/sql');
		}
	}

	public function index() {
		$input = $this->input;
		if ( ! empty($_FILES['load']['tmp_name'])) {
			$this->_file_to_post();
			$this->load->view('code/index');
		} elseif ($input->method() === 'post') {
			
			if ($input->post('save')) {
				$this->_output_post();
			}
			
			$data = array();
			$text = $input->post('text');
			$decode = $input->post('decode');
			$encode = $input->post('encode');
			$sort_fn = $input->post('sort_fn');

			$decode_fn_arr = $this->_decode_fn();
			
			$encode_fn_arr = $this->_encode_fn();
			
			if (isset($decode_fn_arr[$decode])) {
				$decode_fn = $decode_fn_arr[$decode];
				$text = $decode_fn($text);
				if (is_array($text) && in_array($sort_fn, [
					'asort', 
					'arsort', 
				], true)) {
					$sort_fn($text);
				}
			}
			
			$data = ['text'=>[],];
			if ($encode) {
				foreach ($encode as $v) {
					if (isset($encode_fn_arr[$v])) {
						$encode_fn = $encode_fn_arr[$v];
						$data['text'][$v] = $encode_fn($text);
					}
				}
			}
			$this->load->view('code/iframe', $data);
			
		} else {
			$this->load->view('code/index');
		}
	}
}