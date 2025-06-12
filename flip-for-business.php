<?php
/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://flip.id/business
 * @since             1.0.3
 * @package           FlipForBusiness_Payments
 *
 * @wordpress-plugin
 * Plugin Name:       Flip for Business
 * Plugin URI:        https://gitlab.com/flip-dev/flip-for-business
 * Description:       Flip for Business is a robust plugin that integrates Flip's secure and efficient payment processing directly into your WordPress e-commerce site. With this plugin, you can offer your customers a smooth, reliable payment experience without ever leaving your store.
 * Version:           1.0.3
 * Author:            Flip
 * Author URI:        https://flip.id/business
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins: woocommerce
 * Requires at least: 6.2
 * Tested up to: 9.1
 * WC requires at least: 9.1
 * WC tested up to: 9.6
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// if the plugin is active, we're good.
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

class FlipForBusiness_Payments {

    /**
     * Plugin Bootstrapping
     * @return 
     */
    public static function init() {

        // 1. Flip Payments gateway class.
        add_action( 'plugins_loaded', array( __CLASS__, 'includes' ), 0 );

        // 2. Register asset
        add_action('wp_enqueue_scripts', array( __CLASS__, 'flip_enqueue_fe_assets'));
        add_action('admin_enqueue_scripts', array( __CLASS__, 'flip_enqueue_admin_assets'));

        // 3. Make the Flip Payments gateway available to WC.
        add_filter( 'woocommerce_payment_gateways', array( __CLASS__, 'flip_add_payment_gateway' ) );

        // 4. Registers WooCommerce Blocks integration.
        add_action( 'woocommerce_blocks_loaded', array( __CLASS__, 'flip_woocommerce_block_support' ) );

        
        // 5. Registers action link plugin
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__),  array( __CLASS__, 'flip_plugin_action_links' ));
        
        // 6. Hook remove button pay if not Expired
        add_action( 'woocommerce_my_account_my_orders_actions', array( __CLASS__, 'flip_remove_pay_button_my_account_orders'), 10, 2 );

        // 7. Declare HPOS Compatibility
        add_action( 'before_woocommerce_init', array( __CLASS__, 'flip_declare_compatibility'));
    }

    /**
     * 1. Plugin includes
     * @return
     */
    public static function includes() {
		if (!class_exists('WC_Payment_Gateway')) return;

        DEFINE ('FLIP_PLUGIN_VERSION', get_file_data(__FILE__, array('Version' => 'Version'), false)['Version'] );

        // shared imports
        require_once dirname( __FILE__ ) . '/lib/abstract/abstract.flip-gateway.php';
        require_once dirname( __FILE__ ) . '/lib/class-flip-gateway.php';
        require_once dirname( __FILE__ ) . '/lib/class-flip-utils.php';
        require_once dirname( __FILE__ ) . '/lib/class-flip-gateway-api.php';
        require_once dirname( __FILE__ ) . '/lib/class-flip-notif-handler.php';
        require_once dirname( __FILE__ ) . '/lib/class-flip-logger.php';
        require_once dirname( __FILE__ ) . '/lib/class-flip-api-requestor.php';
        require_once dirname( __FILE__ ) . '/lib/class-flip-refund-meta-box.php';
	}

    /**
     * 2a. Register Asset Front End
     */
    public static function flip_enqueue_fe_assets() {
        $resources_version = wp_rand();
        // add Style only on checkout and account pages
        if (is_checkout() || is_account_page()) {
            wp_enqueue_style('flip-payment', self::plugin_url() . '/public/css/flip-payment.css', array(), $resources_version);
        }
        // add Script
        // wp_enqueue_script('flip-payment', self::plugin_url() . '/public/js/flip-payment.js', array(), $resources_version, true);
    }
    
    /**
     * 2b. Register Asset Admin CMS
     */
    public static function flip_enqueue_admin_assets() {
        $resources_version = wp_rand();
        // Select 2 in the queue, remove comments if the refund is running
        // wp_enqueue_style('flip-select2', self::plugin_url() . '/public/css/select2.min.css', array(), $resources_version);
        // wp_enqueue_script('flip-select2', self::plugin_url() . '/public/js/select2.min.js',array(), $resources_version, true);
        
        // Enqueue Script
        wp_enqueue_script('flip-admin-payment', self::plugin_url() . '/public/js/flip-admin-payment.js',array(), $resources_version, true);
    }

    /**
     * 3. Add the Flip Payment gateway to the list of available gateways.
     * @return array
     */
    public static function flip_add_payment_gateway($gateways) {
        $gateways[] = 'FlipForBusiness_Gateway';
        $gateways[] = 'FlipForBusiness_Refund_Meta_Box';
        
        return $gateways;
    }

    /**
     * 4. Registers WooCommerce Blocks integration.
     *
     */
    public static function flip_woocommerce_block_support() {
		if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
			require_once dirname( __FILE__ ) . '/lib/blocks/class-flip-payments-blocks.php';
			add_action(
				'woocommerce_blocks_payment_method_type_registration',
				function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
					$payment_method_registry->register( new FlipForBusiness_Block_Support() );
				}
			);
		}
	}

    /**
     * 5. Adds plugin action links
     *
     * @param array $links
     */
    public static function flip_plugin_action_links($links){
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=flip') . '">' . __('Settings', 'flip-for-business') . '</a>',
        );
    
        return array_merge($plugin_links, $links);
    }
    
    /**
     * 6. Change Url Payment if payment gateway flip
     * @param mixed $actions
     * @param mixed $order
     * @return mixed
     */
    public static function flip_remove_pay_button_my_account_orders($actions, $order) {
        $flip_payment_method = $order->get_payment_method();
        $flip_expired = $order->get_meta('_flip_expired_date');
        $flip_link_url = $order->get_meta('_flip_link_url');
        $status_expired = FlipForBusiness_Utils::flip_is_expired($flip_expired);
        
        if (isset($actions['pay']) && $flip_payment_method === "flip") {
            if ($status_expired && !empty($flip_link_url)) {
                unset($actions['pay']); // Hapus tombol "Bayar"
            }else{
                $actions['pay']['url'] = $flip_link_url;
            }
        }
    
        return $actions;
    }

    /**
     * 7. Declare HPOS Compatibility
     * 
     */
    public static function flip_declare_compatibility() {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }


    /* -------------------------------------------------------------------------- */
    /*                                   Helper                                   */
    /* -------------------------------------------------------------------------- */
    /**
	 * Plugin url.
	 *
	 * @return string
	 */
    public static function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

    /**
	 * Plugin url.
	 *
	 * @return string
	 */
	public static function plugin_abspath() {
		return trailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

FlipForBusiness_Payments::init();