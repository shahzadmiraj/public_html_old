<?php
/**
 * Plugin Name:         Minimum and Maximum Quantity for WooCommerce
 * Plugin URI:          https://www.thedotstore.com/
 * Description:         We can set a minimum and maximum allowable product quantity and/or price that can be purchased for each product storewide, or just for an individual product.
 * Version:             1.0.4
 * Author:              theDotstore
 * Author URI:          https://www.thedotstore.com/
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         min-and-max-quantity-for-woocommerce
 * Domain Path:         /languages
 *
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'MMQW_PLUGIN_VERSION' ) ) {
	define( 'MMQW_PLUGIN_VERSION', '1.0.4' );
}
if ( ! defined( 'MMQW_PLUGIN_URL' ) ) {
	define( 'MMQW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'MMQW_PLUGIN_DIR' ) ) {
	define( 'MMQW_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'MMQW_PLUGIN_DIR_PATH' ) ) {
	define( 'MMQW_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'MMQW_SLUG' ) ) {
	define( 'MMQW_SLUG', 'min-and-max-quantity-for-woocommerce' );
}
if ( ! defined( 'MMQW_PLUGIN_BASENAME' ) ) {
	define( 'MMQW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'MMQW_PLUGIN_NAME' ) ) {
	define( 'MMQW_PLUGIN_NAME', 'Minimum and Maximum Quantity for WooCommerce' );
}
if ( ! defined( 'MMQW_TEXT_DOMAIN' ) ) {
	define( 'MMQW_TEXT_DOMAIN', 'min-and-max-quantity-for-woocommerce' );
}
if ( ! defined( 'MMQW_FEE_AMOUNT_NOTICE' ) ) {
	define( 'MMQW_FEE_AMOUNT_NOTICE', 'If entered fee amount is less than cart subtotal it will reflect with minus sign (EX: $ -10.00) <b>OR</b> If entered fee amount is more than cart subtotal then the total amount shown as zero (EX: Total: 0)' );
}
if ( ! defined( 'MMQW_PERTICULAR_FEE_AMOUNT_NOTICE' ) ) {
	define( 'MMQW_PERTICULAR_FEE_AMOUNT_NOTICE', 'You can turn off this button, if you do not need to apply below min max rules.' );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mmqw-for-woocommerce-activator.php
 */
function mmqw_activate_for_woocommerce() {
	set_transient( 'mmqw-admin-notice', true );
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mmqw-for-woocommerce-activator.php';
	Min_Max_Quantity_For_WooCommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mmqw-for-woocommerce-deactivator.php
 */
function mmqw_deactivate_for_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mmqw-for-woocommerce-deactivator.php';
	Min_Max_Quantity_For_WooCommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'mmqw_activate_for_woocommerce' );
register_deactivation_hook( __FILE__, 'mmqw_deactivate_for_woocommerce' );

add_action( 'admin_init', 'mmqw_deactivate_plugin' );
if ( ! function_exists('mmqw_deactivate_plugin') ) {
	function mmqw_deactivate_plugin() {
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			deactivate_plugins( '/minimum-and-maximum-quantity-for-woocommerce/minimum-and-maximum-quantity-for-woocommerce.php', true );
		}
	}
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mmqw-for-woocommerce.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if ( ! function_exists('mmqw_run_for_woocommerce') ) {
	function mmqw_run_for_woocommerce() {
		$plugin = new Min_Max_Quantity_For_WooCommerce();
		$plugin->run();
	}
}

mmqw_run_for_woocommerce();

add_action( 'admin_notices', 'mmqw_admin_notice_function' );

if ( ! function_exists('mmqw_admin_notice_function') ) {
	function mmqw_admin_notice_function() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( strpos( $screen_id, 'dotstore-plugins_page' ) || strpos( $screen_id, 'plugins' ) ) {
			$mmqw_admin     = filter_input( INPUT_GET, 'mmqw-hide-notice', FILTER_SANITIZE_STRING );
			$wc_notice_nonce = filter_input( INPUT_GET, '_mmqw_notice_nonce', FILTER_SANITIZE_STRING );
			if ( isset( $mmqw_admin ) && $mmqw_admin === 'mmqw_admin' && wp_verify_nonce( sanitize_text_field( $wc_notice_nonce ), 'mmqw_hide_notices_nonce' ) ) {
				delete_transient( 'mmqw-admin-notice' );
			}

			/* Check transient, if available display notice */
			if ( get_transient( 'mmqw-admin-notice' ) ) {
				?>
				<div id="message" class="updated woocommerce-message woocommerce-admin-promo-messages welcome-panel mmqw-panel">
					<a class="woocommerce-message-close notice-dismiss"
					href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'mmqw-hide-notice', 'mmqw_admin' ), 'mmqw_hide_notices_nonce', '_mmqw_notice_nonce' ) ); ?>">
					</a>
					<p>
						<?php
						echo sprintf( wp_kses( __( '<strong>Minimum and Maximum Quantity for WooCommerce is successfully installed and ready to go.</strong>', 'min-and-max-quantity-for-woocommerce' )
							, array( 'strong' => array() ), esc_url( admin_url( 'options-general.php' ) ) ) );
						?>
					</p>
					<p>
						<?php echo wp_kses_post( __( 'Click on settings button and create your shipping method with multiple rules', 'min-and-max-quantity-for-woocommerce' ) ); ?>
					</p>
					<?php
					$url = add_query_arg( array( 'page' => 'mmqw-rules-list' ), admin_url( 'admin.php' ) );
					?>
					<p>
						<a href="<?php echo esc_url( $url ); ?>"
						class="button button-primary"><?php esc_html_e( 'Settings', 'min-and-max-quantity-for-woocommerce' ); ?></a>
					</p>
				</div>
				<?php
			}
		} else {
			return;
		}
	}
}