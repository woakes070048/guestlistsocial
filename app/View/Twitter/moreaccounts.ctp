<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
<div>
<? if (!empty($owner)) {
	if ($type == "accounts") {?>
		You have reached the limit for the number of twitter accounts on your plan. Upgrade now to add more accounts!
	<?} elseif ($type == "teams") {?>
		You have reached the limit for the number of teams on your plan. Upgrade now to add to more teams!
	<?} elseif ($type == "users") {?>
		You have reached the limit for the number of users on your team. Upgrade now to add more users!
	<?}?>
	<div id="table">
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
				<li>
					<form action="" method="POST">
						<?
						if ($this->Session->read('Auth.User.group_id') == 2) {
							$checked = 'disabled';
						} else {
							$checked = '';
						}
						?>
						<button id="customButtonPro" class="stripe-button" <?echo $checked;?>>Upgrade to Pro</button>
					</form>
				</li>
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
				<li>
					<form action="" method="POST">
						<?
						if ($this->Session->read('Auth.User.group_id') == 3) {
							$checked = 'disabled';
						} else {
							$checked = '';
						}
						?>
						<button id="customButtonEnterprise" class="stripe-button" <?echo $checked;?>>Upgrade to Enterprise</button>
					</form>
				</li>
			</ul>
		</div>
	</div>
<?} else {?>
	<?if ($type == "accounts") {?>
		The maximum number of allowed twitter accounts has been reached for this team. The owner will need to upgrade their account to add more twitter accounts.
	<?} elseif ($type == "teams") {?>
		You have reached the limit for the number of teams on your plan. Upgrade now to add to more teams!
	<?} elseif ($type == "users") {?>
		The maximum number of allowed users has been reached for this team. The owner will need to upgrade their account to add more users.
	<?}?>

<?}?>






<script type="text/javascript">
$(document).ready(function () {
      var handler = StripeCheckout.configure({
        key: 'pk_test_jhuRvD7XWwgcdDLE1Ymgvq8e',
        image: '/img/logo.png',
        locale: 'auto',
        email: "<?echo $this->Session->read('Auth.User.email');?>",
        token: function(token) {
          // Use the token to create the charge with a server-side script.
          // You can access the token ID with `token.id`
          formData = new FormData();
          formData.append('stripeToken', token.id);
          formData.append('stripeEmail', token.email);
          formData.append('stripePlan', 30);

          $('#loading').show();

          $.ajax({
			  url: '/twitter/moreaccounts',
			  data: formData,
			  processData: false,
			  contentType: false,
			  type: 'POST',
			  success: function(data){
			    toastr.success('You have successfully been upgraded!');
			    $('#loading').hide();
			  }
			});
        }
      });

      $('#customButtonBasic').on('click', function(e) {
        // Open Checkout with further options
        handler.open({
          name: 'TweetProof',
          description: 'Basic Subscription',
          currency: "usd",
          amount: 3000
        });
        e.preventDefault();
      });


      var handler1 = StripeCheckout.configure({
        key: 'pk_test_jhuRvD7XWwgcdDLE1Ymgvq8e',
        image: '/img/logo.png',
        locale: 'auto',
        email: "<?echo $this->Session->read('Auth.User.email');?>",
        token: function(token) {
          // Use the token to create the charge with a server-side script.
          // You can access the token ID with `token.id`
          formData = new FormData();
          formData.append('stripeToken', token.id);
          formData.append('stripeEmail', token.email);
          formData.append('stripePlan', 30);

          $('#loading').show();

          $.ajax({
			  url: '/twitter/moreaccounts',
			  data: formData,
			  processData: false,
			  contentType: false,
			  type: 'POST',
			  success: function(data){
			    toastr.success('You have successfully been upgraded!');
			    $('#loading').hide();
			  }
			});
        }
      });

      $('#customButtonPro').on('click', function(e) {
        // Open Checkout with further options
        handler1.open({
          name: 'TweetProof',
          description: 'Pro Subscription',
          currency: "usd",
          amount: 3000
        });
        e.preventDefault();
      });

      var handler2 = StripeCheckout.configure({
        key: 'pk_test_jhuRvD7XWwgcdDLE1Ymgvq8e',
        image: '/img/logo.png',
        locale: 'auto',
        email: "<?echo $this->Session->read('Auth.User.email');?>",
        token: function(token) {
          // Use the token to create the charge with a server-side script.
          // You can access the token ID with `token.id`
          formData = new FormData();
          formData.append('stripeToken', token.id);
          formData.append('stripeEmail', token.email);
          formData.append('stripePlan', 100);
		  
		  $('#loading').show();

          $.ajax({
			  url: '/twitter/moreaccounts',
			  data: formData,
			  processData: false,
			  contentType: false,
			  type: 'POST',
			  success: function(data){
			    toastr.success('You have successfully been upgraded!');
			    $('#loading').hide();
			  }
			});
        }
      });

      $('#customButtonEnterprise').on('click', function(e) {
        // Open Checkout with further options
        handler2.open({
          name: 'TweetProof',
          description: 'Enterprise Subscription',
          currency: "usd",
          amount: 10000
        });
        e.preventDefault();
      });

      // Close Checkout on page navigation
      $(window).on('popstate', function() {
        handler.close();
        handler1.close();
        handler2.close();
      });
 });
</script>
<style>
#table {
	display: block;
	margin: 0 auto;
	width: 600px;
}

.table {
	width: 200px;
	display: inline-block;
	float: left
}
.table:not(:first-child) {
	margin-left: -5px;
}

.table h1 {
	width: 100%;
	height: 50px;
	color: #fff;
	font-size: 20px;
	font-weight: 600;
	text-align: center;
	vertical-align: middle;
	line-height: 50px;
}

.table.one {
	background-color: #FFF;
	margin: 0;
}

.table.two {
	background-color: #EBEBEB;
}

.table.three {
	background-color: #FFF;
}

.table.one h1 {
	background-color: #41a3e1;
}

.table.two h1 {
	background-color: #0083D6;
}

.table.three h1 {
	background-color: #1F6592;
}

.table .price {
	font-size: 28px;
	height: 40px;
	vertical-align: middle;
	text-align: center;
	line-height: 40px;
	color: #454545;
	font-weight: 600;
	padding: 10px;
	margin-bottom: 20px;
}

.table .price span {
	font-size: 14px;
	display: block;
	text-align: center;
	line-height: 14px;
}

.table .price small {
	font-size: 25px;
	vertical-align: top;
}

.table ul {
	list-style: none;
	width: 180px;
	margin: 0 auto;
	font-size: 12px;
	line-height: 22px;
}

.table ul li {
	vertical-align: middle;
	text-align: center;
	padding: 10px;
	font-weight: 400;
	border-top: 1px solid #ccc;
}

.table ul li b {
	font-size: 16px;
	margin-right: 5px;
	font-weight: 600;
}
</style>
</div>