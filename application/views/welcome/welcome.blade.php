@extends('layouts.app')

@section('content')
<div id="content">
	<ul>
	</ul>
</div>
<div id="container">
	<h1>工具目录 </h1>

	<div id="body">
		<div>
			<a href="/file_copy/search">多匹配文件查找</a>
		</div>
		
		<div>
			<a href="'/file_copy/copy'">指定文件拷贝</a>
		</div>
		
		<div>
			<a href="/file_copy">文件夹文件拷贝</a>
		</div>
		
		<div>
			<a href="/file_copy/if_eq">查看等号</a>
		</div>
		
		<div>
			<a href="/file_copy/if_blank">查看多余空格回车</a>
		</div>
		
	</div>
</div>
@endsection