<? 
echo $this->Html->script("http://code.jquery.com/jquery-1.9.1.min.js");
echo $this->Html->script('toastr.min');
echo $this->Html->script('jquery.dropdown');
echo $this->Html->css('jquery.dropdown');
?>
<link href="//cdn.rawgit.com/noelboss/featherlight/1.3.3/release/featherlight.min.css" type="text/css" rel="stylesheet" />
<script src="//cdn.rawgit.com/noelboss/featherlight/1.3.3/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>

<div class="section1">
	<div class="sectionContainer">
		<h1>Complete social media management</h1><span>Content creation, scheduling and proofing software</span>
		<div class="buttons">
			<button class="signUp" data-featherlight="/users/register" escape="false">SIGN UP FOR FREE</button>
			<button class="findOut">FIND OUT MORE</button>
		</div>
	</div>
</div>

<div class="section2">
	<div class="sectionContainer">
		<h2>Discover how TweetProof can help you</h2>
		<hr />
		<div class="feature one">
			<i class="fa fa-clock-o fa-fw"></i>
			<h1>SCHEDULE</h1>
			<p>Manage content creation and schedule messages.</p>
			<p>Create a strategy behind your content with an editorial calander</p>
			<p>Reach your audience at the right time</p>
		</div>
		<div class="feature two">
			<i class="fa fa-users fa-fw"></i>
			<h1>COLLABORATE</h1>
			<p>Work with teams across multiple accounts, everyone has an input </p>
			<p>Review, approve and comment on content before it's published</p>
			<p>A safety net to be sure of what is posted on your accounts</p>
		</div>
		<div class="feature three">
			<i class="fa fa-bank fa-fw"></i>
			<h1>TWEETBANK</h1>
			<p>Repost your best content again to make sure more people see it</p>
			<p>Re-use your sales and campaign messages in no time at all</p>
			<p>Manage your content inventory</p>
		</div>
	</div>
</div>

<div class="section5">
	<div class="sectionContainer">
		<h2>Features</h2>
		<hr />
		<div class="feature"><i class="fa fa-clock-o fa-fw"></i><p>Schedule Tweets</p></div>
		<div class="feature"><i class="fa fa-calendar fa-fw"></i><p>Create an Editorial Calander</p></div>
		<div class="feature"><i class="fa fa-users fa-fw"></i><p>Create Teams of writers, proofers and admin</p></div>
		<div class="feature"><i class="fa fa-twitter fa-fw"></i><p>Manage multiple Twitter Accounts</p></div>
		<div class="feature"><i class="fa fa-camera fa-fw"></i><p>Reuse your stock Images</p></div>
		<div class="feature"><i class="fa fa-bar-chart fa-fw"></i><p>View comrehensive real time statistics</p></div>
		<div class="feature"><i class="fa fa-bank fa-fw"></i><p>Tweet Bank of all your approved content</p></div>
		<div class="feature"><i class="fa fa-pie-chart fa-fw"></i><p>Solid reporting</p></div>
	</div>
</div>

<div class="section3">
	<div class="sectionContainer">
		<h2>Pricing</h2>
		<span class="small">Three simple plans to get you started</span>
		<hr />
		<div class="table one">
			<h1>FREE</h1>
			<div class="price">
				FREE
				<span></span>
			</div>
			<ul>
				<li><b>1</b> TWITTER ACCOUNT</li>
				<li><b>1</b> TEAM</li>
				<li><b>2</b> TEAM MEMBERS</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>EDITORIAL CALENDARS</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>SCHEDULING</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>UPLOAD IMAGES</li>
				<li><b><i class="fa fa-times fa-fw"></i></b>STATISTICS</li>
				<li><b><i class="fa fa-times fa-fw"></i></b>TWEETBANK</li>
				<li><b><i class="fa fa-times fa-fw"></i></b>REPORTING</li>
			</ul>
		</div>
		<div class="table two">
			<h1>PRO</h1>
			<div class="price">
				<small>$</small>30
				<span>PER MONTH</span>
			</div>
			<ul>
				<li><b>3</b> TWITTER ACCOUNTS</li>
				<li><b>3</b> TEAMS</li>
				<li><b>2</b> TEAM MEMBERS</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>EDITORIAL CALENDARS</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>SCHEDULING</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>UPLOAD IMAGES</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>STATISTICS</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>TWEETBANK</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>REPORTING</li>
			</ul>
		</div>
		<div class="table three">
			<h1>ENTERPRISE</h1>
			<div class="price">
				<small>$</small>100
				<span>PER MONTH</span>
			</div>
			<ul>
				<li><b>10</b> TWITTER ACCOUNTS</li>
				<li><b>10</b> TEAMS</li>
				<li><b>10</b> TEAM MEMBERS</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>EDITORIAL CALENDARS</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>SCHEDULING</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>UPLOAD IMAGES</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>STATISTICS</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>TWEETBANK</li>
				<li><b><i class="fa fa-check fa-fw"></i></b>REPORTING</li>
			</ul>
		</div>
		<div id="signUp">
			Create an account now for free and get started creating content with TweetProof.
			<div class="tableButton" data-featherlight="/users/register" escape="false">
			SIGN UP
			</div>
		</div>
	</div>
</div>

<div class="section4">
	<div class="sectionContainer">
		<span class="small">These organisations publish content using our software</span>
		<hr />
		<div class="clients">
			<?
			foreach ($topAccounts as $key => $value) {
				echo $this->Html->image($value['TwitterAccount']['profile_pic']);
			}
			?>
		</div>
	</div>
</div>

<div class="sectionContainer">
	<div id="login">
	<? echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));
	        echo $this->Form->input('email', array('placeholder' => 'Email Address', 'label' => false));
	        echo $this->Form->input('password', array('placeholder' => 'Password', 'label' => false));?>
			<h1><?echo $this->Html->link('Forgotten password?', array('action' => '#'), array('class' => 'forgot', 'data-featherlight' => '/users/forgotpw'));?></h1>
	        <?echo $this->Form->end('SIGN IN');?>
	</div>
</div>

<script>
$(document).ready(function() {
	$('.tableButton:not(.one)').featherlight('/users/register', {
	});

	$('.forgot').featherlight('/users/forgotpw', {
	});

	$('.tableButton.one').click(function () {
		$('#login').toggle();
	});

	$('.findOut').click(function () {
		$('html,body').animate({
   			scrollTop: $(".section5").offset().top
		});
	});

	$(document).mousedown(function (e) {
	    var container = $("#login");

	    if (!container.is(e.target) && container.has(e.target).length === 0) {
	        container.hide();
	    }
	});
});
</script>