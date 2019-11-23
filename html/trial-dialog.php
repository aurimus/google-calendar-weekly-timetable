<div>
	<script src="https://www.paypalobjects.com/api/checkout.js" data-version-4></script>
	<p>You can use this plugin for as long as you need with license renewal for 10 days at a time for <strong>free</strong>, when you finished testing - get a license for longer period!</p>

	<time-input inline-template>
		<span class="less sign">‹</span>
		<span class="number">{{days</span>
		<span class="more sign">›</span>
    </time-input>

    <span class="price">$ {{ (renewDays - 10)/180 }}</span>

	<br/> <br/>
	<div id="myContainerElement"></div>
	<script>
	    // Render the button into the container element

	    paypal.Button.render({

	        // Pass the client ids to use to create your transaction on sandbox and production environments

	        client: {
	            sandbox:    'AUsEkhM3qoz2WZEfN_ILkIoXPhBjKMO7XvegzzCGEnTEA1V0iuOrk2OiTyavqXYTy6uFFVI_8FEidrAM', // from https://developer.paypal.com/developer/applications/
	            production: 'Ab3mvRcJZP_xpbE5Q2yNJUjgDV3r7aH56T4cOnljqaBTAVogh7QCfcyWwlyA8EnIUChX7vV1Ib-LkDw6'  // from https://developer.paypal.com/developer/applications/
	        },

	        // Pass the payment details for your transaction
	        // See https://developer.paypal.com/docs/api/payments/#payment_create for the expected json parameters

	        payment: function(data, actions) {
	            return actions.payment.create({
	                transactions: [
	                    {
	                        amount: {
	                            total:    '1.00',
	                            currency: 'USD'
	                        }
	                    }
	                ]
	            });
	        },

	        // Display a "Pay Now" button rather than a "Continue" button

	        commit: true,

	        // Pass a function to be called when the customer completes the payment

	        onAuthorize: function(data, actions) {
	            return actions.payment.execute().then(function(response) {
	                console.log('The payment was completed!');
	            });
	        },

	        // Pass a function to be called when the customer cancels the payment

	        onCancel: function(data) {
	            console.log('The payment was cancelled!');
	        }

	    }, '#myContainerElement');
	</script>

</div>