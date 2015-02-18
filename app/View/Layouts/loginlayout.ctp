<html>
<?
$cakeDescription = __d('cake_dev', 'social.guestlist.net');
?>
<!DOCTYPE html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('loginlayout');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</li>
</head>

<div id="loading" style="display: none;">
<? echo $this->Html->image('ajax-loader.gif'); ?>
</div>

<body>
	<div id="container">
		<div id="content">
	
			<?php echo $this->Session->flash(); ?>
	
			<?php echo $this->fetch('content'); ?>
		</div>
</body>
</html>
