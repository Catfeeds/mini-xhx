<?php if (!defined('THINK_PATH')) exit(); if(C('LAYOUT_ON')) { echo ''; } ?>
<!DOCTYPE>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>跳转提示</title>
	<style type="text/css">
	*{ padding: 0; margin: 0; }
	body{ background: #f3f3f4; font-family: '微软雅黑'; color: #333; font-size: 16px; }
	.system-message{     position: fixed;
	    left: 35%;
	    right: 35%;
	    top: 20%;
	    text-align: center; 
		padding-bottom: 30px;
		border-bottom-right-radius: 10px;
		border-bottom-left-radius: 10px;
		color: #676a6c;
	}
	.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; }
	.system-message .jump{ padding-top: 10px}
	.system-message .jump a{ color: #313131;}
	.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 30px }
	.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
	.system-icon{color: #fff;padding-bottom: 20px;
		border-top-right-radius: 10px;
		border-top-left-radius: 10px;}
	#wait{color: #1ab394;}
	</style>
</head>
<body>
	<div class="system-message">
		<h1 class="system-icon">
		
		<?php if(isset($message)) {?>
		<img src="./Tpl/images/success2.png" alt="" width="50%"/>
		</h1>
		<p class="success"><?php echo($message); ?></p>
		
		<?php }else{?>
		
		<img src="./Tpl/images/error2.png" alt="" width="50%"/>
		</h1>
		<p class="error"><?php echo($error); ?></p>
		<?php }?>
		
		<p class="detail"></p>
		<p class="jump">页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b></p>
	</div>
	<script type="text/javascript">
	(function(){
	var wait = document.getElementById('wait'),href = document.getElementById('href').href;
	var interval = setInterval(function(){
		var time = --wait.innerHTML;
		if(time <= 0) {
			location.href = href;
			clearInterval(interval);
		};
	}, 1000);
	})();
	</script>
</body>
</html>