<?php
/**
 * This class extends WP Webhooks and WP Webhooks Pro
 * with a custom webhook trigger endpoint.
 * 
 * The trigger endpoint is called "Demo Send Data On Register" and can be activated 
 * within your WordPress dashboard -> Settings -> WP Webhooks/Pro -> Settings
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'WP_Webhooks_Email_Integration_Triggers' ) ){

	class WP_Webhooks_Email_Integration_Triggers{

		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'add_webhook_triggers' ), 10 );
			add_filter( 'wpwhpro/webhooks/get_webhooks_triggers', array( $this, 'add_webhook_triggers_content' ), 10 );
		}

		/**
		 * ######################
		 * ###
		 * #### WEBHOOK TRIGGERS
		 * ###
		 * ######################
		 */

		/*
         * Regsiter all available webhook triggers
         */
		public function add_webhook_triggers_content( $triggers ){

			$triggers[] = $this->trigger_send_email_content();

			return $triggers;
		}

		/*
         * Add the specified webhook triggers logic.
         * We also add the demo functionality here
         */
		public function add_webhook_triggers() {

			if( ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', 'send_email' ) ) ){
				add_filter( 'wp_mail', array( $this, 'ironikus_trigger_send_email' ), 10, 1 );
				add_filter( 'ironikus_demo_test_send_email', array( $this, 'ironikus_send_demo_test_send_email' ), 10, 3 );
			}

		}

		/*
        * Register the trigger as an element
        */
		public function trigger_send_email_content(){

			$parameter = array(
				'to' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) A string containing one or multiple emails (as a comma-separated list) of the receivers of the email.', 'trigger-send_email-content' ) ),
				'subject' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The subject of the email.', 'trigger-send_email-content' ) ),
				'message' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The main mesage (body) of the email.', 'trigger-send_email-content' ) ),
				'headers' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the outgoing email.', 'trigger-send_email-content' ) ),
				'attachments' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) An array of given email attachments.', 'trigger-send_email-content' ) ),
			);

			$translation_ident = "trigger-send_email-description";

			ob_start();
			?>
<?php echo WPWHPRO()->helpers->translate( "This webhook trigger is used to send data once an email is sent from your WordPress website via the <code>wp_mail()</code> function.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This description is uniquely made for the <strong>Send Data On Outgoing Email</strong> (send_email) webhook trigger.", $translation_ident ); ?>
<br><br>
<h4><?php echo WPWHPRO()->helpers->translate( "How to use <strong>Send Data On Outgoing Email</strong> (send_email)", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "To get started, you need to add your receiving URL endpoint, that accepts webhook requests, from the third-party provider or service you want to use.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Once you have this URL, please place it into the <strong>Webhook URL</strong> field above.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "For better identification of the webhook URL, we recommend to also fill the <strong>Webhook Name</strong> field. This field will be used as the slug for your webhook URL. In case you leave it empty, we will automatically generate a random number as an identifier.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "After you added your <strong>Webhook URL</strong>, press the <strong>Add</strong> button to finish adding the entry.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "That's it! Now you can receive data on the URL once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Next to the <strong>Webhook URL</strong>, you will find a settings item, which you can use to customize the payload/request.", $translation_ident ); ?></li>
</ol>
<br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "When does this trigger fire?", $translation_ident ); ?></h4>
<br>
<?php echo WPWHPRO()->helpers->translate( "This trigger is registered on the <strong>wp_mail</strong> hook:", $translation_ident ); ?> 
<a title="wordpress.org" target="_blank" href="https://developer.wordpress.org/reference/hooks/wp_mail/">https://developer.wordpress.org/reference/hooks/wp_mail/</a>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Here is the call within our code we use to fire this trigger:", $translation_ident ); ?>
<pre>add_filter( 'wp_mail', array( $this, 'ironikus_trigger_send_email' ), 10, 1 );</pre>
<?php echo WPWHPRO()->helpers->translate( "<strong>IMPORTANT</strong>: Please note that this webhook fires before the email itself is sent. This is due to the fact that the <strong>wp_mail</strong> filter is fired before the actual email is send out.", $translation_ident ); ?>
<br><br><br>

<h4><?php echo WPWHPRO()->helpers->translate( "Tipps", $translation_ident ); ?></h4>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "In case you don't need a specified webhook URL at the moment, you can simply deactivate it by clicking the <strong>Deactivate</strong> link next to the <strong>Webhook URL</strong>. This results in the specified URL not being fired once the trigger fires.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can use the <strong>Send demo</strong> button to send a static request to your specified <strong>Webhook URL</strong>. Please note that the data sent within the request might differ from your live data.", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "Within the <strong>Settings</strong> link next to your <strong>Webhook URL</strong>, you can use customize the functionality of the request. It contains certain default settings like changing the request type the data is sent in, or custom settings, depending on your trigger. An explanation for each setting is right next to it. (Please don't forget to save the settings once you changed them - the button is at the end of the popup.)", $translation_ident ); ?></li>
    <li><?php echo WPWHPRO()->helpers->translate( "You can also check the response you get from the demo webhook call. To check it, simply open the console of your browser and you will find an entry there, which gives you all the details about the response.", $translation_ident ); ?></li>
</ol>
<br><br>

<?php echo WPWHPRO()->helpers->translate( "In case you would like to learn more about our plugin, please check out our documentation at:", $translation_ident ); ?>
<br>
<a title="Go to ironikus.com/docs" target="_blank" href="https://ironikus.com/docs/article-categories/get-started/">https://ironikus.com/docs/article-categories/get-started/</a>
			<?php
			$description = ob_get_clean();

			$settings = array();

			return array(
				'trigger' => 'send_email',
				'name'  => WPWHPRO()->helpers->translate( 'Send Data On Outgoing Email', 'trigger-send_email-content' ),
				'parameter' => $parameter,
				'settings'          => $settings,
				'returns_code'      => WPWHPRO()->helpers->display_var( $this->ironikus_send_demo_test_send_email ( array(), '', '' ) ), //Display some response code within the frontend
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires while an email is being sent from your WordPress site.', 'trigger-send_email-content' ),
				'description' => $description,
				'callback' => 'test_send_email'
			);

		}

		/*
        * Register the send_email trigger as an element
        */
		public function ironikus_trigger_send_email( $atts ){
			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'send_email' );
			$response_data = array();

			foreach( $webhooks as $webhook ){
				$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

				if( $webhook_url_name !== null ){
					$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $atts );
				} else {
					$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $atts );
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_send_email', $atts, $response_data );

			return $atts;
		}

		/*
        * Register the send_email data response
        */
		public function ironikus_send_demo_test_send_email( $data, $webhook, $webhook_group ){

			$data = array (
				"to" => 'test@test.demo',
				"subject" => 'This is the subject',
				"message" => htmlspecialchars( 'This is a <strong>HTML</strong> message!' ),
				"headers" => array(
					"Content-Type: text/html; charset=UTF-8",
					"From: Sender Name <anotheremail@someemail.demo>",
					"Cc: Receiver Name <receiver@someemail.demo>",
					"Cc: onlyemail@someemail.demo",
					"Bcc: bccmail@someemail.demo",
					"Reply-To: Reply Name <replytome@someemail.demo>",
				),
				"attachments" => array(
					"/Your/full/server/path/wp-content/uploads/2020/06/my-custom-file.jpg",
					"/Your/full/server/path/wp-content/uploads/2020/06/another-custom-file.jpg",
				)
			);

			return $data;
		}

	}

	new WP_Webhooks_Email_Integration_Triggers();

}