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

$cakeDescription = __d('cake_dev', 'social.guestlist.net');
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
</li>
</head>

<div id="loading" style="display: none;">
<? echo $this->Html->image('ajax-loader.GIF'); ?>
</div>

<body>
	<div id="container">
		<div id="header">
		<div id="headercontainer">
			<? echo $this->Html->image('GLogo60px.png', array('url' => '/')); ?>
			<h1><?php echo $this->Html->link($cakeDescription, 'http://social.guestlist.net'); ?></h1>
			<? echo $this->Html->image('beta.png', array('class' => 'beta'));?>
			<nav>
				<ul>
					<li class="<?php echo (!empty($this->params['action']) && ($this->params['action']=='admin') )?'active' :'inactive' ?>">
						<? echo $this->Html->link('write tweets', '/');?>
					</li>

					<? //if ($this->Session->read('Auth.User.Group.id') == 1 || $this->Session->read('Auth.User.Group.id') == 5) { ?>

						<li class="<?php echo (!empty($this->params['action']) && ($this->params['action']=='manageteam') )?'active' :'inactive' ?>">
							<? echo $this->Html->link('manage team', array('controller' => 'teams', 'action' => 'manageteam'));?>
						</li>
	
						<li class="<?php echo (!empty($this->params['action']) && ($this->params['action']=='index') )?'active' :'inactive' ?>">
							<? echo $this->Html->link('manage tweets', array('controller' => 'twitter', 'action' => 'index'));?>
						</li>

					<? //} ?>

					<li class="<?php echo (!empty($this->params['action']) && ($this->params['action']=='#') )?'active' :'inactive' ?>">
						<? echo $this->Html->link('you', '#');?>
					</li>
				</ul>
			</nav>
			<div class = 'fr'> <?php echo $this->Session->read('Auth.User.email') . ' | ' . $this->Html->link('log out', array('controller' => 'users', 'action' => 'logout')) ; ?> </div>
		</div>
		</div>
		<div id="content">

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
