<html>
	<head>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<?
		echo $this->Html->css('newlayout');
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		?>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
	</head>

	<body>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<header>
				<div class='stage stage1'>
					<span>1.</span>Add Twitter Account
				</div>
				<div class='stage stage2'>
					<span>2.</span>Add Twitter Account
				</div>
				<div class='stage stage3'>
					<span>1.</span>Add Twitter Account
				</div>
			</header>

			<?php echo $this->fetch('content'); ?>
		</div>
	</body>
</html>