<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends MY_Controller {
	public function index()
	{
		// 入口菜单
		$this->load->view('welcome/welcome');
	}
}