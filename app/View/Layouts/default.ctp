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

$cakeDescription = __d('cake_dev', 'TweetProof');
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>
		<?php //echo $title_for_layout; ?>
	</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('newlayout');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>

	<script>
	  window.intercomSettings = {
	    app_id: "ryyuwn45",
	    name: "<?echo $this->Session->read('Auth.User.first_name') . ' ' . $this->Session->read('Auth.User.last_name');?>", // Full name
	    email: "<?echo $this->Session->read('Auth.User.email');?>", // Email address
	    //created_at: <?echo time();?>, 
	    created_at: <?echo strtotime($this->Session->read('Auth.User.created'))?>, // Signup date as a Unix timestamp
	    tutorial_progress: <?echo $this->Session->read('Auth.User.first_login_complete');?>
	  };
	</script>
	<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/ryyuwn45';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>
</head>

<div id="loading" style="display: none;">
	<div class="loader">
	    <svg class="circular">
	        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10"/>
	    </svg>
	</div>
</div>

<body>
	<div id="container">
		<header>
			<div id="headercontainer">
				<div id="logo">
					<a href='/'><? echo $this->Html->image('/img/logo_white.png', array('height' => '40px'));?><span>TWEET<em>PROOF</em></span></a>
					<? echo $this->Html->image('beta_white.png', array('class' => 'beta'));?>
				</div>
				<nav>
					<? echo $this->Html->image($this->Session->read('Auth.User.profile_pic'), array('width' => '30px', 'height' => '30px')); ?>
					<div class='arrowdown'></div>
					<i class="fa fa-bell-o navbell" id="notificationFrontImage"></i>
					<div class='arrowup notificationarrow' style='display: none'></div>
					<div id='notificationbox' style="display: none">
						<? echo $this->Html->image('/img/loader.gif', array('class' => 'notifloading', 'style' => 'position: absolute;top: 50%;right: 50%;display: none;')); ?>
					</div>
					<span class='help'><b style="font-weight: 600;">support</b></span>
				</nav>
				<div class='accountSettings'>
					<? echo $this->Html->image($this->Session->read('Auth.User.profile_pic'), array('width' => '40px', 'height' => '40px')); ?>
					<span><? echo $this->Session->read('Auth.User.first_name') . ' ' . $this->Session->read('Auth.User.last_name');?></span>
					<ul>
						<li><a href="/users/manage"><i class="fa fa-user fa-fw"></i>Account Management</a></li>
						<li><a href="#"><i class="fa fa-arrow-up fa-fw"></i>Upgrade</a></li>
						<li><a href="#"><i class="fa fa-cog fa-fw"></i>Settings</a></li>
						<li><a href="/users/logout"><i class="fa fa-sign-out fa-fw"></i>Sign Out</a></li>
					</ul>
				</div>
			</div>
		</header>
		<div id="contentheader">
			<div id="navcontainer">
			<nav>
				<ul>
					<li><a href='#' class="<?php echo (!empty($this->params['action']) && ($this->params['action']=='index') )?'active' :'inactive' ?>"><i class="fa fa-twitter fa-fw"></i>My Tweets</a></li>
					<li><a href='#'><i class="fa fa-users fa-fw"></i>Manage Teams</a></li>
					<li><a href='#'><i class="fa fa-cog fa-fw"></i>Settings</a></li>
				</ul>
			</nav>
			</div>
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
	</div>
	
</body>

<script>
$(document).ready(function() {

<? if (!empty($notificationCount)) {?>
	<?if ($notificationCount > 9) {
		$notificationCount = '9+';
	}?>
	$('.navbell').addClass('badge badge' + <?echo strval($notificationCount);?>);
<?}?>

$('.navbell').click(function () {
	$('#notificationbox').load('/notifications/notificationrefresh/' + <? echo $this->Session->read('Auth.User.id'); ?>);
    $('#notificationbox, .notificationarrow').toggle();
	$('.notifloading').show();
    if ($('#notificationbox').css('display') == 'none') {
        str = $(".navbell").attr('class');
        str1 = str.substr(32);
        if (str1 != "9+") {
            str2 =  Number(str1.split('.')[0]) - 5;
            if (str2 < 0) {
                $('.navbell').removeClass('badge badge' + str2);
            }
        }
        $(".navbell").removeClass('badge' + str1);
        $(".navbell").addClass('badge' + str2);
    }
	$('.notifloading').hide();
});

$(document).click(function(e) {  
    if(e.target.id != 'notificationbox' && e.target.id != 'notificationFrontImage') {
        $("#notificationbox, .notificationarrow").hide();
        str = $("#notificationFrontImage").attr('src');
        /*str1 = str.substr(17);
        if (str1 != "9plus.png") {
            str1 =  Number(str1.split('.')[0]) - 5;
            if (str1 < 0) {
                str1 = 0
            }
        }
        $("#notificationFrontImage").attr('src', '/img/notification' + str1 + '.png');*/
    }
    if(e.target.id != 'teamIcon') {
    }

});

$(document).mousedown(function (e) {
    var container = $("#twitterManage");

    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
    }
});

$(document).mousedown(function (e) {
    var container = $("#manageTeam");

    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
    }
});

$(document).mousedown(function (e) {
    var container = $("#createTeam");

    if (!container.is(e.target) && container.has(e.target).length === 0) {
        container.hide();
    }
});

$(document).mousedown(function (e) {
    var container = $(".filter.filterTeamScroll");

    if (!container.is(e.target) && container.has(e.target).length === 0) {
        if ($('.filter.filterTeamScroll').css('display') == 'none') {
        } else {
        	$('.filter.filterTeamScroll').hide();
            $('.selectedTeam').find('i').css('transform', 'rotate(0deg)');
            $('.selectedTeam').removeClass('clicked');
        }
    }
});

$(document).mousedown(function (e) {
    var container = $(".filter.filterAccountScroll");

    if (!container.is(e.target) && container.has(e.target).length === 0) {
        if ($('.filter.filterAccountScroll').css('display') == 'none') {
        } else {
        	$('.filter.filterAccountScroll').hide();
            $('.selectedAccount').find('i').css('transform', 'rotate(0deg)');
            $('.selectedAccount').removeClass('clicked');
        }
    }
});


$('#headercontainer nav img, #headercontainer nav .arrowdown').click(function () {
	$('.accountSettings').toggle();
});

});
</script>
</html>