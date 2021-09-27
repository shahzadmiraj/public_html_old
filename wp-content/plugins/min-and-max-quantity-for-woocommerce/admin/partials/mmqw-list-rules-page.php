<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$get_action  = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
$get_id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
$get_wpnonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );

$retrieved_nonce = isset( $get_wpnonce ) ? sanitize_text_field( wp_unslash( $get_wpnonce ) ) : '';

$mmqwnonce = wp_create_nonce( 'mmqwnonce' );

require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' );

if ( isset( $get_action ) && 'delete' === sanitize_text_field( wp_unslash( $get_action ) ) ) {
	if ( ! wp_verify_nonce( $retrieved_nonce, 'mmqwnonce' ) ) {
		die( 'Failed security check' );
	}
	$get_post_id = sanitize_text_field( $get_id );
	wp_delete_post( $get_post_id );
	wp_redirect( admin_url( '/admin.php?page=mmqw-rules-list' ) );
	exit;
}

$admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin( '', '' );
$get_all_sm   = $admin_object::mmqw_get_all_rule_list( 'list' );

$default_lang = $admin_object->mmqw_get_default_langugae_with_sitpress();
$getSortOrder = get_option( 'sm_sortable_order_' . $default_lang );
?>
	<div class="mmqw-section-left">
		<div class="mmqw-main-table res-cl">
			<div class="product_header_title">
				<h2>
					<?php esc_html_e( 'Min/Max Rules', 'min-and-max-quantity-for-woocommerce' ); ?>
					<a class="add-new-btn"
					   href="<?php echo esc_url( add_query_arg( array( 'page' => 'mmqw-add-rules' ), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Add New Min/Max Rule', 'min-and-max-quantity-for-woocommerce' ); ?></a>
					<a id="delete-shipping-method" class="delete-shipping-method"><?php esc_html_e( 'Delete (Selected)', 'min-and-max-quantity-for-woocommerce' ); ?></a>
					<a class="shipping-methods-order" style="display: none;"><?php esc_html_e( 'Save Order', 'min-and-max-quantity-for-woocommerce' ); ?></a>
				</h2>
			</div>
			<table id="shipping-methods-listing" class="table-outer form-table shipping-methods-listing tablesorter">
				<thead>
				<tr class="mmqw-head">
					<th><input type="checkbox" name="check_all" class="condition-check-all"></th>
					<th><?php esc_html_e( 'Min/Max Rule Title1', 'min-and-max-quantity-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Status', 'min-and-max-quantity-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'min-and-max-quantity-for-woocommerce' ); ?></th>
				</tr>
				</thead>

				<?php if ( ! empty( $get_all_sm ) && is_array( $get_all_sm ) ) { ?>
					<tbody>
					<?php
					foreach ( $get_all_sm as $sm ) {
						$shipping_title       = get_the_title( $sm->ID ) ? get_the_title( $sm->ID ) : 'Fee';
						$admin_shipping_title = get_post_meta( $sm->ID, 'fee_settings_unique_shipping_title', true );
						if ( empty( $admin_shipping_title ) ) {
							$admin_shipping_title = $shipping_title . ' - ' . $sm->ID;
						} else {
							$admin_shipping_title = $admin_shipping_title;
						}
						$shipping_cost       = get_post_meta( $sm->ID, 'sm_product_cost', true );
						$sm_is_taxable       = get_post_meta( $sm->ID, 'sm_select_taxable', true );
						$shipping_status     = get_post_status( $sm->ID );
						$shipping_status_chk = ( ( ! empty( $shipping_status ) && 'publish' === $shipping_status ) || empty( $shipping_status ) ) ? 'checked' : '';
						?>
						<tr id="<?php echo esc_attr( $sm->ID ); ?>">
							<td width="10%">
								<input type="checkbox" name="multiple_delete_fee[]" class="multiple_delete_fee" value="<?php echo esc_attr( $sm->ID ); ?>">
							</td>
							<td>
								<a href="<?php echo esc_url( add_query_arg( array(
									'page'     => 'mmqw-edit-rule',
									'id'       => esc_attr( $sm->ID ),
									'action'   => 'edit',
									'_wpnonce' => esc_attr( $mmqwnonce ),
								), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( $admin_shipping_title, 'min-and-max-quantity-for-woocommerce' ); ?></a>
							</td>
							<td>
								<label class="switch">
									<input type="checkbox" name="shipping_status" id="shipping_status_id" value="on" <?php echo esc_attr( $shipping_status_chk ); ?>
									       data-smid="<?php echo esc_attr( $sm->ID ); ?>">
									<div class="slider round"></div>
								</label>
								<div style="display: none;">
								<?php echo ('checked' === $shipping_status_chk)?'Yes':'No';?>
								</div>
							</td>
							<td>
								<a class="fee-action-button button-primary" href="<?php echo esc_url( add_query_arg( array(
									'page'     => 'mmqw-edit-rule',
									'id'       => esc_attr( $sm->ID ),
									'action'   => 'edit',
									'_wpnonce' => esc_attr( $mmqwnonce ),
								), admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Edit', 'min-and-max-quantity-for-woocommerce' ); ?></a>
								<a class="fee-action-button button-primary" href="<?php echo esc_url( add_query_arg( array(
									'page'     => 'mmqw-rules-list',
									'id'       => esc_attr( $sm->ID ),
									'action'   => 'delete',
									'_wpnonce' => esc_attr( $mmqwnonce ),
								), admin_url( 'admin.php' ) ) ); ?>"
								   onclick="return confirm('<?php esc_html_e( 'Are you sure you want to delete this rule?', 'min-and-max-quantity-for-woocommerce' ) ?>');
										   "><?php esc_html_e( 'Delete', 'min-and-max-quantity-for-woocommerce' ); ?></a>
								<a class="fee-action-button button-primary" href="javascript:void(0);" id="mmqw_clone_rule"
								   data-attr="<?php echo esc_attr( $sm->ID ); ?>"><?php esc_html_e( 'Clone', 'min-and-max-quantity-for-woocommerce' ); ?></a>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				<?php } ?>
			</table>
		</div>
	</div>

<?php
require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php' ); ?>