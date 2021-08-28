<?php
/**
 * WCFM plugin view
 *
 * WCFM Order Details View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.0.0
 */

global $wp, $WCFM, $WCFMmp, $theorder, $wpdb;

$wcfm_is_allow_orders = apply_filters( 'wcfm_is_allow_orders', true );
if( !$wcfm_is_allow_orders ) {
	wcfm_restriction_message_show( "Orders" );
	return;
}


if( isset( $wp->query_vars['wcfm-orders-details'] ) && !empty( $wp->query_vars['wcfm-orders-details'] ) ) {
	$order_id = $wp->query_vars['wcfm-orders-details'];
}

if( !$order_id ) return;

if( wcfm_is_vendor() ) {
	$is_order_for_vendor = $WCFM->wcfm_vendor_support->wcfm_is_order_for_vendor( $order_id );
	if( !$is_order_for_vendor ) {
		if( apply_filters( 'wcfm_is_show_order_restrict_message', true, $order_id ) ) {
			wcfm_restriction_message_show( "Restricted Order" );
		} else {
			echo apply_filters( 'wcfm_show_custom_order_restrict_message', '', $order_id );
		}
		return;
	}
}

$theorder = wc_get_order( $order_id );

if( !is_a( $theorder, 'WC_Order' ) ) {
	wcfm_restriction_message_show( "Invalid Order" );
	return;
}

if( !$theorder ) return;

$post = get_post($order_id);
$order = $theorder;

$WCFM->library->init_address_fields();

if ( WC()->payment_gateways() ) {
	$payment_gateways = WC()->payment_gateways->payment_gateways();
} else {
	$payment_gateways = array();
}

if( !is_a( $order, 'WC_Order' ) ) $payment_method = '';
else $payment_method = ! empty( $order->get_payment_method() ) ? $order->get_payment_method() : '';

$order_type_object = get_post_type_object( $post->post_type );

// Get line items
$line_items          = $order->get_items( 'line_item' );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );

$order_taxes = $classes_options = array();
if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) {
	if ( wc_tax_enabled() ) {
		$order_taxes         = $order->get_taxes();
		$tax_classes         = WC_Tax::get_tax_classes();
		$classes_options[''] = __( 'Standard', 'wc-frontend-manager' );
	
		if ( ! empty( $tax_classes ) ) {
			foreach ( $tax_classes as $class ) {
				$classes_options[ sanitize_title( $class ) ] = $class;
			}
		}
	
		// Older orders won't have line taxes so we need to handle them differently :(
		$tax_data = '';
		if ( $line_items ) {
			$check_item = current( $line_items );
			$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
		} elseif ( $line_items_shipping ) {
			$check_item = current( $line_items_shipping );
			$tax_data = maybe_unserialize( isset( $check_item['taxes'] ) ? $check_item['taxes'] : '' );
		} elseif ( $line_items_fee ) {
			$check_item = current( $line_items_fee );
			$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
		}
		
		$legacy_order     = ! empty( $order_taxes ) && empty( $tax_data ) && ! is_array( $tax_data );
		$show_tax_columns = ! $legacy_order || sizeof( $order_taxes ) === 1;
	}
}

$wcfm_generate_csv_url = apply_filters( 'wcfm_generate_csv_url', '', $order_id );

$statuses = apply_filters( 'wcfm_allowed_order_status', wc_get_order_statuses(), $order_id );
$current_order_status = apply_filters( 'wcfm_current_order_status', $order->get_status(), $order->get_id() );

$status_update_block_statuses = apply_filters( 'wcfm_status_update_block_statuses', array( 'refunded', 'cancelled', 'failed' ), $order_id );

// Marketplace Filters
$line_items          = apply_filters( 'wcfm_valid_line_items', $line_items, $order->get_id() );
$line_items_shipping = apply_filters( 'wcfm_valid_shipping_items', $line_items_shipping, $order->get_id() );

do_action( 'before_wcfm_orders_details', $order_id );

?>

