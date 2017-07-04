<?php

//临时文件夹，用来存放暂未上传信息的音乐文件
$config['upload_path'] = dirname(dirname(dirname(__FILE__))).'\uploads\temp\\';

//正式文件夹
$config['music_path'] = dirname(dirname(dirname(__FILE__))).'\uploads\music\\';

//允许上传的文件后缀
$config['allowed_types'] = 'mp1|mp2|mp3|wma|wmv|rm|rmvb|aac|mid|wav';

//允许上传的文件大小（还需设置php.ini文件）
$config['max_size'] = 100;

