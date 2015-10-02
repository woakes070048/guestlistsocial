<form action="" method="POST">
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="pk_test_jhuRvD7XWwgcdDLE1Ymgvq8e"
    data-amount="3000"
    data-name="Demo Site"
    data-description="Basic Subscription"
    data-image="/img/logo.png"
    data-locale="auto"
    data-email="<? echo $this->Session->read('Auth.User.email');?>">
  </script>
</form>
<?echo $this->Html->script("https://code.jquery.com/jquery-1.9.1.min.js");?>
<script src="https://checkout.stripe.com/checkout.js"></script>
<form action="" method="POST">
<button id="customButton">Go</button>
</form>

<script>
$(document).ready(function () {
      var handler = StripeCheckout.configure({
        key: 'pk_test_jhuRvD7XWwgcdDLE1Ymgvq8e',
        image: '/img/logo.png',
        locale: 'auto',
        token: function(token) {
          // Use the token to create the charge with a server-side script.
          // You can access the token ID with `token.id`
        }
      });

      $('#customButton').on('click', function(e) {
        // Open Checkout with further options
        handler.open({
          name: 'TweetProof',
          description: 'Basic Subscription',
          currency: "usd",
          amount: 3000
        });
        e.preventDefault();
      });

      // Close Checkout on page navigation
      $(window).on('popstate', function() {
        handler.close();
      });
 });
</script>