<div class="collapse wcfm-collapse" id="wcfm_order_details">

  <div class="wcfm-page-headig">
		<span class="wcfmfa fa-cart-arrow-down"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Order Details', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Order #', 'wc-frontend-manager' ); echo $theorder->get_order_number(); ?></h2>
			<span class="order-status order-status-<?php echo sanitize_title( $current_order_status ); ?>"><?php _e( ucfirst(wc_get_order_status_name( $current_order_status )), 'wc-multivendor-marketplace' ); ?></span>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('post.php?post='.$order_id.'&action=edit'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fab fa-wordpress fa-wordpress-simple"></span></a>
				<?php
			}
			?>
			
			<div id="order_quick_actions">
				<?php
				if( $wcfm_is_allow_export_csv = apply_filters( 'wcfm_is_allow_export_csv', true ) ) {
					if( $wcfm_generate_csv_url ) {
						echo '<a class="wcfm_csv_export order_quick_action add_new_wcfm_ele_dashboard" href="'.$wcfm_generate_csv_url.'" data-orderid="' . $order_id . '"><span class="wcfmfa fa-file-excel-o text_tip" data-tip="' . esc_attr__( 'CSV Export', 'wc-frontend-manager' ) . '"></span></a>';
					}
				}
				
				if( apply_filters( 'wcfm_is_allow_pdf_invoice', true ) || apply_filters( 'wcfm_is_allow_pdf_packing_slip', true ) ) {
					if( WCFM_Dependencies::wcfmu_plugin_active_check() && WCFM_Dependencies::wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() ) {
						echo apply_filters ( 'wcfm_orders_module_actions', '', $order_id, $theorder );
					} else {
						if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
							if( wcfm_is_vendor() ) {
								echo '<a class="wcfm_pdf_invoice_vendor_dummy order_quick_action add_new_wcfm_ele_dashboard" href="#" data-orderid="' . $order_id . '"><span class="wcfmfa fa-file-invoice text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
							} else {
								echo '<a class="wcfm_pdf_invoice_dummy order_quick_action add_new_wcfm_ele_dashboard" href="#" data-orderid="' . $order_id . '"><span class="wcfmfa fa-file-invoice text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
							}
						}
					}
				}
				do_action( 'wcfm_after_order_quick_actions', $order_id );
				?>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'begin_wcfm_orders_details', $order_id ); ?>
	  
		<div class="wcfm-container">
			<div id="orders_details_general_expander" class="wcfm-content">
	
				<p class="form-field form-field-wide"><label for="order_date"><?php _e( 'Order date:', 'wc-frontend-manager' ) ?></label>
					<?php echo date_i18n( wc_date_format() . ' @' . wc_time_format(), strtotime( $post->post_date ) ); ?>
				</p>
				
				<?php if( apply_filters( 'wcfm_is_allow_order_status_update', true ) ) { ?>
					<div id="wcfm_order_status_update_wrapper" class="wcfm_order_status_update_wrapper">
						<p class="form-field form-field-wide wc-order-status">
							<label for="order_status"><?php _e( 'Order status:', 'wc-frontend-manager' ) ?> 
								<?php
								if( $wcfm_is_allow_order_details = apply_filters( 'wcfm_allow_order_details', true ) ) {
									if ( $order->needs_payment() ) {
										printf( '<a target="_blank" href="%s">%s &rarr;</a>',
											esc_url( $order->get_checkout_payment_url() ),
											__( 'Customer payment page', 'wc-frontend-manager' )
										);
									}
								}
								?>
							</label>
							<?php
							  if( !in_array( $current_order_status, $status_update_block_statuses ) && apply_filters( 'wcfm_is_allow_order_status_change_active', true, $order_id, $order ) ) {
							  	$order_status = '<select id="wcfm_order_status" name="order_status">';
							  } else {
							  	$order_status = '<select id="wcfm_order_status" name="order_status" disabled>';
							  }
								foreach ( $statuses as $status => $status_name ) {
									$order_status .= '<option value="' . esc_attr( $status ) . '" ' . selected( $status, 'wc-' . $current_order_status, false ) . '>' . esc_html( $status_name ) . '</option>';
								}
								$order_status .= '</select>';
								if( !in_array( $current_order_status, $status_update_block_statuses ) && apply_filters( 'wcfm_is_allow_order_status_change_active', true, $order_id, $order ) ) {
									$order_status .= '<button class="wcfm_modify_order_status button" id="wcfm_modify_order_status" data-orderid="' . $order->get_id() . '">' .  __( 'Update', 'wc-frontend-manager' ) . '</button>';
								}
								echo $order_status;
							?>
						</p>
						
						<?php do_action( 'wcfm_after_order_status_edit_block', $order_id ); ?>
						
						<div class="wcfm-message" tabindex="-1"></div>
					</div>
			  <?php } ?>		
					
				<?php if ( apply_filters( 'wcfm_allow_order_customer_details', true ) ) { ?>
					<p class="form-field form-field-wide wc-customer-user">
						<label for="customer_user"><?php _e( 'Customer:', 'wc-frontend-manager' ) ?> <?php
							if ( $order->get_user_id() ) {
								$args = array( 'post_status' => 'all',
									'post_type'      => 'shop_order',
									'_customer_user' => absint( $order->get_user_id() )
								);
								printf( '<a target="_blank" href="%s">%s &rarr;</a>',
									esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) ),
									__( 'View other orders', 'wc-frontend-manager' )
								);
							}
						?></label>
						<?php
						$user_string = '';
						$user_id     = '';
						if ( $order->get_user_id() ) {
							$user_id     = absint( $order->get_user_id() );
							$user        = get_user_by( 'id', $user_id );
							if( $user ) {
								$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' - ' . esc_html( $user->user_email ) . ')';
							}
						}
						echo htmlspecialchars( $user_string );
						?>
					</p>
				<?php } ?>
				
				<?php 
				if( apply_filters( 'wcfm_is_allow_woocommerce_admin_order_data_after_order_details', true ) ) {
					do_action( 'woocommerce_admin_order_data_after_order_details', $order );
				}
				?>
		
				<p class="order_number">
					<?php
		
						if ( $payment_method ) {
							printf( __( 'Payment via %s', 'woocommerce' ), ( isset( $payment_gateways[ $payment_method ] ) ? esc_html( $payment_gateways[ $payment_method ]->get_title() ) : esc_html( $payment_method ) ) );
			
							if ( apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
								if ( $transaction_id = $order->get_transaction_id() ) {
										if ( isset( $payment_gateways[ $payment_method ] ) && ( $url = $payment_gateways[ $payment_method ]->get_transaction_url( $order ) ) ) {
										echo ' (<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $transaction_id ) . '</a>)';
									} else {
										echo ' (' . esc_html( $transaction_id ) . ')';
									}
								}
							}
							echo '. ';
			
							if ( $order->get_date_paid() ) {
								/* translators: 1: date 2: time */
								printf( ' ' . __( 'Paid on %1$s @ %2$s', 'woocommerce' ), wc_format_datetime( $order->get_date_paid() ), wc_format_datetime( $order->get_date_paid(), get_option( 'time_format' ) ) );
							}
			
							echo '. ';
						}
			
						if ( apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
							if ( $ip_address = $order->get_customer_ip_address() ) {
								echo __( 'Customer IP', 'wc-frontend-manager' ) . ': <span class="woocommerce-Order-customerIP">' . esc_html( $ip_address ) . '</span>';
							}
						}
					?>
				</p>
				
				<?php if( apply_filters( 'wcfm_allow_customer_billing_details', true ) || apply_filters( 'wcfm_allow_customer_shipping_details', true ) || apply_filters( 'wcfm_is_allow_view_customer', true ) ) { ?>
					<table>
						<thead>
							<tr>
							  <?php if( apply_filters( 'wcfm_allow_customer_billing_details', true ) || apply_filters( 'wcfm_is_allow_view_customer', true ) ) { ?>
									<th>
										<?php _e( 'Billing Details', 'wc-frontend-manager' ); ?>
									</th>
								<?php } ?>
								
								<?php if( apply_filters( 'wcfm_allow_customer_shipping_details', true ) || apply_filters( 'wcfm_is_allow_view_customer', true ) ) { ?>

									<?php if ( ( $order->needs_shipping_address() || $order->get_formatted_shipping_address() ) || ( ( !$order->needs_shipping_address() || !$order->get_formatted_shipping_address() ) && apply_filters( 'wcfm_is_allow_shipping_column_without_address', true ) ) ) { ?>
										<th>
											<?php _e( 'Shipping Details', 'wc-frontend-manager' ); ?>
										</th>
									<?php } ?>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<tr>
							  <?php if( apply_filters( 'wcfm_allow_customer_billing_details', true ) || apply_filters( 'wcfm_is_allow_view_customer', true ) ) { ?>
									<td>
										<?php
											// Display values
											echo '<div class="address">';
											
											if( apply_filters( 'wcfm_allow_customer_billing_details', true ) ) {
												if ( $order->get_formatted_billing_address() ) {
													echo '<p>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
												} else {
													echo '<p class="none_set">' . __( 'No billing address set.', 'wc-frontend-manager' ) . '</p>';
												}
											}
				
											if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
												foreach ( $WCFM->library->billing_fields as $key => $field ) {
													if ( isset( $field['show'] ) && false === $field['show'] ) {
														continue;
													}
					
													$field_name = 'billing_' . $key;
													
													if( !apply_filters( 'wcfm_allow_view_customer_'.$field_name, true ) ) continue;
					
													if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
														$field_value = $order->{"get_$field_name"}( 'edit' );
													} else {
														$field_value = $order->get_meta( '_' . $field_name );
													}
				
													echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $field_value ) ) . '</p>';
												}
											}
				
											echo '</div>';
				
											if( apply_filters( 'wcfm_is_allow_order_data_after_billing_address', false ) ) {
												do_action( 'woocommerce_admin_order_data_after_billing_address', $order );
											}
											?>
									</td>
								<?php } ?>
								
								<?php if( apply_filters( 'wcfm_allow_customer_shipping_details', true ) || apply_filters( 'wcfm_is_allow_view_customer', true ) ) { ?>

									<?php if ( ( $order->needs_shipping_address() || $order->get_formatted_shipping_address() ) || ( ( !$order->needs_shipping_address() || !$order->get_formatted_shipping_address() ) && apply_filters( 'wcfm_is_allow_shipping_column_without_address', true ) ) ) { ?>
										<td style="vertical-align:top;">
											<?php
												// Display values
												echo '<div class="address">';
												
													if( apply_filters( 'wcfm_allow_customer_shipping_details', true ) ) {


														if ( ( $order->needs_shipping_address() && $order->get_formatted_shipping_address() ) || apply_filters( 'wcfm_is_force_shipping_address', false ) ) {
															echo '<p>' . wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
														} else {
															echo '<p class="none_set">' . __( 'No shipping address set.', 'wc-frontend-manager' ) . '</p>';
														}
													}
					
													if( apply_filters( 'wcfm_allow_view_customer_email', true ) ) {
														if ( ! empty( $WCFM->library->shipping_fields ) ) {
															foreach ( $WCFM->library->shipping_fields as $key => $field ) {
																if ( isset( $field['show'] ) && false === $field['show'] ) {
																	continue;
																}
						
																$field_name = 'shipping_' . $key;
																
																if( !apply_filters( 'wcfm_allow_view_customer_'.$field_name, true ) ) continue;
						
																if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
																	$field_value = $order->{"get_$field_name"}( 'edit' );
																} else {
																	$field_value = $order->get_meta( '_' . $field_name );
																}
						
																echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $field_value ) ) . '</p>';
															}
														}
													}
					
													if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' == get_option( 'woocommerce_enable_order_comments', 'yes' ) ) && $post->post_excerpt ) {
														echo '<p><strong>' . __( 'Customer Provided Note', 'wc-frontend-manager' ) . ':</strong> ' . nl2br( esc_html( $post->post_excerpt ) ) . '</p>';
													}
													
												echo '</div>';
												
												if( apply_filters( 'wcfm_is_allow_order_data_after_shipping_address', false ) ) {
													do_action( 'woocommerce_admin_order_data_after_shipping_address', $order );
												}
												
												do_action( 'wcfm_order_details_after_shipping_address',  $order );
												?>
										</td>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					<?php } ?>
					


					<?php do_action( 'wcfm_order_details_after_address',  $order ); ?>
				
					
					<?php
					if( !wcfm_is_vendor() ) {
						if( !in_array( $current_order_status, apply_filters( 'wcfm_pdf_invoice_download_disable_order_status', array( 'failed', 'cancelled', 'request', 'proposal', 'proposal-sent', 'proposal-expired', 'proposal-rejected', 'proposal-canceled', 'proposal-accepted' ) ) ) ) {
							$wcfm_store_invoices = get_post_meta( $order->get_id(), '_wcfm_store_invoices', true );
							if( $wcfm_store_invoices  && is_array( $wcfm_store_invoices ) && !empty( $wcfm_store_invoices ) ) {
								echo '<h2>' . __( 'Store Invoice(s)', 'wc-frontend-manager' ) . '</h2><div class="wcfm_clearfix"></div>';
								$upload_dir = wp_upload_dir();
								foreach( $wcfm_store_invoices as $vendor_id => $wcfm_store_invoice ) {
									if( file_exists( $wcfm_store_invoice ) ) {
										if (empty($upload_dir['error'])) {
											$upload_base = trailingslashit( $upload_dir['basedir'] );
											$upload_url = trailingslashit( $upload_dir['baseurl'] );
											$invoice_path = str_replace( $upload_base, $upload_url, $wcfm_store_invoice );
											
											$sold_by_text = __( 'Store', 'wc-frontend-manager-ultimate' );
											if( $WCFMmp ) {
												$sold_by_text = $WCFMmp->wcfmmp_vendor->sold_by_label( absint($vendor_id) );
											}
											echo '<a id="wcfm-store-invoice-' . $vendor_id . '" target="_blank" class="add_new_wcfm_ele_dashboard text_tip" style="float:left!important;color:#ffffff!important;margin-right:10px;" href="' . $invoice_path . '" data-tip="' . __('Download Store Invoice', 'wcfm-gosend') . '"><span class="wacfmfa fa-currence">' .get_woocommerce_currency_symbol(). '</span><span class="">' . apply_filters( 'wcfm_store_invoice_download_label', wcfm_get_vendor_store_name( absint($vendor_id) ) . ' ' . $sold_by_text . ' ' . __( 'Invoice', 'wc-frontend-manager-ultimate'), $order->get_id(), $vendor_id ) . '</span></a>';
										}
									}
								}
								echo '<div class="wcfm_clearfix"></div><br />';
							}
						}
					}
					?>
					<?php if( apply_filters( 'wcfm_is_allow_order_details_after_order_table', true ) ) { do_action('woocommerce_order_details_after_order_table',  $order ); } ?>
					<?php do_action( 'wcfm_order_details_after_order_table',  $order ); ?>
					
					<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<!-- end collapsible -->
		
		<?php 

		//start custom label
		do_action( 'before_wcfm_order_items', $order_id ); 

		$billing_address_array =array(
			"billing_first_name"=> get_post_meta( $order->get_id(), '_billing_first_name', true ),
			"billing_last_name"=> get_post_meta( $order->get_id(), '_billing_last_name', true ),
			"billing_phone"=> get_post_meta( $order->get_id(), '_billing_phone', true ),
			"billing_address_1"=> get_post_meta( $order->get_id(), '_billing_address_1', true ),
			"billing_address_2"=> get_post_meta( $order->get_id(), '_billing_address_2', true ),
			"billing_country"=> get_post_meta( $order->get_id(), '_billing_country', true ),
			"billing_city"=> get_post_meta( $order->get_id(), '_billing_city', true ),
			"billing_state"=> get_post_meta( $order->get_id(), '_billing_state', true ),
			"billing_postcode"=> get_post_meta( $order->get_id(), '_billing_postcode', true ),
			"billing_email"=> get_post_meta( $order->get_id(), '_billing_email', true ),
			"order_id" => $order->get_id()

		);


		?>


		<div class="wcfm-clearfix"></div><br/>
		<div id="wcfm-edit-billing-info-popup" style="display:none;">
			<div class="page_collapsible">
								Edit Billing Detail
			</div>
			<form method="post" id="wcfm_orders_manage_billing_address_form">
				<div class="">
						<input name="billing_order_id" type="hidden"
						value="<?php echo  $billing_address_array['order_id']; ?>" >	

					<div class="wcfm_popup_wrapper">
						<span class="wcfm_popup_label"><strong>First name</strong></span>
						<input name="billing_first_name" type="text" class="wcfm_popup_input" id="billing_first_name" placeholder="First name" value="<?php echo $billing_address_array['billing_first_name'];  ?>" >	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Last name</strong></span>
						<input name="billing_last_name" type="text" class="wcfm_popup_input" id="billing_last_name" placeholder="Last name" value="<?php echo $billing_address_array['billing_last_name'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Phone</strong></span>
						<input name="billing_phone" type="text" class="wcfm_popup_input" id="billing_phone" placeholder="Phone" value="<?php echo $billing_address_array['billing_phone'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Address 1</strong></span>
						<input name="billing_address_1" type="text" class="wcfm_popup_input" id="billing_address_1" placeholder="Address 1" value="<?php echo $billing_address_array['billing_address_1'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Address 2</strong></span>
						<input name="billing_address_2" type="text" class="wcfm_popup_input" id="billing_address_2" placeholder="Address 2" value="<?php echo $billing_address_array['billing_address_2'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Country</strong></span>
						<input readonly name="billing_country" type="text" class="wcfm_popup_input" id="billing_country" placeholder="Country" value="<?php echo $billing_address_array['billing_country'];  ?>">	
						<div class="wcfm_clearfix"></div>


						<span class="wcfm_popup_label"><strong>City</strong></span>
						<input name="billing_city" type="text" class="wcfm_popup_input" id="billing_city" placeholder="City" value="<?php echo $billing_address_array['billing_city'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>State</strong></span>
						<input name="billing_state" type="text" class="wcfm_popup_input" id="billing_state" placeholder="State" value="<?php echo $billing_address_array['billing_state'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Postcode</strong></span>
						<input name="billing_postcode" type="text" class="wcfm_popup_input" id="billing_postcode" placeholder="Postcode" value="<?php echo $billing_address_array['billing_postcode'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<button name="wcfm_orders_manage_billing_address_button" value="submit" id="wcfm_orders_manage_billing_address_button" class="wcfm_popup_button ">Submit</button>
						<div class="wcfm_clearfix"></div>


					</div>

				</div>
			</form>
	</div>


	<?php 

		//start shipping detail

		$shipping_address_array =array(
			"shipping_first_name"=> get_post_meta( $order->get_id(), '_shipping_first_name', true ),
			"shipping_last_name"=> get_post_meta( $order->get_id(), '_shipping_last_name', true ),
			"shipping_phone"=> get_post_meta( $order->get_id(), '_shipping_phone', true ),
			"shipping_address_1"=> get_post_meta( $order->get_id(), '_shipping_address_1', true ),
			"shipping_address_2"=> get_post_meta( $order->get_id(), '_shipping_address_2', true ),
			"shipping_country"=> get_post_meta( $order->get_id(), '_shipping_country', true ),
			"shipping_city"=> get_post_meta( $order->get_id(), '_shipping_city', true ),
			"shipping_state"=> get_post_meta( $order->get_id(), '_shipping_state', true ),
			"shipping_postcode"=> get_post_meta( $order->get_id(), '_shipping_postcode', true ),
			"shipping_email"=> get_post_meta( $order->get_id(), '_shipping_email', true ),
			"order_id" => $order->get_id()

		);


		?>


		<div class="wcfm-clearfix"></div><br/>
		<div id="wcfm-edit-shipping-info-popup" style="display:none;">
			<div class="page_collapsible">
								Edit Shipping Detail
			</div>
			<form method="post" id="wcfm_orders_manage_shipping_address_form">
				<div class="">
						<input name="shiping_address_order_id" type="hidden"
						value="<?php echo  $shipping_address_array['order_id']; ?>" >	

					<div class="wcfm_popup_wrapper">
						<span class="wcfm_popup_label"><strong>First name</strong></span>
						<input name="shipping_first_name" type="text" class="wcfm_popup_input" id="shipping_first_name" placeholder="First name" value="<?php echo $shipping_address_array['shipping_first_name'];  ?>" >	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Last name</strong></span>
						<input name="shipping_last_name" type="text" class="wcfm_popup_input" id="shipping_last_name" placeholder="Last name" value="<?php echo $shipping_address_array['shipping_last_name'];  ?>">	
						<div class="wcfm_clearfix"></div>

					
						<span class="wcfm_popup_label"><strong>Address 1</strong></span>
						<input name="shipping_address_1" type="text" class="wcfm_popup_input" id="shipping_address_1" placeholder="Address 1" value="<?php echo $shipping_address_array['shipping_address_1'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Address 2</strong></span>
						<input name="shipping_address_2" type="text" class="wcfm_popup_input" id="shipping_address_2" placeholder="Address 2" value="<?php echo $shipping_address_array['shipping_address_2'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Country</strong></span>
						<input readonly name="shipping_country" type="text" class="wcfm_popup_input" id="shipping_country" placeholder="Country" value="<?php echo $shipping_address_array['shipping_country'];  ?>">	
						<div class="wcfm_clearfix"></div>


						<span class="wcfm_popup_label"><strong>City</strong></span>
						<input name="shipping_city" type="text" class="wcfm_popup_input" id="shipping_city" placeholder="City" value="<?php echo $shipping_address_array['shipping_city'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>State</strong></span>
						<input name="shipping_state" type="text" class="wcfm_popup_input" id="shipping_state" placeholder="State" value="<?php echo $shipping_address_array['shipping_state'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Postcode</strong></span>
						<input name="shipping_postcode" type="text" class="wcfm_popup_input" id="shipping_postcode" placeholder="Postcode" value="<?php echo $shipping_address_array['shipping_postcode'];  ?>">	
						<div class="wcfm_clearfix"></div>

						<button name="wcfm_orders_manage_shipping_address_button" value="submit" id="wcfm_orders_manage_shipping_address_button" class="wcfm_popup_button ">Submit</button>
						<div class="wcfm_clearfix"></div>


					</div>

				</div>
			</form>
	</div>



		<?php 

		//start order extra detail

		$order_edit_array =array(
			"payment_details"=> get_post_meta( $order->get_id(), '_transaction_id', true ),
			"invoice_notes"=> get_post_meta( $order->get_id(), '_wcpdf_invoice_notes', true ),
			"delivery_times"=> get_post_meta( $order->get_id(), '_wcfmd_delvery_times', true ),
			"order_id" => $order->get_id(),
			"payment_paid"=> get_post_meta( $order->get_id(), '_wcfm_om_payment_paid', true )

		);

		$delivery_time = "";
		$delivery_date = "";
		if($order_edit_array['delivery_times']){
			$wcfmd_delvery_times = $order_edit_array['delivery_times'];
			$lastArrayString=end($wcfmd_delvery_times);
			$timeStamp = end(explode('|', $lastArrayString));
			$delivery_date = date('Y-m-d', $timeStamp);
			$delivery_time = date('h:m', $timeStamp);
		}

		?>


		<div class="wcfm-clearfix"></div><br/>
		<div id="wcfm-edit-info-popup"  style="display:none">
			<div class="page_collapsible">
								Edit Order information
			</div>
			<form method="post" id="wcfm_orders_manage_edit_form">
				<div class="">
					

						<input name="extra_edit_order_id" type="hidden"
						value="<?php echo  $order_edit_array['order_id']; ?>" >	

					

					<div class="wcfm_popup_wrapper">
						<span class="wcfm_popup_label"><strong>Delivery date</strong></span>
						<input name="current_delivery_date" type="date"  min="<?php echo date("Y-m-d")?>" class="wcfm_popup_input" id="delivery_date" value="<?php echo $delivery_date;  ?>" >	
						<div class="wcfm_clearfix"></div>

						<span class="wcfm_popup_label"><strong>Delivery time</strong></span>
						<input name="current_delivery_time" type="time" class="wcfm_popup_input" id="delivery_time"value="<?php echo $delivery_time;  ?>">	
						<div class="wcfm_clearfix"></div>

					
						<span class="wcfm_popup_label"><strong>Invoice note/order detail</strong></span>
						<input name="current_invoice_note" type="text" class="wcfm_popup_input" id="invoice_note" placeholder="invoice note" value="<?php echo $order_edit_array['invoice_notes'];  ?>">	
						<div class="wcfm_clearfix"></div>

						
						<button name="wcfm_orders_manage_edit_button" value="submit" id="wcfm_orders_manage_edit_button" class="wcfm_popup_button ">Submit</button>
						<div class="wcfm_clearfix"></div>


					</div>

				</div>
			</form>
	</div>


