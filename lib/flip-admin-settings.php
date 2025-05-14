<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters('flipforbusiness_settings',
	array(
      'enabled'	=> array(
			'title'		=> 'Enable/Disable',
			'type'		=> 'checkbox',
			'label'		=> 'Enable Flip for Business',
			'default'	=> 'yes'
      ),
      'environment'	=> array(
			'title'			=> 'Environment',
			'type'			=> 'select',
			'description'	=> 'Choose whether you want to use the TestMode/Sandbox or LiveMode/Production environment.',
			'default'		=> 'sandbox',
			'options'		=> array(
				'sandbox'		=> 'TestMode/Sandbox',
				'production'   => 'LiveMode/Production',
			),
      ),
      'api_key_sandbox'	=> array(
			'title'			=> 'API Secret Key - TestMode/Sandbox',
			'type'			=> 'password',
			'description'	=> 'Enter your TestMode/Sandbox API Secret Key. </br> Make sure your api secret key value doesn\'t contain any whitespace.',
			'default'		=> '',
      ),
      'api_key_production'	=> array(
			'title'			=> 'API Secret Key - LiveMode/Production',
			'type'			=> 'password',
			'description'	=> 'Enter your LiveMode/Production API Secret Key. </br> Make sure your api secret key value doesn\'t contain any whitespace.',
			'default'		=> '',
      ),
      'token_validation_sandbox'	=> array(
			'title'			=> 'Token Validation - TestMode/Sandbox',
			'type'			=> 'password',
			'description'	=> 'Enter your TestMode/Sandbox Token Validation. </br> Make sure your token validation value doesn\'t contain any whitespace.',
			'default'		=> '',
      ),
      'token_validation_production'	=> array(
			'title'			=> 'Token Validation - LiveMode/Production',
			'type'			=> 'password',
			'description'	=> 'Enter your LiveMode/Production Token Validation. </br> Make sure your token validation value doesn\'t contain any whitespace.',
			'default'		=> '',
      ),
      'title'	=> array(
			'title'			=> 'Title',
			'type'			=> 'text',
			'description'	=> 'This controls the title which the user sees during checkout.',
			'default'		=> 'Flip Payment',
      ),
      'description'	=> array(
			'title'			=> 'Description',
			'type'			=> 'textarea',
			'description'	=> 'This controls the description which the user sees during checkout.',
			'default' 		=> 'Pay with Flip for Business.',
      ),
		// 'charge_fee' => array(
      //    'title'     	=> 'Charge Fee',
      //    'type'      	=> 'checkbox',
		// 	'label'     	=> 'Charge Fee for Customer',
		// 	'description' 	=>	'The fee is charged to the customer',
      //    'default'      => 'no'
      // ),
      'expiry'	=> array(
			'title'			=> 'Expiry',
			'type'			=> 'text',
			'description'	=> 'This will allow you to set custom duration on how long the transaction available to be paid.<br> example: 45 minutes, 6 hours',
			'default'		=> '6 hours'
      ),
		'logging' => array(
         'title'     	=> 'Enable Flip Logging',
         'type'      	=> 'checkbox',
			'label'     	=> 'Log debug messages',
			'description' 	=>	'Save debug messages to the WooCommerce System Status log.',
         'default'      => 'no'
      ),
		'notification_url_display'             => array(
			'title'         => 'Notification URL value',
			'type'          => 'title',
			'description'   => 'After you have filled required config above, don\'t forget to scroll to bottom and click  <strong>Save Changes</strong> button.</br></br><article> <h4>Step configuration:</h4> <ol class="instruction-flip-notification"><li>Login to your <strong><a href="https://business.flip.id" target="_blank">Flip Account</a></strong>, select your environment (Test Mode / Live Mode), go to menu Developer Menu > Manage API.</li><li>Insert <code>'.$this->get_main_notification_url().'</code> as your Accept Payment URL</li><li>Click Save & Test Callback. You should see the result "Callback sent! Callback URL saved."</li></ol></article>',
		),
   )
);