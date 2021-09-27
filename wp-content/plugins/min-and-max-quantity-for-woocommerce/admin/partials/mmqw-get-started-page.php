<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );
?>
	<div class="mmqw-section-left">
		<div class="mmqw-main-table res-cl">
			<h2><?php esc_html_e( 'Thanks For Installing Minimum and Maximum Quantity for WooCommerce', 'min-and-max-quantity-for-woocommerce' ); ?></h2>
			<table class="table-outer">
				<tbody>
				<tr>
					<td class="fr-2">
						<p class="block gettingstarted">
							<strong><?php esc_html_e( 'Getting Started', 'min-and-max-quantity-for-woocommerce' ); ?></strong>
						</p>
						<p class="block textgetting">
							<?php esc_html_e( 'Goto Checkout Settings > Add the Min Max global settings value as per your business need.', 'min-and-max-quantity-for-woocommerce' ); ?>
						</p>
						<p class="block textgetting">
							<?php
							echo sprintf( wp_kses( __( '<strong>Step 1: </strong>Add the value in the below textbox as per your requirement and add the 999999 in the max value for unlimited value.'
									, 'min-and-max-quantity-for-woocommerce' )
								, array( 'strong' => array() ) ) );
							?>
							<span class="gettingstarted">
                                <img src="<?php echo esc_url( MMQW_PLUGIN_URL . 'admin/images/Getting_Started_01.png' ); ?>">
                            </span>
						</p>
						<p class="block gettingstarted textgetting">
							<?php
							echo sprintf( wp_kses( __( '<strong>Step 2: </strong>Add the global messages for your cart once any requirement does not match.'
									, 'min-and-max-quantity-for-woocommerce' )
								, array( 'strong' => array() ) ) );
							?>
							<span class="gettingstarted">
                                <img src="<?php echo esc_url( MMQW_PLUGIN_URL . 'admin/images/Getting_Started_02.png' ); ?>">
                            </span>
						</p>
						<p class="block gettingstarted textgetting">
							<?php
							echo sprintf( wp_kses( __( '<strong>Step 3: </strong>Add the Min Max rules with title based on your  business requirement.'
									, 'min-and-max-quantity-for-woocommerce' )
								, array( 'strong' => array() ) ) );
							?>
							<span class="gettingstarted">
                                <img src="<?php echo esc_url( MMQW_PLUGIN_URL . 'admin/images/Getting_Started_03.png' ); ?>">
                            </span>
						</p>
						<p class="block gettingstarted textgetting">
							<?php
							echo sprintf( wp_kses( __( '<strong>Important Note: </strong>This plugin override the rules if you created twice or more than that and consider the last rules Min Max value from the rules order.'
									, 'min-and-max-quantity-for-woocommerce' )
								, array( 'strong' => array() ) ) );
							?>
						</p>
						<p class="block gettingstarted textgetting">
							<?php
							echo sprintf( wp_kses( __( '<strong>Tokens for the messages: </strong>{MIN_ORDER_QTY}, {ORDER_QTY}, {MAX_ORDER_QTY}, {MIN_CART_VALUE}, {CART_VALUE}, {MIN_ORDER_ITEM}, {MAX_ORDER_ITEM}'
									, 'min-and-max-quantity-for-woocommerce' )
								, array( 'strong' => array() ) ) );
							?>
						</p>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>

<?php
require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php' );