<style>
	.wcfm-add-product .add_multi_input_block.fa-plus-circle{
		display: none !important;
	}
	@media screen and (max-width: 786px) {
		.select2.select2-container.select2-container--default,.wcfm-select.wcfm_ele.associate_product_variation.multi_input_block_element{
			    width: 100% !important;
		}
 
	}
	#orders_details_items_expander ul.wc_coupon_list li.code{
		display: inline-flex !important;
		cursor: pointer;
		padding-top: 5px !important;
	}
	.svg-icon-cross-custom{
		width: 26px;
		stroke: #ca4a1f;
	}
	#orders_details_notes_expander .product-wrapper{
		display: flex;
    justify-content: space-between;
    background: white;
	}



</style>

		<script>


jQuery(document).ready(function($) {
$('#order_shipping_line_items .shipping:first-child h2').html(' Shipping / Extra change Item(s)');

var shippingFooter =jQuery('.wc-order-totals tr:nth-child(2) .label').html().replace("Shipping:","Shipping/Extra Charges");
 $('.wc-order-totals tr:nth-child(2) .label').html(shippingFooter);

if($('.wcfm_order_edit_request').length===0){
	$('.wc-order-edit-line-item').remove();
}

if($('.wcfm_order_edit_request').length>0){

$('#orders_details_general_expander table thead tr:first-child th:first-child').append('<a class="wcfm-action-icon" href="javascript:void(0)" style="margin-left: 20px;" id="wcfm-edit-billing-info-popup-show-button"><span class="wcfmfa fa-edit text_tip" data-tip="Billing edit" data-hasqtip="116" aria-describedby="qtip-116"></span></a>');

$('#orders_details_general_expander table thead tr:first-child th:last-child').append('<a class="wcfm-action-icon" href="javascript:void(0)" style="margin-left: 20px;" id="wcfm-edit-shipping-info-popup-show-button"><span class="wcfmfa fa-edit text_tip" data-tip="shipping edit" data-hasqtip="116" aria-describedby="qtip-116"></span></a>');

$('#orders_details_general_expander').append('<a id="wcfm-order-detail-edit" class="add_new_wcfm_ele_dashboard" href="javascript:void(0)" ><span class="wcfmfa fa-pencil-alt text_tip"></span><span class="text">Detail Edit</span></a><div class="wcfm_clearfix"></div>');

$('.wcfm-add-product').prepend('<a id="wcfm-order-edit-add-product" class="add_new_wcfm_ele_dashboard" href="javascript:void(0)" ><span class="wcfmfa fa-cart-plus"></span><span class="text">Add more product</span></a><div class="wcfm_clearfix"></div>');

var  Coupon_code_li = $('.wc-used-coupons .wc_coupon_list .code');
Coupon_code_li.each(function(index,value){
	var coupon_code_title=$('a span',this).text();
	var icon='<form method="post" class="wc_coupon_list_btn" id="wc_coupon_list_'+index+'">'+
									'<input type="hidden" name="wc_coupon_list_order_id" value="<?php echo  $order_edit_array['order_id']; ?>">'+
									'<input type="hidden" name="coupon_code_title" value="'+coupon_code_title+'">'+
								'<svg  class="svg-icon  svg-icon-cross-custom" viewBox="0 0 20 20">'+
							'<path d="M10.185,1.417c-4.741,0-8.583,3.842-8.583,8.583c0,4.74,3.842,8.582,8.583,8.582S18.768,14.74,18.768,10C18.768,5.259,14.926,1.417,10.185,1.417 M10.185,17.68c-4.235,0-7.679-3.445-7.679-7.68c0-4.235,3.444-7.679,7.679-7.679S17.864,5.765,17.864,10C17.864,14.234,14.42,17.68,10.185,17.68 M10.824,10l2.842-2.844c0.178-0.176,0.178-0.46,0-0.637c-0.177-0.178-0.461-0.178-0.637,0l-2.844,2.841L7.341,6.52c-0.176-0.178-0.46-0.178-0.637,0c-0.178,0.176-0.178,0.461,0,0.637L9.546,10l-2.841,2.844c-0.178,0.176-0.178,0.461,0,0.637c0.178,0.178,0.459,0.178,0.637,0l2.844-2.841l2.844,2.841c0.178,0.178,0.459,0.178,0.637,0c0.178-0.176,0.178-0.461,0-0.637L10.824,10z"></path>'+
						'</svg>';
	$(this).append(icon);
});

// start productSectionHtmlCode
var productSectionHtmlCode = $('.wcfm-orders-manage-add-product-section').html();
$('.wcfm-orders-manage-add-product-section').remove();
$('#orders_details_items_expander').prepend("<div class='wcfm-orders-manage-add-product-section' style='display:none' >"+productSectionHtmlCode+"</div>");
// end productSectionHtmlCode



$( document ).on( "click", ".wcfm_orders_manage_add_products_form_display_button", function(e) {
	e.preventDefault;
	$('#wcfm_orders_manage_edit_section').toggle('slow');
});

$( document ).on( "click", ".wc_coupon_list_btn", function(e) {
	e.preventDefault;
	var form_id= $(this).attr('id');
	var popUp_id = "#wcfm-edit-shipping-info-popup";
	formSubmitAction(popUp_id,"#"+form_id);
});

$( document ).on( "click", ".remove__shipping_from_order", function(e) {
	e.preventDefault;
	var form_id= $(this).data('itemid');
	var popUp_id = "#order_shipping_line_items";
	formSubmitAction(popUp_id,"#remove__shipping_from_order_"+form_id);
});


$( document ).on( "click", "#add_shipping_method_btn", function(e) {
	e.preventDefault;
	var form_id= "#add_shipping_method";
	var popUp_id = ".add_shipping_method";
	formSubmitAction(popUp_id,form_id);
});




$('#wcfm-edit-shipping-info-popup-show-button').click(function(e){
	e.preventDefault;
	var popUp_id = "#wcfm-edit-shipping-info-popup";
	var popUp_form = "#wcfm_orders_manage_shipping_address_form";
	var popUp_submit = "#wcfm_orders_manage_shipping_address_button";
	order_edit(popUp_id,popUp_form,popUp_submit);
});

$('#wcfm-order-edit-add-product').click(function(e){
	e.preventDefault;
	if(jQuery('#associate_products_variation_0').val() === null){
		alert("Please select product");
		return false;
	}
	$('#associate_products .add_multi_input_block.multi_input_block_manupulate.wcfmfa.fa-plus-circle').trigger('click');
	$('.wcfm-add-product #associate_products_quantity_0').val();
	var quantity = jQuery('.wcfm-add-product #associate_products_quantity_0').val();
	jQuery('#associate_products .select2-selection__clear:first').trigger('mousedown');
	$('#associate_products .associate_product_qty:last').val(quantity);
	$('#associate_products .associate_product_qty:first').val("1");
	jQuery('.remove_multi_input_block:first').hide();//remove x first
	
});

    

$('#wcfm-edit-billing-info-popup-show-button').click(function(e){
	e.preventDefault;
	var popUp_id = "#wcfm-edit-billing-info-popup";
	var popUp_form = "#wcfm_orders_manage_billing_address_form";
	var popUp_submit = "#wcfm_orders_manage_billing_address_button";
	order_edit(popUp_id,popUp_form,popUp_submit);
});
$('#wcfm-order-detail-edit').click(function(e){
	e.preventDefault;
	var popUp_id = "#wcfm-edit-info-popup";
	var popUp_form = "#wcfm_orders_manage_edit_form";
	var popUp_submit = "#wcfm_orders_manage_edit_button";
	order_edit(popUp_id,popUp_form,popUp_submit);
});


$( document ).on( "click", "#wcfm_orders_manage_add_products_form_save_button", function(e) {
	e.preventDefault();
	var popUp_id = ".wcfm-orders-manage-add-product-section";
	var popUp_form = "#wcfm_orders_manage_add_products_form";
	formSubmitAction(popUp_id,popUp_form);
});
$( document ).on( "click", ".delete-order-item", function(e) {
	e.preventDefault();
	var itemid = $(this).data("itemid");
	var popUp_id = ".wcfm-orders-manage-add-product-section";
	var popUp_form = "#remove__products_from_order_form_"+itemid;
	formSubmitAction(popUp_id,popUp_form);
});


	function formSubmitAction(popUp_id,popUp_form){
		  jQueryquick_edit = $(this);
			
			// Ajax Call for Fetching Quick Edit HTML
			$(popUp_id).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action  : 'wcfm_modify_order_status',
				wcfm_orders_manage_form : $(popUp_form).serialize(),
				wcfm_orders_manage_billing_address_button: 'submit'
			}	
			
			jQuery.ajax({
				type    :		'POST',
				url     : wcfm_params.ajax_url,
				data    : data,
				success :	function(response) {
					
					$(popUp_id).unblock();
					console.log(response);
					//$response_json = $.parseJSON(response);
					wcfm_notification_sound.play();
					$(popUp_form).trigger('reset');
					//location.reload();
					//$.colorbox.resize();
				}
			});

		}
      
      function order_edit(popUp_id,popUp_form,popUp_submit) {
      	var $popup_width = '70%';
	      if( $(window).width() <= 960 ) {
	        $popup_width = '90%';
	      }

	      jQuery.colorbox( { 
        inline:true, 
        scrolling:true,
        href: popUp_id,
        width: $popup_width,
        onComplete:function() {
        	//$('body').css({"overflow":"hidden"});
        	$(popUp_id).show();
        	$.colorbox.resize();
        	$(popUp_submit).click(function(event) {
						  event.preventDefault();
        			formSubmitAction(popUp_id,popUp_form);
        			//click event 
					});
					
        },
        onClosed: function() {
		     // $('body').css({"overflow":"unset"});
		      $(popUp_id).hide();
		    }


      });
    }


}


	if( $("#associate_products").length > 0 ) {
		$("#associate_products").find('.associate_product').select2( $wcfm_simple_product_select_args );
	}
	
	$('#associate_products').find('.add_multi_input_block').click(function() {
		$('#associate_products').find('.multi_input_block:last').find('.associate_product').val('').select2( $wcfm_simple_product_select_args );
	  $('#associate_products').find('.multi_input_block:last').find('.associate_product_variation').html('').addClass('wcfm_ele_hide');
		$('#associate_products').find('.multi_input_block:last').find('.associate_product_variation_label').addClass('wcfm_ele_hide');
		$('#associate_products').find('.multi_input_block:last').find('.associate_product_qty').val('1');
		variationSelectProperty( $("#associate_products").find('.associate_product') );
	});

	variationSelectProperty( $('#associate_products').find('.multi_input_block:last').find('.associate_product') );
	
	// // Check is Variable Product
	function variationSelectProperty( $element ) {
		$element.on('change', function() {
			$associate_product = $(this);
			$selected_product = $(this).val();
			$variations_html  = '';
			if( $selected_product ) {
				jQuery.each( $wcfm_search_products_list, function( id, product ) {
					if( $selected_product == id ) {
						$variations = product.variations;
						if( !jQuery.isEmptyObject( $variations ) ) {
							$.each($variations, function( $variation_id, $variation_label ) {
								$variations_html += '<option value="' + $variation_id + '">' + $variation_label + '</option>';
							});
							$associate_product.parent().find('.associate_product_variation').html($variations_html).removeClass('wcfm_ele_hide');
							$associate_product.parent().find('.associate_product_variation_label').removeClass('wcfm_ele_hide');
						} else {
							$associate_product.parent().find('.associate_product_variation').html($variations_html).addClass('wcfm_ele_hide');
							$associate_product.parent().find('.associate_product_variation_label').addClass('wcfm_ele_hide');
						}
					}
				});
			} else {
				$associate_product.parent().find('.associate_product_variation').html($variations_html).addClass('wcfm_ele_hide');
				$associate_product.parent().find('.associate_product_variation_label').addClass('wcfm_ele_hide');
			}
		});
	}


			

//ready end

});

		</script>

		<!-- collapsible -->
		<div class="page_collapsible wcfm_orders_manage_add_products_form_display_button"><?php _e('Order items Edit', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container" id="wcfm_orders_manage_edit_section" style="display:none">

							<div class="wcfm-clearfix"></div><br/>
							<div class="page_collapsible">
													Add products
								</div>
							<div class="wcfm-container" style="background: #6666660d;    border-width: 0px 0px 0px 4px;border-style: solid;">
								<form method="post" id="wcfm_orders_manage_add_products_form">
									<div class="">
										

											<input name="add_more_products_order_id" type="hidden"
											value="<?php echo  $order_edit_array['order_id']; ?>" >	

										<div class="wcfm_popup_wrapper">

											<div class="wcfm-add-product">
											<?php
											$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_orders_manage_fields_product', array(  
												                                                                                  "associate_products" => array( 'type' => 'multiinput', 'class' => 'wcfm_non_sortable', 'options' => array( 
																																																																					"product" => array( 'label' => __( 'Product', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'attributes' => array( 'style' => 'width: 50%;' ), 'label_class' => 'wcfm_title', 'class' => 'wcfm-select wcfm_ele associate_product wcfm_popup_input', 'options' => array(), 'value' => '' ),
																																																																					"variation"  => array( 'label' => __( 'Variation', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'label_class' => 'wcfm_title wcfm_ele_hide associate_product_variation_label', 'class' => 'wcfm-select wcfm_ele wcfm_ele_hide associate_product_variation', 'attributes' => array( 'style' => 'width: 50%;' ), 'option' => array(), 'value' => '' ),
																																																																					"quantity"  => array( 'label' => __( 'Quantity', 'wc-frontend-manager-ultimate' ), 'type' => 'number', 'label_class' => 'wcfm_title', 'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input associate_product_qty wcfm_popup_input', 'attributes' => array( 'style' => 'width: 50%;' ), 'value' => '1' )
																																																																				) )
																																																) ) );
											?>
											</div> 


											
											<button type="submit" name="wcfm_orders_manage_add_products_form_save_button" value="submit" id="wcfm_orders_manage_add_products_form_save_button" class="wcfm_popup_button">Submit product</button>

											<div class="wcfm_clearfix"></div>


										</div>

									</div>
								</form>

						</div>


					<div class="wcfm-clearfix"></div><br/>
						<div class="page_collapsible" id="wcfm_om_shipping_head">
							<label class="wcfmfa fa-truck"></label>
							<?php _e('Shipping/Extra Charge', 'wc-frontend-manager'); ?><span></span>
						</div>
					<div class="wcfm-container add_shipping_method" style="background: #6666660d;    border-width: 0px 0px 0px 4px;border-style: solid;">

						<form method="post" id="add_shipping_method" class="wcfm_popup_wrapper">

							<input name="add_shipping_method_order_id" type="hidden"
							value="<?php echo  $order_edit_array['order_id']; ?>" >	

							<?php
								$shipping_methods = WC()->shipping->load_shipping_methods();
								$shipping_method_array = array( '' => __( 'Select Shipping Method/Extra Charge', 'wc-frontend-manager-ultimate' ) );
								if( !empty( $shipping_methods ) ) {
									foreach( $shipping_methods as $shipping_method ) {
										$shipping_method_array[$shipping_method->id] = esc_attr( $shipping_method->get_method_title() ); 
									}
								}
							?>
							 <?php if( !empty( $shipping_method_array ) && apply_filters( 'wcfm_orders_manage_shipping', true ) ) { ?>
									
									<div class="wcfm-container">
										<div id="wcfm_on_shipping_expander" class="wcfm-content">
											<?php
											$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_orders_manage_fields_shipping', array(  
																																																					"wcfm_om_shipping_method" => array( 'label' => __( 'Shipping Method / Extra Charge', 'wc-frontend-manager-ultimate' ), 'type' => 'select', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'options' => $shipping_method_array, 'value' => '' ),
																																																					"wcfm_om_shipping_cost"  => array( 'label' => __( 'Cost', 'wc-frontend-manager-ultimate' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '' ),
																																																					"wcfm_om_shipping_quantity"  => array( 'label' => __( 'Quantity', 'wc-frontend-manager-ultimate' ), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '' ),
																																																					"wcfm_om_shipping_title"  => array( 'label' => __( 'Title', 'wc-frontend-manager-ultimate' ), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => '' )
																																																								
																																																)) );
											?>
											<?php do_action( 'wcfm_orders_manage_after_shipping' ); ?>
											
										</div>
									</div>
								<?php } ?>

								<button type="button" name="add_shipping_method_btn" value="submit" id="add_shipping_method_btn" class="wcfm_popup_button">Submit shipping</button>

								<div class="wcfm_clearfix"></div>

							</form>
					</div>

		</div>


		
		<div class="wcfm-clearfix"></div><br />
		<!-- collapsible -->
		<div class="page_collapsible orders_details_items" id="wcfm_orders_items_options"><?php _e('Order Items', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container">

			<div id="orders_details_items_expander" class="wcfm-content">


						
				<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
					<thead>
						<tr>
							<th class="item-thumb no_mob" data-sort="string-ins"></th>
							<th class="item sortable" data-sort="string-ins"><?php _e( 'Item', 'wc-frontend-manager' ); ?></th>
							<?php do_action( 'woocommerce_admin_order_item_headers', $order ); ?>
							<th class="item_cost sortable no_mob" data-sort="float"><?php _e( 'Cost', 'wc-frontend-manager' ); ?></th>
							<th class="item_quantity wcfm_item_qty_heading sortable" data-sort="int"><?php _e( 'Qty', 'wc-frontend-manager' ); ?></th>
							<?php if( $is_wcfm_order_details_line_total_head = apply_filters( 'wcfm_order_details_line_total_head', true ) ) { ?>
								<th class="line_cost sortable" data-sort="float"><?php _e( 'Total', 'wc-frontend-manager' ); ?></th>
							<?php } ?>
							<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
								<?php
									if ( empty( $legacy_order ) && ! empty( $order_taxes ) ) :
										foreach ( $order_taxes as $tax_id => $tax_item ) :
											$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
											$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'wc-frontend-manager' );
											$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'wc-frontend-manager' );
											$column_tip     = $tax_item['name'] . ' (' . $tax_class_name . ')';
											?>
											<th class="line_tax text_tip no_ipad no_mob" data-tip="<?php echo esc_attr( $column_tip ); ?>">
												<?php echo esc_attr( $column_label ); ?>
												<input type="hidden" class="order-tax-id" name="order_taxes[<?php echo $tax_id; ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
												<a class="delete-order-tax" href="#" data-rate_id="<?php echo $tax_id; ?>"></a>
											</th>
											<?php
										endforeach;
									endif;
								?>
							<?php } ?>
							<?php do_action( 'wcfm_order_details_after_line_total_head', $order ); ?>
						</tr>
					</thead>
					<tbody id="order_line_items">
					<?php
						foreach ( $line_items as $item_id => $item ) {
							$_product  = $item->get_product();
							$product_link = '';
							do_action( 'woocommerce_before_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
							
							if( apply_filters( 'wcfm_is_allow_edit_products', true ) && apply_filters( 'wcfm_is_allow_edit_specific_products', true, $item->get_product_id() ) && apply_filters( 'wcfm_is_allow_order_details_product_permalink', true ) ) {
								$product_link  = $_product ? get_wcfm_edit_product_url( $item->get_product_id(), $_product ) : '';
							} else {
								if( apply_filters( 'wcfm_is_allow_show_product_permalink', true ) && apply_filters( 'wcfm_is_allow_order_details_product_permalink', true ) ) {
									$product_link  = $_product ? get_permalink( $item->get_product_id() ) : '';
								}
							}
							$thumbnail     = $_product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
							$tax_data = $item->get_taxes();
							?>
							<tr class="item <?php echo apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item, $order ); ?>" data-order_item_id="<?php echo $item_id; ?>">
								<td class="thumb no_mob">
									<?php
										echo '<div class="wc-order-item-thumbnail no_ipad">' . wp_kses_post( $thumbnail ) . '</div>';
									?>
								</td>
								<td class="name" data-sort-value="<?php echo esc_attr( $item->get_name() ); ?>">
									<?php
										echo $product_link ? '<a href="' . esc_url( $product_link ) . '" class="wc-order-item-name">' .  esc_html( apply_filters( 'wcfm_order_item_name', $item->get_name(), $item ) ) . '</a>' : '<div class="class="wc-order-item-name"">' . esc_html( apply_filters( 'wcfm_order_item_name', $item->get_name(), $item ) ) . '</div>';
							
										if ( $_product && $_product->get_sku() ) {
											echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'wc-frontend-manager' ) . '</strong> ' . esc_html( $_product->get_sku() ) . '</div>';
										}
							
										if ( ! empty( $item->get_variation_id() ) ) {
											echo '<div class="wc-order-item-variation"><strong>' . __( 'Variation ID:', 'wc-frontend-manager' ) . '</strong> ';
											if ( ! empty( $item->get_variation_id() ) && 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
												echo esc_html( $item->get_variation_id() );
											} elseif ( ! empty( $item->get_variation_id() ) ) {
												echo esc_html( $item->get_variation_id() ) . ' (' . __( 'No longer exists', 'wc-frontend-manager' ) . ')';
											}
											echo '</div>';
										}
									?>
							
									<div class="view">
									  <?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, $_product ) ?>
									  <?php do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false ); ?>
										<?php wc_display_item_meta( $item ); ?>
										<?php 
										//if( !class_exists( 'WC_Deposits_Order_Item_Manager' ) || ( class_exists( 'WC_Deposits_Order_Item_Manager' ) && !WC_Deposits_Order_Item_Manager::is_deposit( $item ) ) ) {
											do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false ); 
										//}
										?>
										<?php 
										if( !class_exists( 'WC_Deposits_Order_Item_Manager' ) || ( class_exists( 'WC_Deposits_Order_Item_Manager' ) && !WC_Deposits_Order_Item_Manager::is_deposit( $item ) && empty( $item['original_order_id'] ) ) ) {
											do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $_product );
										}
										
										do_action( 'wcfm_after_order_itemmeta', $item_id, $item, $_product, $order );
										?>
									</div>
								</td>
							
								<?php do_action( 'woocommerce_admin_order_item_values', $_product, $item, absint( $item_id ) ); ?>
							
								<td class="item_cost no_mob" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, false, true ) ); ?>">
									<div class="view">
										<?php
											if ( $item->get_total() ) {
												echo wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $order->get_currency() ) );
							
												if ( $item->get_subtotal() != $item->get_total() ) {
													echo '<span class="wc-order-item-discount">-' . wc_price( wc_format_decimal( $order->get_item_subtotal( $item, false, false ) - $order->get_item_total( $item, false, false ), '' ), array( 'currency' => $order->get_currency() ) ) . '</span>';
												}
											}
										?>
									</div>
								</td>
								<td class="wcfm_item_qty" width="1%">
									<div class="view">
										<?php
											echo '<small class="times">&times;</small> ' . ( $item->get_quantity() ? esc_html( $item->get_quantity() ) : '1' );
							
											if ( $refunded_qty = $order->get_qty_refunded_for_item( $item_id ) ) {
												echo '<small class="refunded">-' . ( $refunded_qty * -1 ) . $item_id . '</small>';
											}
										?>
									</div>
								</td>
								
								<?php if( $is_wcfm_order_details_line_total = apply_filters( 'wcfm_order_details_line_total', true ) ) { ?>
									<td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( ( $item->get_total() ) ? $item->get_total() : '' ); ?>">
										<div class="view">
											<?php
												if ( $item->get_total() ) {
													echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );
												}
								
												if ( $item->get_subtotal() !== $item->get_total() ) {
													echo '<span class="wc-order-item-discount">' . sprintf( esc_html__( '%s discount', 'woocommerce' ), wc_price( wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ), array( 'currency' => $order->get_currency() ) ) ) . '</span>';
												}
								
												if ( $refunded = $order->get_total_refunded_for_item( $item_id ) ) {
													echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
												}
											?>
										</div>
									</td>
								<?php } ?>
							
								<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
									<?php
									if ( wc_tax_enabled() ) {
											if ( ! empty( $tax_data ) ) {
												foreach ( $order_taxes as $tax_item ) {
													$tax_item_id       = $tax_item['rate_id'];
													$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : 0;
													$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : 0;
													?>
													<td class="line_tax no_ipad no_mob" width="1%">
														<div class="view">
															<?php
																if ( '' != $tax_item_total ) {
																	echo wc_price( wc_round_tax_total( $tax_item_subtotal ), array( 'currency' => $order->get_currency() ) );
																} else {
																	echo '&ndash;';
																}
									
																if ( $tax_item_subtotal !== $tax_item_total ) {
																	echo '<span class="wc-order-item-discount">-' . wc_price( wc_round_tax_total( $tax_item_subtotal - $tax_item_total ), array( 'currency' => $order->get_currency() ) ) . '</span>';
																}
									
																if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id ) ) {
																	echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
																}
															?>
														</div>
													</td>
													<?php
												}
											}
										}
									?>
								<?php } ?>
								
								<?php do_action( 'wcfm_after_order_details_line_total', $item, $order ); ?>

								<td class="wc-order-edit-line-item">
									<form method="post" id="remove__products_from_order_form_<?php echo $item_id; ?>">
										<input type="hidden" name="remove__products_from_order" value="yess">
										<input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
										<input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
										<div class="wc-order-edit-line-item-actions">
											<a class="delete-order-item tips" href="javascript:void(0)" data-itemid="<?php echo $item_id; ?>" data-tip="<?php esc_attr_e( 'Delete item', 'woocommerce' ); ?>"><svg class="svg-icon svg-icon-cross-custom" viewBox="0 0 20 20">
							<path d="M10.185,1.417c-4.741,0-8.583,3.842-8.583,8.583c0,4.74,3.842,8.582,8.583,8.582S18.768,14.74,18.768,10C18.768,5.259,14.926,1.417,10.185,1.417 M10.185,17.68c-4.235,0-7.679-3.445-7.679-7.68c0-4.235,3.444-7.679,7.679-7.679S17.864,5.765,17.864,10C17.864,14.234,14.42,17.68,10.185,17.68 M10.824,10l2.842-2.844c0.178-0.176,0.178-0.46,0-0.637c-0.177-0.178-0.461-0.178-0.637,0l-2.844,2.841L7.341,6.52c-0.176-0.178-0.46-0.178-0.637,0c-0.178,0.176-0.178,0.461,0,0.637L9.546,10l-2.841,2.844c-0.178,0.176-0.178,0.461,0,0.637c0.178,0.178,0.459,0.178,0.637,0l2.844-2.841l2.844,2.841c0.178,0.178,0.459,0.178,0.637,0c0.178-0.176,0.178-0.461,0-0.637L10.824,10z"></path>
						</svg></a>
										</div>
									</form>
								</td>


							</tr>

	
							<?php
			
							do_action( 'woocommerce_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );


						}
						do_action( 'woocommerce_admin_order_items_after_line_items', $order->get_id() );

					?>
					</tbody>
					
					<?php if( apply_filters( 'wcfm_order_details_shipping_line_item', true ) && !empty( $line_items_shipping ) ) { ?>
					<tbody id="order_shipping_line_items">
						<tr class="shipping">
							<td class="name" colspan="5"><h2><?php _e( 'Shipping Item(s)', 'wc-frontend-manager' ); ?></h2></td>
							<?php if ( wc_tax_enabled() ) : $total_taxes = count( $order_taxes ); ?>
								<?php for ( $i = 0;  $i < $total_taxes; $i++ ) : ?>
									<td class="line_tax no_ipad no_mob" width="1%"></td>
								<?php endfor; ?>
							<?php endif; ?>
							<?php do_action( 'wcfm_after_order_details_refund_total', '', $order ); ?>
						</tr>
						<?php
						$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
						foreach ( $line_items_shipping as $item_id => $item ) {
							?>
							<tr class="shipping <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_item_id="<?php echo $item_id; ?>">
								<td class="thumb no_ipad no_mob"><span class="wcfmfa fa-truck"></span></td>
							
								<td class="name" colspan="<?php echo wp_is_mobile() ? 2 : 3; ?>">
									<div class="view wcfm_order_details_shipping_method_name">
										<?php echo ! empty( $item->get_name() ) ? wc_clean( $item->get_name() ) : __( 'Shipping', 'wc-frontend-manager' ); ?>
									</div>
							
									<div class="view">
									  <?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, null ); ?>
										<?php wc_display_item_meta( $item ); ?>
										<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, null ) ?>
									</div>
								</td>
							
								<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>
							
								<td class="line_cost" width="1%">
									<div class="view">
										<?php
											echo ( isset( $item['cost'] ) ) ? wc_price( wc_round_tax_total( $item['cost'] ), array( 'currency' => $order->get_currency() ) ) : '';
							
											if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'shipping' ) ) {
												echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
											}
										?>
									</div>
								</td>
							
								<?php if( apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
									<?php
										if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
											foreach ( $order_taxes as $tax_item ) {
												$tax_item_id    = $tax_item->get_rate_id();
												$tax_item_total = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
												?>
													<td class="line_tax no_ipad no_mob" width="1%">
														<div class="view">
															<?php
																echo ( '' != $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
								
																if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'shipping' ) ) {
																	echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
																}
															?>
														</div>
													</td>
								
												<?php
											}
										}
									?>
								<?php } ?>
								
								<?php do_action( 'wcfm_after_order_details_shipping_total', $item, $order ); ?>


								<td class="wc-order-edit-line-item">
									<form method="post" id="remove__shipping_from_order_<?php echo $item_id; ?>">
										<input type="hidden" name="remove__shipping_from_order" value="yess">
										<input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
										<input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
										<div class="wc-order-edit-line-item-actions">
											<a class="remove__shipping_from_order tips" href="javascript:void(0)" data-itemid="<?php echo $item_id; ?>" data-tip="<?php esc_attr_e( 'Delete item', 'woocommerce' ); ?>"><svg class="svg-icon svg-icon-cross-custom" viewBox="0 0 20 20">
							<path d="M10.185,1.417c-4.741,0-8.583,3.842-8.583,8.583c0,4.74,3.842,8.582,8.583,8.582S18.768,14.74,18.768,10C18.768,5.259,14.926,1.417,10.185,1.417 M10.185,17.68c-4.235,0-7.679-3.445-7.679-7.68c0-4.235,3.444-7.679,7.679-7.679S17.864,5.765,17.864,10C17.864,14.234,14.42,17.68,10.185,17.68 M10.824,10l2.842-2.844c0.178-0.176,0.178-0.46,0-0.637c-0.177-0.178-0.461-0.178-0.637,0l-2.844,2.841L7.341,6.52c-0.176-0.178-0.46-0.178-0.637,0c-0.178,0.176-0.178,0.461,0,0.637L9.546,10l-2.841,2.844c-0.178,0.176-0.178,0.461,0,0.637c0.178,0.178,0.459,0.178,0.637,0l2.844-2.841l2.844,2.841c0.178,0.178,0.459,0.178,0.637,0c0.178-0.176,0.178-0.461,0-0.637L10.824,10z"></path>
						</svg></a>
										</div>
									</form>
								</td>
							
							</tr>
							<?php
						}
						do_action( 'woocommerce_admin_order_items_after_shipping', $order->get_id() );
					?>
					</tbody>
					<?php } ?>
					
					<?php if( apply_filters( 'wcfm_order_details_fee_line_item', true ) && !empty( $line_items_fee ) ) { ?>
					<tbody id="order_fee_line_items">
						<tr class="shippin">
							<td class="name" colspan="5"><h2><?php _e( 'Fee Item(s)', 'wc-frontend-manager' ); ?></h2></td>
							<?php if ( wc_tax_enabled() ) : $total_taxes = count( $order_taxes ); ?>
								<?php for ( $i = 0;  $i < $total_taxes; $i++ ) : ?>
									<td class="line_tax no_ipad no_mob" width="1%"></td>
								<?php endfor; ?>
							<?php endif; ?>
							<?php do_action( 'wcfm_after_order_details_refund_total', '', $order ); ?>
						</tr>
						<?php
						foreach ( $line_items_fee as $item_id => $item ) {
							?>
							<tr class="fee <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_item_id="<?php echo $item_id; ?>">
								<td class="thumb no_ipad no_mob"><span class="wcfmfa fa-plus-circle"></span></td>
							
								<td class="name" colspan="3">
									<div class="view">
										<?php echo ! empty( $item->get_name() ) ? esc_html( $item->get_name() ) : __( 'Fee', 'wc-frontend-manager' ); ?>
									</div>
								</td>
							
								<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>
							
								<td class="line_cost" width="1%">
									<div class="view">
										<?php
											echo ( $item->get_total() ) ? wc_price( wc_round_tax_total( $item->get_total() ), array( 'currency' => $order->get_currency() ) ) : '';
							
											if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'fee' ) ) {
												echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
											}
										?>
									</div>
								</td>
							
								<?php if( apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
									<?php
										if ( empty( $legacy_order ) && wc_tax_enabled() ) :
											$line_tax_data = isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '';
											$tax_data      = maybe_unserialize( $line_tax_data );
								
											foreach ( $order_taxes as $tax_item ) :
												$tax_item_id       = $tax_item['rate_id'];
												$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
												?>
													<td class="line_tax no_ipad no_mob" width="1%">
														<div class="view">
															<?php
																echo ( '' != $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
								
																if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'fee' ) ) {
																	echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
																}
															?>
														</div>
													</td>
								
												<?php
											endforeach;
										endif;
									?>
								<?php } ?>
							
							</tr>
							<?php
						}
						do_action( 'woocommerce_admin_order_items_after_fees', $order->get_id() );
					?>
					</tbody>
					<?php } ?>
					
					<?php if( apply_filters( 'wcfm_order_details_refund_line_item', true ) && !empty( $order->get_refunds() ) ) { ?>
					<tbody id="order_refunds">
					  <tr class="shippin">
							<td class="name" colspan="5"><h2 style="color: #a00;"><?php _e( 'Refund(s)', 'wc-frontend-manager' ); ?></h2></td>
							<?php if ( wc_tax_enabled() ) : $total_taxes = count( $order_taxes ); ?>
								<?php for ( $i = 0;  $i < $total_taxes; $i++ ) : ?>
									<td class="line_tax no_ipad no_mob" width="1%"></td>
								<?php endfor; ?>
							<?php endif; ?>
							<?php do_action( 'wcfm_after_order_details_refund_total', '', $order ); ?>
						</tr>
						<?php
						if ( $refunds = $order->get_refunds() ) {
							$cur_vendor_id   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
							foreach ( $refunds as $refund ) {
								$who_refunded = new WP_User( $refund->get_refunded_by() );
								if( wcfm_is_vendor() && ( !$who_refunded || ( $who_refunded && ( $who_refunded->ID != $cur_vendor_id ) ) ) ) continue;
								?>
								<tr class="refund <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_refund_id="<?php echo $refund->get_id(); ?>">
									<td class="thumb no_ipad no_mob"><span class="wcicon-status-refunded"></span></td>
								
									<td class="name" colspan="3">
										<?php
											/* translators: 1: refund id 2: date */
											printf( __( 'Refund #%1$s - %2$s', 'woocommerce' ), $refund->get_id(), wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) );
								
											if ( $who_refunded->exists() ) {
												echo '<div class="wcfm_clearfix"></div>' . esc_attr_x( 'by', 'Ex: Refund - $date >by< $username', 'woocommerce' ) . ' ' . '<abbr class="refund_by" title="' . sprintf( esc_attr__( 'ID: %d', 'woocommerce' ), absint( $who_refunded->ID ) ) . '">' . esc_attr( $who_refunded->display_name ) . '</abbr>' ;
											}
										?>
										<?php if ( $refund->get_reason() ) : ?>
											<div class="wcfm_clearfix"></div>
											<p class="description"><?php echo esc_html( $refund->get_reason() ); ?></p>
										<?php endif; ?>
									</td>
								
									<?php do_action( 'woocommerce_admin_order_item_values', null, $refund, $refund->get_id() ); ?>
								
									<td class="line_cost refunded-total" width="1%">
										<div class="view">
											<?php echo wc_price( '-' . $refund->get_amount() ); ?>
										</div>
									</td>
								
									<?php if ( wc_tax_enabled() ) : $total_taxes = count( $order_taxes ); ?>
										<?php for ( $i = 0;  $i < $total_taxes; $i++ ) : ?>
											<td class="line_tax no_ipad no_mob" width="1%"></td>
										<?php endfor; ?>
									<?php endif; ?>
									
									<?php do_action( 'wcfm_after_order_details_refund_total', $item, $order ); ?>
							 </tr>
									<?php
							}
							do_action( 'woocommerce_admin_order_items_after_refunds', $order->get_id() );
						}
					?>
					</tbody>
					<?php } ?>
				</table>
				
				<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
					<?php //if( apply_filters( 'wcfm_order_details_coupon_line_item', true ) ) { ?>
						<?php
							$coupons = $order->get_items( array( 'coupon' ) );
							if ( $coupons ) {
								?>
								<div class="wc-used-coupons">
									<ul class="wc_coupon_list"><?php
										echo '<li><strong>' . __( 'Coupon(s) Used', 'wc-frontend-manager' ) . '</strong></li>';
										foreach ( $coupons as $item_id => $item ) {
											$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item->get_name() ) );
					
											if( apply_filters( 'wcfm_is_allow_show_only_vendor_coupon_to_vendors', true ) && wcfm_is_vendor() && ( !wcfm_get_vendor_id_by_post( $post_id ) || ( wcfm_get_vendor_id_by_post( $post_id ) && ($WCFMmp->vendor_id != wcfm_get_vendor_id_by_post( $post_id ) ) ) ) ) continue;
											
											$link = $post_id ? get_wcfm_coupons_manage_url( $post_id ) : get_wcfm_coupons_url();
											
											if( !apply_filters( 'wcfm_is_allow_manage_coupons', true ) || wcfm_is_vendor() ) { $link = '#'; }
					
											echo '<li class="code"><a target="_blank" href="' . esc_url( $link ) . '" class="img_tip" data-tip="' . esc_attr( wc_price( $item['discount_amount'], array( 'currency' => $order->get_currency() ) ) ) . '"><span>' . esc_html( $item->get_name() ). '</span></a></li>';
										}
									?></ul>
								</div>
								<?php
							}
						?>
					<?php //} ?>
					
					<table class="wc-order-totals">
						<?php if( apply_filters( 'wcfm_order_details_coupon_line_item', true ) ) { ?>
							<tr>
								<th class="label"><span class="wcfmfa fa-question no_mob img_tip" data-tip="<?php _e( 'This is the total discount. Discounts are defined per line item.', 'wc-frontend-manager' ) ; ?>"></span> <?php _e( 'Discount', 'wc-frontend-manager' ); ?>:</th>
								<td width="1%"></td>
								<td class="total">
									<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_currency() ) ); ?>
								</td>
							</tr>
						<?php } ?>
				
						<?php do_action( 'woocommerce_admin_order_totals_after_discount', $order->get_id() ); ?>
				
						<?php if( apply_filters( 'wcfm_order_details_shipping_line_item', true ) && apply_filters( 'wcfm_order_details_shipping_total', true ) && $order->get_formatted_shipping_address() ) { ?>
							<tr>
								<th class="label"><span class="wcfmfa fa-question no_mob img_tip" data-tip="<?php _e( 'This is the shipping and handling total costs for the order.', 'wc-frontend-manager' ) ; ?>"></span> <?php _e( 'Shipping', 'wc-frontend-manager' ); ?>:</th>
								<td width="1%"></td>
								<td class="total"><?php
									if ( ( $refunded = $order->get_total_shipping_refunded() ) > 0 ) {
										echo '<del>' . strip_tags( wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) ) ) . '</del> <ins>' . wc_price( $order->get_total_shipping() - $refunded, array( 'currency' => $order->get_currency() ) ) . '</ins>';
									} else {
										echo wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) );
									}
									
								?></td>
							</tr>
						<?php } ?>
				
						<?php do_action( 'woocommerce_admin_order_totals_after_shipping', $order->get_id() ); ?>
				
						<?php if( apply_filters( 'wcfm_order_details_tax_total', true ) ) { ?>
							<?php if ( wc_tax_enabled() ) : ?>
								<?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
									<tr>
										<th class="label"><?php echo $tax->label; ?>:</th>
										<td width="1%"></td>
										<td class="total"><?php
											if ( ( $refunded = $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) > 0 ) {
												echo '<del>' . strip_tags( $tax->formatted_amount ) . '</del> <ins>' . wc_price( WC_Tax::round( $tax->amount, wc_get_price_decimals() ) - WC_Tax::round( $refunded, wc_get_price_decimals() ), array( 'currency' => $order->get_currency() ) ) . '</ins>';
											} else {
												echo $tax->formatted_amount;
											}
										?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php } ?>
				
						<?php do_action( 'woocommerce_admin_order_totals_after_tax', $order->get_id() ); ?>
				
						<?php if( $is_wcfm_order_details_total = apply_filters( 'wcfm_order_details_total', true ) ) { ?>
						<tr>
							<th class="label"><?php _e( 'Order Total', 'wc-frontend-manager' ); ?>:</th>
							<td width="1%"></td>
							<td class="total">
								<div class="view"><?php echo $order->get_formatted_order_total(); ?></div>
							</td>
						</tr>
						<?php } ?>

						<tr>
							<th class="label"><?php _e( 'Paid Amount', 'wc-frontend-manager' ); ?>:</th>
							<td width="1%"></td>
							<td class="total">
								<div class="view"><?php echo  wc_price( end($order_edit_array['payment_paid']), array( 'currency' => $order->get_currency() ) );
								 ?></div>
							</td>
						</tr>

						<tr>
							<th class="label"><?php _e( 'Remaining Amount', 'wc-frontend-manager' ); ?>:</th>
							<td width="1%"></td>
							<td class="total">
								<div class="view"><?php 
									$remaining_amount =(float) $order->get_total() - (float) end($order_edit_array['payment_paid']); 
									echo  wc_price( $remaining_amount, array( 'currency' => $order->get_currency() ) );
									 ?>
								</div>
							</td>
						</tr>

				
						<?php if( apply_filters( 'wcfm_order_details_refund_line_item', true ) && apply_filters( 'wcfm_order_details_refund_total', true ) ) { ?>
							<?php if ( $order->get_total_refunded() ) : ?>
								<tr>
									<th class="label refunded-total"><?php _e( 'Refunded', 'wc-frontend-manager' ); ?>:</th>
									<td width="1%"></td>
									<td class="total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ); ?></td>
								</tr>
							<?php endif; ?>
						<?php } ?>
						
						<?php if( ( $marketplece = wcfm_is_marketplace() ) && !wcfm_is_vendor() && apply_filters( 'wcfm_is_allow_view_commission', true ) && apply_filters( 'wcfm_is_allow_commission_manage', true ) && !in_array( $current_order_status, array( 'failed', 'cancelled', 'refunded', 'request', 'proposal', 'proposal-sent', 'proposal-expired', 'proposal-rejected', 'proposal-canceled', 'proposal-accepted' ) ) ) { ?>
						<tr>
							<th class="label"><?php _e( 'Vendor(s) Earning', 'wc-frontend-manager' ); ?>:</th>
							<td width="1%"></td>
							<td class="total">
								<div class="view">
								  <?php 
								  $commission = $WCFM->wcfm_vendor_support->wcfm_get_commission_by_order( $order->get_id() );
									if( $commission ) {
										echo  wc_price( $commission, array( 'currency' => $order->get_currency() ) );
									} else {
										echo  __( 'N/A', 'wc-frontend-manager' );
									}
								  ?>
								 </div>
							</td>
						</tr>
						<tr>
							<th class="label"><?php _e( 'Admin Fee', 'wc-frontend-manager' ); ?>:</th>
							<td width="1%"></td>
							<td class="total">
								<div class="view">
								  <?php 
									if( $commission ) {
										$gross_sales  = (float) $order->get_total();
										$total_refund = (float) $order->get_total_refunded();
										//if( $admin_fee_mode || ( $marketplece == 'dokan' ) ) {
											$commission = $gross_sales - $total_refund - $commission;
										//}
										echo  wc_price( $commission, array( 'currency' => $order->get_currency() ) );
									} else {
										echo  __( 'N/A', 'wc-frontend-manager' );
									}
								  ?>
								 </div>
							</td>
						</tr>
						<?php } ?>
						
						<?php do_action( 'wcfm_order_totals_after_total', $order->get_id() ); ?>
				
						<?php 
						//do_action( 'woocommerce_admin_order_totals_after_refunded', $order->get_id() ); 
						?>
				
					</table>
					<div class="wcfm-clearfix"></div>
				</div>
				
				<?php do_action( 'after_wcfm_orders_details_items', $order_id, $order, $line_items ); ?>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<!-- end collapsible -->
		<?php do_action( 'end_wcfm_orders_details', $order_id ); ?>
	</div>
</div>




<?php
do_action( 'after_wcfm_orders_details', $order_id );
?>



