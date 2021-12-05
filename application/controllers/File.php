<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends MY_Controller {
	public function index()
	{
		// 入口菜单
		echo $this->load->blade('welcome.welcome');
	}
}