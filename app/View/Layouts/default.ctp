<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'tweetPROOF');
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('layout');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
</li>
</head>

<div id="loading" style="display: none;">
<? echo $this->Html->image('ajax-loader.GIF'); ?>
</div>

<body>
	<div id="container">
		<div id="header">
		<div id="headercontainer">
			<div id="logo">
				<h1><a href='http://social.guestlist.net'>tweet<b>PROOF</b></a></h1>
				<? echo $this->Html->image('beta.png', array('class' => 'beta'));?>
			</div>
			<nav>
				<ul>
					<? //if ($this->Session->read('Auth.User.Group.id') == 1 || $this->Session->read('Auth.User.Group.id') == 5) { ?>

						<li class="<?php echo (!empty($this->params['action']) && ($this->params['action']=='index') )?'active' :'inactive' ?>">
							<? echo $this->Html->link('Manage Tweets', array('controller' => 'twitter', 'action' => 'index'), array('class' => 'managetweets'));?>
						</li>

						<li class="<?php echo (!empty($this->params['action']) && ($this->params['action']=='manageteam') )?'active' :'inactive' ?>">
							<? echo $this->Html->link('Manage Teams', array('controller' => 'teams', 'action' => 'manageteam'), array('class' => 'manageteams'));?>
						</li>
	
					<? //} ?>

					<li class="<?php echo (!empty($this->params['action']) && ($this->params['action']=='manage') )?'active' :'inactive' ?>">
						<? echo $this->Html->link('Settings', array('controller' => 'users', 'action' => 'manage'), array('class' => 'settings'));?>
					</li>
				</ul>
			</nav>
			
		</div>
		</div>
		<div id="content">
			<div id='searchbar'>
				<? echo $this->Form->create('search');
				echo $this->Form->input('search', array('type' => 'text', 'label' => false, 'placeholder' => 'Search...')); 
				echo $this->Form->end();?>

				<div class = 'fr'>
				<div style='margin:0; display: inline-block'><? echo $this->Html->image($this->Session->read('Auth.User.profile_pic'), array('style' => 'height: 25px; padding: 0 5px; margin-top: -3px')); ?>
				<?php echo $this->Session->read('Auth.User.first_name') . ' ' . $this->Session->read('Auth.User.last_name') .  $this->Html->image('chevron.png'); ?></div>
				<hr style='height: 20px; width: 1px; display: inline-block; background-color: #fff; margin: 0'>
				<? echo $this->Html->image('notification0.png', array('style' => 'padding: 0; margin: -5px 0 0 10px'));?>
				<div id='userlogout'>
					<ul>
						<a href='/users/manage'><li>Account Management</li></a>
						<a href='/users/logout'><li>Sign Out</li></a>
					</ul>
				</div>
				</div>
			</div>


			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false)
				);
			?>
		</div>
	</div>
</body>
</html>
