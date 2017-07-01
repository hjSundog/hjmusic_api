<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>register</title>
	<style>
		#main{
			position: absolute;
			top: 50%;
			left: 50%;
			width: 	300px;
			height: 300px;
			margin: -150px -150px;
			padding: 0 0;
			background-color: #eee;
		}
		#title {
			text-align:center;
			height:50px;
		}
		.emailname_password{
			line-height: 2;
		}
		#submit{
			text-align: center;
			margin: 20px 0;
		}
	</style>
</head>
<body>

<form action="<?php echo site_url('lwy/register'); ?>" method="post">
	<div id="main">
		<div id="title">
			我的音乐播放器注册中心
		</div>
		<div class="emailname_password">
		<span>邮　箱：</span>
		<span><input type="input" name= "email" value="<?php echo set_value('email'); ?>" ><?php echo form_error('email','<span>','</span>');?></span><br />
		<span>用户名：</span>
		<span><input type="input" name= "username" value="<?php echo set_value('username'); ?>" ><?php echo form_error('username','<span>','</span>');?></span><br />	
		<span>密　码：</span>
		<span><input type="password" name= "password" value="<?php echo set_value('password'); ?>" ><?php echo form_error('password','<span>','</span>');?></span><br />	
		<span>确认密码：</span>
		<span><input type="password" name= "passconf" value="<?php echo set_value('passconf'); ?>" ><?php echo form_error('passconf','<span>','</span>');?></span>	
		</div>
		<div id="submit">
			<input type ="submit" name= "submit" value="注册" />
		</div>
	</div>
	</div>
	</form>
</body>
</html>