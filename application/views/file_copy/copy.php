<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>

	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="body">

<?php $input = $this->input; ?>
<form id="mainform" name="mainform" target="iframe_file" method="post" enctype="multipart/form-data">

	<div>
	<?php echo form_label('导入文件夹:'); ?>
	<?php echo form_input(array(
              'name'        => 'dir_from',
              'id'          => 'dir_from',
              'value'       => $input->get_post('dir_from'),
              'size'        => '65',
            )); ?>
	</div>
	
	<div>
	<?php echo form_label('导出文件夹:'); ?>
	<?php echo form_input(array(
              'name'        => 'dir_to',
              'id'          => 'dir_to',
              'value'       => $input->get_post('dir_to'),
              'size'        => '65',
            )); ?>
	</div>

	<div>
	<?php echo form_label('要复制的文件:'); ?>
	</div>
	<div>
	<textarea rows="5" cols="120" name="allow_to_copy"><?php 
		echo $input->get_post('allow_to_copy') ?: ''; 
	?></textarea>
	</div>
	
	<div>&nbsp;</div>
	<div>
	
	<div>
		<?php echo form_label('储存名称:'); ?>
		<?php echo form_input(array(
			  'name'        => 'save_name',
			  'id'          => 'save_name',
			  'value'       => $input->get_post('save_name'),
			  'size'        => '25',
			)); ?>
		<input class="button" type="submit" name="save" value="SAVE"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<?php echo form_upload(array(
			'name' => 'load',
			'onChange' => 'submit_file(this);',
		)); ?>
		<script>
		function submit_file(obj) {
			obj.form.target = '';
			obj.form.submit();
		}
		</script>
	</div>
	<div>&nbsp;</div>
	<div>
		<input class="button" type="submit" name="ok" value=" OK "/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input class="button" type="reset" value="RESET"/>
	</div>
	
	</div>
	<div>&nbsp;</div>
</form>

<iframe name="iframe_file" width="800" height="600">
</iframe>
	
</div>
<script>

</script>
</body>
</html>