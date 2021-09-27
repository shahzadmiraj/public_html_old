<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path( __FILE__ ).'header/plugin-header.php');

$mmqw_admin_object = new MMQW_Min_Max_Quantity_For_WooCommerce_Admin( '', '' );
$mmqw_object = new Min_Max_Quantity_For_WooCommerce( '', '' );

$get_action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);
/*
 * save all posted data method define in class-min-max-quantity-for-woocommerce-admin
 */
if (isset($_POST['submitFee']) && !empty($_POST['submitFee'])) {

    $post_wpnonce = filter_input(INPUT_POST,'mmqw_conditions_save',FILTER_SANITIZE_STRING);
    $post_retrieved_nonce = isset($post_wpnonce) ? sanitize_text_field(wp_unslash($post_wpnonce)) : '';

    if (!wp_verify_nonce($post_retrieved_nonce, 'mmqw_save_action')) {
        die('Failed security check');
    } else {
        $mmqw_admin_object->mmqw_rules_conditions_save();
    }
}

/*
 * edit all posted data method define in class-min-max-quantity-for-woocommerce-admin
 */
if (isset($get_action) && 'edit' === $get_action) {

    $get_wpnonce = filter_input(INPUT_GET,'_wpnonce',FILTER_SANITIZE_STRING);
    $get_retrieved_nonce = isset($get_wpnonce) ? sanitize_text_field(wp_unslash($get_wpnonce)) : '';

    if (!wp_verify_nonce($get_retrieved_nonce, 'mmqwnonce')) {
        die('Failed security check');
    }

    $get_id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);

    $get_post_id = isset($get_id) ? sanitize_text_field(wp_unslash($get_id)) : '';
    $sm_status = get_post_status($get_post_id);
    $ap_rule_status = get_post_meta($get_post_id, 'ap_rule_status', true);

    $sm_title = __(get_the_title($get_post_id), 'min-and-max-quantity-for-woocommerce');
    $fee_settings_unique_shipping_title = get_post_meta($get_post_id, 'fee_settings_unique_shipping_title', true);
    $sm_cost = get_post_meta($get_post_id, 'sm_product_cost', true);
    $getFeesPerQtyFlag = get_post_meta($get_post_id, 'sm_fee_chk_qty_price', true);
    $getFeesPerQty = get_post_meta($get_post_id, 'sm_fee_per_qty', true);
    $extraProductCost = get_post_meta($get_post_id, 'sm_extra_product_cost', true);
    $sm_tooltip_desc = get_post_meta($get_post_id, 'sm_tooltip_desc', true);
    $sm_is_taxable = get_post_meta($get_post_id, 'sm_select_taxable', true);
    $sm_estimation_delivery = get_post_meta($get_post_id, 'sm_estimation_delivery', true);
    $sm_start_date = get_post_meta($get_post_id, 'sm_start_date', true);
    $sm_end_date = get_post_meta($get_post_id, 'sm_end_date', true);
    $sm_time_from = get_post_meta($get_post_id, 'sm_time_from', true);
    $sm_time_to = get_post_meta($get_post_id, 'sm_time_to', true);
    $sm_select_day_of_week  = get_post_meta( $get_post_id, 'sm_select_day_of_week', true );
    if ( is_serialized( $sm_select_day_of_week ) ) {
        $sm_select_day_of_week = maybe_unserialize( $sm_select_day_of_week );
    } else {
        $sm_select_day_of_week = $sm_select_day_of_week;
    }

    $sm_extra_cost           = get_post_meta( $get_post_id, 'sm_extra_cost', true );
    if ( is_serialized( $sm_extra_cost ) ) {
        $sm_extra_cost = maybe_unserialize( $sm_extra_cost );
    } else {
        $sm_extra_cost = $sm_extra_cost;
    }
    $sm_extra_cost_calc_type = get_post_meta($get_post_id, 'sm_extra_cost_calculation_type', true);
    $sm_metabox = get_post_meta($get_post_id, 'sm_metabox', true);
    if (is_serialized($sm_metabox)) {
        $sm_metabox = maybe_unserialize($sm_metabox);
    } else {
        $sm_metabox = $sm_metabox;
    }
    /*Advance rule status*/
    $cost_on_product_status             = get_post_meta( $get_post_id, 'cost_on_product_status', true );
    $cost_on_product_weight_status      = get_post_meta( $get_post_id, 'cost_on_product_weight_status', true );
    $cost_on_product_variation_status    = get_post_meta( $get_post_id, 'cost_on_product_variation_status', true );

    $cost_on_category_status            = get_post_meta( $get_post_id, 'cost_on_category_status', true );
    $cost_on_category_weight_status     = get_post_meta( $get_post_id, 'cost_on_category_weight_status', true );
    $cost_on_country_status   = get_post_meta( $get_post_id, 'cost_on_country_status', true );

    $cost_on_total_cart_qty_status      = get_post_meta( $get_post_id, 'cost_on_total_cart_qty_status', true );
    $cost_on_total_cart_weight_status   = get_post_meta( $get_post_id, 'cost_on_total_cart_weight_status', true );
    $cost_on_total_cart_subtotal_status = get_post_meta( $get_post_id, 'cost_on_total_cart_subtotal_status', true );
    $cost_on_shipping_class_subtotal_status = get_post_meta( $get_post_id, 'cost_on_shipping_class_subtotal_status', true );
    /*Advance rule status*/

    //APM variable initialize on edit action
    $sm_metabox_ap_product = get_post_meta($get_post_id, 'sm_metabox_ap_product', true);
    if (is_serialized($sm_metabox_ap_product)) {
        $sm_metabox_ap_product = maybe_unserialize($sm_metabox_ap_product);
    } else {
        $sm_metabox_ap_product = $sm_metabox_ap_product;
    }
    $sm_metabox_ap_product_variation = get_post_meta( $get_post_id, 'sm_metabox_ap_product_variation', true );
    if ( is_serialized( $sm_metabox_ap_product_variation ) ) {
        $sm_metabox_ap_product_variation = maybe_unserialize( $sm_metabox_ap_product_variation );
    } else {
        $sm_metabox_ap_product_variation = $sm_metabox_ap_product_variation;
    }
    $sm_metabox_ap_category = get_post_meta($get_post_id, 'sm_metabox_ap_category', true);
    if (is_serialized($sm_metabox_ap_category)) {
        $sm_metabox_ap_category = maybe_unserialize($sm_metabox_ap_category);
    } else {
        $sm_metabox_ap_category = $sm_metabox_ap_category;
    }
    $sm_metabox_ap_country = get_post_meta( $get_post_id, 'sm_metabox_ap_country', true );
    if ( is_serialized( $sm_metabox_ap_country ) ) {
        $sm_metabox_ap_country = maybe_unserialize( $sm_metabox_ap_country );
    } else {
        $sm_metabox_ap_country = $sm_metabox_ap_country;
    }
    $sm_metabox_ap_total_cart_qty = get_post_meta($get_post_id, 'sm_metabox_ap_total_cart_qty', true);
    if (is_serialized($sm_metabox_ap_total_cart_qty)) {
        $sm_metabox_ap_total_cart_qty = maybe_unserialize($sm_metabox_ap_total_cart_qty);
    } else {
        $sm_metabox_ap_total_cart_qty = $sm_metabox_ap_total_cart_qty;
    }
    $sm_metabox_ap_product_weight = get_post_meta($get_post_id, 'sm_metabox_ap_product_weight', true);
    if (is_serialized($sm_metabox_ap_product_weight)) {
        $sm_metabox_ap_product_weight = maybe_unserialize($sm_metabox_ap_product_weight);
    } else {
        $sm_metabox_ap_product_weight = $sm_metabox_ap_product_weight;
    }
    $sm_metabox_ap_category_weight = get_post_meta($get_post_id, 'sm_metabox_ap_category_weight', true);
    if (is_serialized($sm_metabox_ap_category_weight)) {
        $sm_metabox_ap_category_weight = maybe_unserialize($sm_metabox_ap_category_weight);
    } else {
        $sm_metabox_ap_category_weight = $sm_metabox_ap_category_weight;
    }
    $sm_metabox_ap_total_cart_weight = get_post_meta($get_post_id, 'sm_metabox_ap_total_cart_weight', true);
    if (is_serialized($sm_metabox_ap_total_cart_weight)) {
        $sm_metabox_ap_total_cart_weight = maybe_unserialize($sm_metabox_ap_total_cart_weight);
    } else {
        $sm_metabox_ap_total_cart_weight = $sm_metabox_ap_total_cart_weight;
    }

    $sm_metabox_ap_total_cart_subtotal = get_post_meta($get_post_id, 'sm_metabox_ap_total_cart_subtotal', true);
    if (is_serialized($sm_metabox_ap_total_cart_subtotal)) {
        $sm_metabox_ap_total_cart_subtotal = maybe_unserialize($sm_metabox_ap_total_cart_subtotal);
    } else {
        $sm_metabox_ap_total_cart_subtotal = $sm_metabox_ap_total_cart_subtotal;
    }

    $sm_metabox_ap_shipping_class_subtotal = get_post_meta($get_post_id, 'sm_metabox_ap_shipping_class_subtotal', true);
    if (is_serialized($sm_metabox_ap_shipping_class_subtotal)) {
        $sm_metabox_ap_shipping_class_subtotal = maybe_unserialize($sm_metabox_ap_shipping_class_subtotal);
    } else {
        $sm_metabox_ap_shipping_class_subtotal = $sm_metabox_ap_shipping_class_subtotal;
    }

    $cost_rule_match = get_post_meta( $get_post_id, 'cost_rule_match', true );

    if (!empty($cost_rule_match)) {
        if ( is_serialized( $cost_rule_match ) ) {
            $cost_rule_match = maybe_unserialize( $cost_rule_match );
        } else {
            $cost_rule_match = $cost_rule_match;
        }

        if (array_key_exists('general_rule_match', $cost_rule_match)) {
            $general_rule_match = $cost_rule_match['general_rule_match'];
        } else {
            $general_rule_match = 'any';
        }

        if (array_key_exists('advance_rule_match', $cost_rule_match)) {
            $advance_rule_match = $cost_rule_match['advance_rule_match'];
        } else {
            $advance_rule_match = 'any';
        }

        if (array_key_exists('cost_on_product_weight_rule_match', $cost_rule_match)) {
            $cost_on_product_weight_rule_match = $cost_rule_match['cost_on_product_weight_rule_match'];
        } else {
            $cost_on_product_weight_rule_match = 'any';
        }
        if (array_key_exists('cost_on_product_variation_rule_match', $cost_rule_match)) {
            $cost_on_product_variation_rule_match = $cost_rule_match['cost_on_product_variation_rule_match'];
        } else {
            $cost_on_product_variation_rule_match = 'any';
        }

        if (array_key_exists('cost_on_category_rule_match', $cost_rule_match)) {
            $cost_on_category_rule_match = $cost_rule_match['cost_on_category_rule_match'];
        } else {
            $cost_on_category_rule_match = 'any';
        }
        if (array_key_exists('cost_on_category_weight_rule_match', $cost_rule_match)) {
            $cost_on_category_weight_rule_match = $cost_rule_match['cost_on_category_weight_rule_match'];
        } else {
            $cost_on_category_weight_rule_match = 'any';
        }
        if (array_key_exists('cost_on_country_rule_match', $cost_rule_match)) {
            $cost_on_country_rule_match = $cost_rule_match['cost_on_country_rule_match'];
        } else {
            $cost_on_country_rule_match = 'any';
        }

        if (array_key_exists('cost_on_total_cart_qty_rule_match', $cost_rule_match)) {
            $cost_on_total_cart_qty_rule_match = $cost_rule_match['cost_on_total_cart_qty_rule_match'];
        } else {
            $cost_on_total_cart_qty_rule_match = 'any';
        }
        if (array_key_exists('cost_on_total_cart_weight_rule_match', $cost_rule_match)) {
            $cost_on_total_cart_weight_rule_match = $cost_rule_match['cost_on_total_cart_weight_rule_match'];
        } else {
            $cost_on_total_cart_weight_rule_match = 'any';
        }
        if (array_key_exists('cost_on_total_cart_subtotal_rule_match', $cost_rule_match)) {
            $cost_on_total_cart_subtotal_rule_match = $cost_rule_match['cost_on_total_cart_subtotal_rule_match'];
        } else {
            $cost_on_total_cart_subtotal_rule_match = 'any';
        }
        if (array_key_exists('cost_on_shipping_class_subtotal_rule_match', $cost_rule_match)) {
            $cost_on_shipping_class_subtotal_rule_match = $cost_rule_match['cost_on_shipping_class_subtotal_rule_match'];
        } else {
            $cost_on_shipping_class_subtotal_rule_match = 'any';
        }
    }
} else {
    $get_post_id = '';
    $sm_status = '';
    $ap_rule_status = '';
    $sm_title = '';
    $fee_settings_unique_shipping_title = '';
    $sm_cost = '';
    $getFeesPerQtyFlag = '';
    $getFeesPerQty = '';
    $extraProductCost = '';
    $sm_tooltip_desc = '';
    $sm_is_taxable = '';
    $sm_estimation_delivery = '';
    $sm_start_date = '';
    $sm_end_date = '';
    $sm_extra_cost = array();
    $sm_extra_cost_calc_type = '';
    $sm_metabox = array();
    $cost_on_product_status = '';
    $cost_on_category_status = '';
    $cost_on_total_cart_qty_status = '';
    $cost_on_product_weight_status = '';
    $cost_on_category_weight_status = '';
    $cost_on_total_cart_weight_status = '';
    $cost_on_total_cart_subtotal_status = '';
    $cost_on_shipping_class_subtotal_status = '';

    $sm_metabox_ap_product = array();
    $sm_metabox_ap_category = array();
    $sm_metabox_ap_total_cart_qty = array();
    $sm_metabox_ap_product_weight = array();
    $sm_metabox_ap_category_weight = array();
    $sm_metabox_ap_total_cart_weight = array();
    $sm_metabox_ap_total_cart_subtotal = array();
    $sm_metabox_ap_shipping_class_subtotal = array();
}

$sm_status = ((!empty($sm_status) && 'publish' === $sm_status) || empty($sm_status)) ? 'checked' : '';
$ap_rule_status = (!empty($ap_rule_status) && 'on' === $ap_rule_status && "" !== $ap_rule_status) ? 'checked' : '';
$cost_on_product_status             = ( ! empty( $cost_on_product_status ) && 'on' === $cost_on_product_status && "" !== $cost_on_product_status ) ? 'checked' : '';
$cost_on_product_weight_status      = ( ! empty( $cost_on_product_weight_status ) && 'on' === $cost_on_product_weight_status && "" !== $cost_on_product_weight_status ) ? 'checked' : '';
$cost_on_product_variation_status    = ( ! empty( $cost_on_product_variation_status ) && 'on' === $cost_on_product_variation_status && "" !== $cost_on_product_variation_status ) ? 'checked' : '';
$cost_on_category_status            = ( ! empty( $cost_on_category_status ) && 'on' === $cost_on_category_status && "" !== $cost_on_category_status ) ? 'checked' : '';
$cost_on_category_weight_status     = ( ! empty( $cost_on_category_weight_status ) && 'on' === $cost_on_category_weight_status && "" !== $cost_on_category_weight_status ) ? 'checked' : '';
$cost_on_country_status   = ( ! empty( $cost_on_country_status ) && 'on' === $cost_on_country_status && "" !== $cost_on_country_status ) ? 'checked' : '';
$cost_on_total_cart_qty_status      = ( ! empty( $cost_on_total_cart_qty_status ) && 'on' === $cost_on_total_cart_qty_status && "" !== $cost_on_total_cart_qty_status ) ? 'checked' : '';
$cost_on_total_cart_weight_status   = ( ! empty( $cost_on_total_cart_weight_status ) && 'on' === $cost_on_total_cart_weight_status && "" !== $cost_on_total_cart_weight_status ) ? 'checked' : '';
$cost_on_total_cart_subtotal_status = ( ! empty( $cost_on_total_cart_subtotal_status ) && 'on' === $cost_on_total_cart_subtotal_status && "" !== $cost_on_total_cart_subtotal_status ) ? 'checked' : '';
$cost_on_shipping_class_subtotal_status = ( ! empty( $cost_on_shipping_class_subtotal_status ) && 'on' === $cost_on_shipping_class_subtotal_status && "" !== $cost_on_shipping_class_subtotal_status ) ? 'checked' : '';

$sm_title = !empty($sm_title) ? esc_attr(stripslashes($sm_title)) : '';
$sm_cost = ('' !== $sm_cost) ? esc_attr(stripslashes($sm_cost)) : '';
$sm_tooltip_desc = !empty($sm_tooltip_desc) ? $sm_tooltip_desc : '';
$sm_estimation_delivery = !empty($sm_estimation_delivery) ? esc_attr(stripslashes($sm_estimation_delivery)) : '';
$sm_start_date = !empty($sm_start_date) ? esc_attr(stripslashes($sm_start_date)) : '';
$sm_end_date = !empty($sm_end_date) ? esc_attr(stripslashes($sm_end_date)) : '';
$sm_time_from = !empty($sm_time_from) ? esc_attr(stripslashes($sm_time_from)) : '';
$sm_time_to = !empty($sm_time_to) ? esc_attr(stripslashes($sm_time_to)) : '';
$sm_select_day_of_week = !empty($sm_select_day_of_week) ? $sm_select_day_of_week : '';
$submit_text = __('Save changes', 'min-and-max-quantity-for-woocommerce');

if (empty($fee_settings_unique_shipping_title) && !empty($sm_title)) {
    $fee_settings_unique_shipping_title = $sm_title;
}
?>
<?php // Shipping Rules Condition   ?>
    <div class="text-condtion-is" style="display:none;">
        <select class="text-condition">
            <option value="is_equal_to"><?php esc_html_e('Equal to ( = )', 'min-and-max-quantity-for-woocommerce'); ?></option>
            <option value="less_equal_to"><?php esc_html_e('Less or Equal to ( <= )', 'min-and-max-quantity-for-woocommerce'); ?></option>
            <option value="less_then"><?php esc_html_e('Less than ( < )', 'min-and-max-quantity-for-woocommerce'); ?></option>
            <option value="greater_equal_to"><?php esc_html_e('Greater or Equal to ( >= )', 'min-and-max-quantity-for-woocommerce'); ?></option>
            <option value="greater_then"><?php esc_html_e('Greater than ( > )', 'min-and-max-quantity-for-woocommerce'); ?></option>
            <option value="not_in"><?php esc_html_e('Not Equal to ( != )', 'min-and-max-quantity-for-woocommerce'); ?></option>
        </select>
        <select class="select-condition">
            <option value="is_equal_to"><?php esc_html_e('Equal to ( = )', 'min-and-max-quantity-for-woocommerce'); ?></option>
            <option value="not_in"><?php esc_html_e('Not Equal to ( != )', 'min-and-max-quantity-for-woocommerce'); ?></option>
        </select>
    </div>
    <div class="default-country-box" style="display:none;">
        <?php echo wp_kses($mmqw_admin_object->mmqw_get_country_list(), Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags()); ?>
    </div>

    <div class="mmqw-section-left">
        <div class="mmqw-main-table res-cl">
            <h2><?php esc_html_e('Min/Max Rule Configuration', 'min-and-max-quantity-for-woocommerce'); ?></h2>
            <form method="POST" name="feefrm" action="">
                <?php wp_nonce_field('mmqw_save_action','mmqw_conditions_save'); ?>
                <input type="hidden" name="post_type" value="wc_mmqw">
                <input type="hidden" name="fee_post_id" value="<?php echo esc_attr($get_post_id) ?>">
                <table class="form-table table-outer shipping-method-table">
                    <tbody>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="onoffswitch"><?php esc_html_e('Status', 'min-and-max-quantity-for-woocommerce'); ?></label>
                        </th>
                        <td class="forminp">
                            <label class="switch">
                                <input type="checkbox" name="sm_status" value="on" <?php echo esc_attr($sm_status); ?>>
                                <div class="slider round"></div>
                            </label>
                            <span class="mmqw_for_woocommerce_tab_description"></span>
                            <p class="description" style="display:none;">
                                <?php esc_html_e('Enable or Disable this Min Max rule using this button (This rule only apply if it is enabled).',
	                                'min-and-max-quantity-for-woocommerce'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th class="titledesc" scope="row">
                            <label for="fee_settings_unique_shipping_title"><?php esc_html_e('Min/Max Rule Title', 'min-and-max-quantity-for-woocommerce'); ?>
                                <span class="required-star">*</span>
                            </label>
                        </th>
                        <td class="forminp">
                            <input type="text" name="fee_settings_unique_shipping_title" class="text-class"
                                   id="fee_settings_unique_shipping_title" value="<?php echo esc_attr($fee_settings_unique_shipping_title); ?>"
                                   required=""
                                   placeholder="<?php esc_html_e('Enter Min/Max rule title for admin purpose', 'min-and-max-quantity-for-woocommerce'); ?>">
                            <span class="mmqw_for_woocommerce_tab_description"></span>
                            <p class="description" style="display:none;">
                                <?php esc_html_e('This name will use only for admin purpose', 'min-and-max-quantity-for-woocommerce'); ?>
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php // Advanced Pricing Section start  ?>
                <div id="apm_wrap" class="mmqw-condition-rules">
                    <div class="ap_title">
                        <h2><?php esc_html_e('Advanced Rules for Min/Max Quantities', 'min-and-max-quantity-for-woocommerce'); ?></h2>
                        <label class="switch">
                            <input type="checkbox" name="ap_rule_status"
                                   value="on" <?php echo esc_attr($ap_rule_status); ?>>
                            <div class="slider round"></div>
                        </label>
                        <span class="mmqw_for_woocommerce_tab_description"></span>
                        <p class="description"
                           style="display:none;padding-left: 15px;"><?php esc_html_e('If enabled this Advanced Pricing button only than below all rule\'s will go for apply to shipping method.', 'min-and-max-quantity-for-woocommerce'); ?></p>
                    </div>

                    <div class="pricing_rules">
                        <div class="pricing_rules_tab">
                            <ul class="tabs">
                                <?php
                                $tab_array = array(
                                        'tab-1' => esc_html__('QTY on Product', 'min-and-max-quantity-for-woocommerce'),
                                        'tab-2' => esc_html__('QTY on Variable Product', 'min-and-max-quantity-for-woocommerce'),
                                        'tab-3' => esc_html__('QTY on Category', 'min-and-max-quantity-for-woocommerce'),
                                        'tab-4' => esc_html__('QTY on Country', 'min-and-max-quantity-for-woocommerce')
                                );
                                if (!empty($tab_array)) {
                                    foreach ($tab_array as $data_tab => $tab_title) {
                                        if ("tab-1" === $data_tab) {
                                            $class = " current";
                                        } else {
                                            $class = "";
                                        }
                                        ?>
                                        <li class="tab-link<?php echo esc_attr($class); ?>"
                                            data-tab="<?php echo esc_attr($data_tab); ?>">
                                            <?php esc_html_e($tab_title); ?>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="pricing_rules_tab_content">
                            <?php // Advanced Pricing Product Section end here ?>
                            <div class="ap_product_container min_max_quantity_rule_box tab-content current" id="tab-1" data-title="<?php esc_html_e('QTY on Product', 'min-and-max-quantity-for-woocommerce'); ?>">
                                <div class="tap-class">
                                    <div class="predefined_elements">
                                        <div id="all_product_list"></div>
                                    </div>
                                    <div class="sub-title">
                                        <h2><?php esc_html_e('QTY on Product', 'min-and-max-quantity-for-woocommerce'); ?></h2>
                                        <div class="tap">
                                            <a id="ap-product-add-field" class="button button-primary button-large"
                                               href="javascript:;"><?php esc_html_e('+ Add Rule', 'min-and-max-quantity-for-woocommerce'); ?></a>
                                            <div class="switch_status_div">
                                                <label class="switch switch_in_pricing_rules">
                                                    <input type="checkbox" name="cost_on_product_status"
                                                           value="on" <?php echo esc_attr($cost_on_product_status); ?>>
                                                    <div class="slider round"></div>
                                                </label>
                                                <span class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description switch_in_pricing_rules_description"
                                                   style="display:none;">
                                                    <?php esc_html_e(MMQW_PERTICULAR_FEE_AMOUNT_NOTICE, 'min-and-max-quantity-for-woocommerce'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="tbl_ap_product_method"
                                           class="tbl_product_fee table-outer tap-cas form-table advance-country-method-table">
                                        <tbody>
                                        <tr class="heading">
                                            <th class="titledesc th_product_fees_conditions_condition" scope="row">
                                                <?php esc_html_e('Product', 'min-and-max-quantity-for-woocommerce'); ?> <span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('Select a product to apply Min/Max quantity rule.', 'min-and-max-quantity-for-woocommerce'); ?></p>
                                            </th>
                                            <th class="titledesc th_product_fees_conditions_condition" scope="row">
                                                <?php esc_html_e('Min Quantity ', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('You can set a minimum product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce'); ?>
                                                </p></th>
                                            <th class="titledesc th_product_fees_conditions_condition" scope="row">
                                                <?php esc_html_e('Max Quantity ', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('You can set a maximum product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce'); ?>
                                                    <br/><?php esc_html_e('Leave empty then will set with maximum 999999999999999999999999999', 'min-and-max-quantity-for-woocommerce'); ?>
                                                </p></th>
                                            <th class="titledesc th_product_fees_conditions_condition" scope="row"
                                                colspan="2"><?php esc_html_e('Action', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
	                                            <p class="description" style="display:none;">
		                                            <?php
		                                            esc_html_e('Click below icon to remove specific rule.', 'min-and-max-quantity-for-woocommerce');
		                                            ?>
                                            </th>
                                        </tr>
                                        <?php
                                        //check advanced pricing value fill proper or unset if not
                                        $filled_arr = array();
                                        if (!empty($sm_metabox_ap_product) && is_array($sm_metabox_ap_product)):
                                            foreach ($sm_metabox_ap_product as $app_arr) {
                                                //check that if required field fill or not once save the APR,  if match than fill in array
                                                if (!empty($app_arr) || '' !== $app_arr) {
                                                    if (('' !== $app_arr['ap_fees_products'] && '' !== $app_arr['ap_fees_ap_price_product']) && ('' !== $app_arr['ap_fees_ap_prd_min_qty'] || '' !== $app_arr['ap_fees_ap_prd_max_qty'])) {
                                                        //if condition match than fill in array
                                                        $filled_arr[] = $app_arr;
                                                    }
                                                }
                                            }
                                        endif;
                                        //check APR exist
                                        if (isset($filled_arr) && !empty($filled_arr)) {
                                            $cnt_product = 2;
                                            foreach ($filled_arr as $key => $productfees) {
                                                $fees_ap_fees_products = isset($productfees['ap_fees_products']) ? $productfees['ap_fees_products'] : '';
                                                $ap_fees_ap_min_qty = isset($productfees['ap_fees_ap_prd_min_qty']) ? $productfees['ap_fees_ap_prd_min_qty'] : '';
                                                $ap_fees_ap_max_qty = isset($productfees['ap_fees_ap_prd_max_qty']) ? $productfees['ap_fees_ap_prd_max_qty'] : '';
                                                $ap_fees_ap_price_product = isset($productfees['ap_fees_ap_price_product']) ? $productfees['ap_fees_ap_price_product'] : '';
                                                ?>
                                                <tr id="ap_product_row_<?php echo esc_attr( $cnt_product ); ?>"
                                                    valign="top" class="ap_product_row_tr">
                                                    <td class="titledesc" scope="row">
                                                        <select rel-id="<?php echo esc_attr( $cnt_product ); ?>"
                                                                id="ap_product_fees_conditions_condition_<?php echo esc_attr( $cnt_product ); ?>"
                                                                name="fees[ap_product_fees_conditions_condition][<?php echo esc_attr( $cnt_product ); ?>][]"
                                                                id="ap_product_fees_conditions_condition"
                                                                class="ap_product product_fees_conditions_values multiselect2 min_max_select"
                                                                multiple="multiple">
                                                            <?php
                                                            echo wp_kses( $mmqw_admin_object->mmqw_get_product_options( $cnt_product, $fees_ap_fees_products, false ), $mmqw_object::mmqw_allowed_html_tags() );
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_product ); ?> condition-value">
                                                        <input type="number" name="fees[ap_fees_ap_prd_min_qty][]"
                                                               class="text-class qty-class" id="ap_fees_ap_prd_min_qty[]"
                                                               placeholder="<?php esc_html_e( 'Min quantity', 'min-and-max-quantity-for-woocommerce' ); ?>"
                                                               value="<?php echo esc_attr( $ap_fees_ap_min_qty ); ?>"
                                                               min="0">
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_product ); ?> condition-value">
                                                        <input type="number" name="fees[ap_fees_ap_prd_max_qty][]"
                                                               class="text-class qty-class qty-class" id="ap_fees_ap_prd_max_qty[]"
                                                               placeholder="<?php esc_html_e( 'Max quantity', 'min-and-max-quantity-for-woocommerce' ); ?>"
                                                               value="<?php echo esc_attr( $ap_fees_ap_max_qty ); ?>"
                                                               min="0">
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_product ); ?> condition-value">
                                                        <a id="ap-product-delete-field"
                                                           rel-id="<?php echo esc_attr( $cnt_product ); ?>"
                                                           title="Delete" class="delete-row" href="javascript:;">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                                $cnt_product++;
                                            }
                                            ?>
                                            <?php
                                        } else {
                                            $cnt_product = 1;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="total_row_product" id="total_row_product"
                                           value="<?php echo esc_attr($cnt_product); ?>">
                                </div>
                            </div>
                            <?php // Advanced Pricing Product Section end here ?>

                            <!-- Advanced Pricing Product Subtotal start here -->
                            <div class="ap_product_variation_container min_max_quantity_rule_box tab-content" id="tab-2" data-title="<?php esc_html_e('QTY on Product Subtotal', 'min-and-max-quantity-for-woocommerce'); ?>">
                                <div class="tap-class">
                                    <div class="predefined_elements">
                                        <div id="all_cart_subtotal">
                                            <option value="product_variation"><?php esc_html_e('Product Subtotal', 'min-and-max-quantity-for-woocommerce'); ?></option>
                                        </div>
                                    </div>
                                    <div class="sub-title">
                                        <h2><?php esc_html_e('QTY on Variable Product', 'min-and-max-quantity-for-woocommerce'); ?></h2>
                                        <div class="tap">
                                            <a id="ap-product-variation-add-field" class="button button-primary button-large"
                                               href="javascript:;"><?php esc_html_e('+ Add Rule', 'min-and-max-quantity-for-woocommerce'); ?></a>
                                            <div class="switch_status_div">
                                                <label class="switch switch_in_pricing_rules">
                                                    <input type="checkbox" name="cost_on_product_variation_status"
                                                           value="on" <?php echo esc_attr($cost_on_product_variation_status); ?>>
                                                    <div class="slider round"></div>
                                                </label>
                                                <span class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description switch_in_pricing_rules_description" style="display:none;">
                                                    <?php esc_html_e(MMQW_PERTICULAR_FEE_AMOUNT_NOTICE, 'min-and-max-quantity-for-woocommerce'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="tbl_ap_product_variation_method"
                                           class="tbl_product_variation table-outer tap-cas form-table advance-country-method-table">
                                        <tbody>
                                        <tr class="heading">
                                            <th class="titledesc th_product_variation_fees_conditions_condition"
                                                scope="row"><?php esc_html_e('Variable Product', 'min-and-max-quantity-for-woocommerce'); ?>
                                                <span class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('Select Variable Products to apply Min/Max quantity rule', 'min-and-max-quantity-for-woocommerce');
                                                    ?></p></th>
                                            <th class="titledesc th_product_variation_fees_conditions_condition" scope="row">
                                                <?php esc_html_e('Min Quantity ', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('You can set a minimum variable product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce')
                                                    ; ?>
                                                </p></th>
                                            <th class="titledesc th_product_variation_fees_conditions_condition" scope="row">
                                                <?php esc_html_e('Max Quantity', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('You can set a maximum variable product quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce'); ?>
                                                    <br/><?php esc_html_e('Leave empty then will set with maximum 999999999999999999999999999', 'min-and-max-quantity-for-woocommerce'); ?>
                                                </p></th>
                                            <th class="titledesc th_product_variation_fees_conditions_condition" scope="row"
                                                colspan="2"><?php esc_html_e('Action ', 'min-and-max-quantity-for-woocommerce'); ?> <span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php
                                                    esc_html_e('Click below icon to remove specific rule.', 'min-and-max-quantity-for-woocommerce');
                                                    ?>
                                            </th>
                                        </tr>
                                        <?php
                                        //check advanced pricing value fill proper or unset if not
                                        $filled_product_variation = array();
                                        //check if category AP rules exist

                                        if (!empty($sm_metabox_ap_product_variation) && is_array($sm_metabox_ap_product_variation)):

                                            foreach ($sm_metabox_ap_product_variation as $apcat_arr):
                                                //check that if required field fill or not once save the APR,  if match than fill in array
                                                if (!empty($apcat_arr) || $apcat_arr !== '') {
                                                    if (
                                                        ($apcat_arr['ap_fees_product_variation'] !== '' && $apcat_arr['ap_fees_ap_price_product_variation'] !== '') &&
                                                        ($apcat_arr['ap_fees_ap_product_variation_min_qty'] !== '' || $apcat_arr['ap_fees_ap_product_variation_max_qty'] !== '')
                                                    ) {
                                                        $filled_product_variation[] = $apcat_arr;
                                                    }
                                                }
                                            endforeach;
                                        endif;
                                        //check APR exist
                                        if (isset($filled_product_variation) && !empty($filled_product_variation)) {
                                            $cnt_product_variation = 2;
                                            foreach ($filled_product_variation as $key => $productfees) {
                                                $fees_ap_fees_product_variation = isset($productfees['ap_fees_product_variation']) ? $productfees['ap_fees_product_variation'] : '';
                                                $ap_fees_ap_product_variation_min_qty = isset($productfees['ap_fees_ap_product_variation_min_qty']) ? $productfees['ap_fees_ap_product_variation_min_qty'] : '';
                                                $ap_fees_ap_product_variation_max_qty = isset($productfees['ap_fees_ap_product_variation_max_qty']) ? $productfees['ap_fees_ap_product_variation_max_qty'] : '';
                                                $ap_fees_ap_price_product_variation = isset($productfees['ap_fees_ap_price_product_variation']) ? $productfees['ap_fees_ap_price_product_variation'] : '';
                                                ?>
                                                <tr id="ap_product_variation_row_<?php echo esc_attr( $cnt_product_variation ); ?>"
                                                    valign="top" class="ap_product_variation_row_tr">
                                                    <td class="titledesc" scope="row">
                                                        <select rel-id="<?php echo esc_attr( $cnt_product_variation ); ?>"
                                                                id="ap_product_variation_fees_conditions_condition_<?php echo esc_attr( $cnt_product_variation ); ?>"
                                                                name="fees[ap_product_variation_fees_conditions_condition][<?php echo esc_attr( $cnt_product_variation ); ?>][]"
                                                                class="ap_product_variation product_fees_conditions_values multiselect2 min_max_select"
                                                                multiple="multiple">
                                                            <?php
                                                            echo wp_kses( $mmqw_admin_object->mmqw_get_product_options( $cnt_product_variation, $fees_ap_fees_product_variation, true), $mmqw_object::mmqw_allowed_html_tags() );
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_product_variation ); ?> condition-value">
                                                        <input type="number"
                                                               name="fees[ap_fees_ap_product_variation_min_qty][]"
                                                               class="text-class qty-class" id="ap_fees_ap_product_variation_min_qty[]"
                                                               placeholder="<?php esc_html_e( 'Min Quantity', 'min-and-max-quantity-for-woocommerce' ); ?>"
                                                               step="1"
                                                               value="<?php echo esc_attr( $ap_fees_ap_product_variation_min_qty ); ?>"
                                                               min="0">
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_product_variation ); ?> condition-value">
                                                        <input type="number"
                                                               name="fees[ap_fees_ap_product_variation_max_qty][]"
                                                               class="text-class qty-class" id="ap_fees_ap_product_variation_max_qty[]"
                                                               placeholder="<?php esc_html_e( 'Max Quantity', 'min-and-max-quantity-for-woocommerce' ); ?>"
                                                               step="1"
                                                               value="<?php echo esc_attr( $ap_fees_ap_product_variation_max_qty ); ?>"
                                                               min="0">
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_product_variation ); ?> condition-value">
                                                        <a id="ap-product-subtotal-delete-field"
                                                           rel-id="<?php echo esc_attr( $cnt_product_variation ); ?>"
                                                           title="Delete" class="delete-row" href="javascript:;">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                                $cnt_product_variation++;
                                            }
                                            ?>
                                            <?php
                                        } else {
                                            $cnt_product_variation = 1;
                                        } ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="total_row_product_variation" id="total_row_product_variation"
                                           value="<?php echo esc_attr($cnt_product_variation); ?>">
                                    <!-- Advanced Pricing Category Section end here -->
                                </div>
                            </div>
                            <!-- Advanced Pricing Product Subtotal end here -->
                            <?php // Advanced Pricing Category Section start here ?>
                            <div class="ap_category_container min_max_quantity_rule_box tab-content" id="tab-3" data-title="<?php esc_html_e('QTY on Category', 'min-and-max-quantity-for-woocommerce'); ?>">
                                <div class="tap-class">
                                <div class="predefined_elements">
                                    <div id="all_category_list">
                                        <?php
                                        echo wp_kses($mmqw_admin_object->mmqw_get_category_options('', $json = true), Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags());
                                        ?>
                                    </div>
                                </div>
                                <div class="sub-title">
                                    <h2><?php esc_html_e('QTY on Category', 'min-and-max-quantity-for-woocommerce'); ?></h2>
                                    <div class="tap">
                                        <a id="ap-category-add-field" class="button button-primary button-large"
                                           href="javascript:;"><?php esc_html_e('+ Add Rule', 'min-and-max-quantity-for-woocommerce'); ?></a>
                                        <div class="switch_status_div">
                                            <label class="switch switch_in_pricing_rules">
                                                <input type="checkbox" name="cost_on_category_status"
                                                       value="on" <?php echo esc_attr($cost_on_category_status); ?>>
                                                <div class="slider round"></div>
                                            </label>
                                            <span class="mmqw_for_woocommerce_tab_description"></span>
                                            <p class="description switch_in_pricing_rules_description"
                                               style="display:none;">
                                                <?php esc_html_e(MMQW_PERTICULAR_FEE_AMOUNT_NOTICE, 'min-and-max-quantity-for-woocommerce'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <table id="tbl_ap_category_method"
                                       class="tbl_category_fee table-outer tap-cas form-table advance-country-method-table">
                                    <tbody>
                                    <tr class="heading">
                                        <th class="titledesc th_category_fees_conditions_condition"
                                            scope="row"><?php esc_html_e('Category', 'min-and-max-quantity-for-woocommerce'); ?>
                                            <span class="mmqw_for_woocommerce_tab_description"></span>
                                            <p class="description" style="display:none;">
                                                <?php esc_html_e('Select Category to apply Min/Max quantity rule.', 'min-and-max-quantity-for-woocommerce'); ?></p>
                                        </th>
                                        <th class="titledesc th_category_fees_conditions_condition" scope="row">
                                            <?php esc_html_e('Min Quantity ', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                    class="mmqw_for_woocommerce_tab_description"></span>
                                            <p class="description" style="display:none;">
                                                <?php esc_html_e('You can set a minimum category quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce'); ?>
                                            </p></th>
                                        <th class="titledesc th_category_fees_conditions_condition" scope="row">
                                            <?php esc_html_e('Max Quantity ', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                    class="mmqw_for_woocommerce_tab_description"></span>
                                            <p class="description" style="display:none;">
                                                <?php esc_html_e('You can set a maximum category quantity to apply on product detail page and cart page.', 'min-and-max-quantity-for-woocommerce'); ?>
                                                <br/><?php esc_html_e('Leave empty then will set with maximum 999999999999999999999999999', 'min-and-max-quantity-for-woocommerce'); ?>
                                            </p></th>
                                        <th class="titledesc th_category_fees_conditions_condition" scope="row"
                                            colspan="2"><?php esc_html_e('Action', 'min-and-max-quantity-for-woocommerce'); ?> <span
                                                    class="mmqw_for_woocommerce_tab_description"></span>
                                            <p class="description" style="display:none;">
                                                <?php
                                                esc_html_e('Click below icon to remove specific rule.', 'min-and-max-quantity-for-woocommerce');
                                                ?>
                                            </p>
                                        </th>
                                    </tr>
                                    <?php
                                    //check advanced pricing value fill proper or unset if not
                                    $filled_arr = array();
                                    //check if category AP rules exist
                                    if (!empty($sm_metabox_ap_category) && is_array($sm_metabox_ap_category)):

                                        foreach ($sm_metabox_ap_category as $apcat_arr):
                                            //check that if required field fill or not once save the APR,  if match than fill in array
                                            if (!empty($apcat_arr) || '' !== $apcat_arr) {
                                                if (('' !== $apcat_arr['ap_fees_categories'] && '' !== $apcat_arr['ap_fees_ap_price_category']) &&
                                                    ('' !== $apcat_arr['ap_fees_ap_cat_min_qty'] || '' !== $apcat_arr['ap_fees_ap_cat_max_qty'])) {
                                                    //if condition match than fill in array
                                                    $filled_arr[] = $apcat_arr;
                                                }
                                            }
                                        endforeach;
                                    endif;
                                    //check APR exist
                                    if (isset($filled_arr) && !empty($filled_arr)) {
                                        $cnt_category = 2;
                                        foreach ($filled_arr as $key => $productfees) {
                                            $fees_ap_fees_categories = isset($productfees['ap_fees_categories']) ? $productfees['ap_fees_categories'] : '';
                                            $ap_fees_ap_cat_min_qty = isset($productfees['ap_fees_ap_cat_min_qty']) ? $productfees['ap_fees_ap_cat_min_qty'] : '';
                                            $ap_fees_ap_cat_max_qty = isset($productfees['ap_fees_ap_cat_max_qty']) ? $productfees['ap_fees_ap_cat_max_qty'] : '';
                                            $ap_fees_ap_price_category = isset($productfees['ap_fees_ap_price_category']) ? $productfees['ap_fees_ap_price_category'] : '';
                                            ?>
                                            <tr id="ap_category_row_<?php echo esc_attr( $cnt_category ); ?>" valign="top"
                                                class="ap_category_row_tr">
                                                <td class="titledesc" scope="row">
                                                    <select rel-id="<?php echo esc_attr( $cnt_category ); ?>"
                                                            id="ap_category_fees_conditions_condition_<?php echo esc_attr( $cnt_category ); ?>"
                                                            name="fees[ap_category_fees_conditions_condition][<?php echo esc_attr( $cnt_category ); ?>][]"
                                                            id="ap_category_fees_conditions_condition"
                                                            class="ap_category product_fees_conditions_values multiselect2 min_max_select"
                                                            multiple="multiple">
                                                        <?php
                                                        echo wp_kses( $mmqw_admin_object->mmqw_get_category_options( $fees_ap_fees_categories, $json = false ), $mmqw_object::mmqw_allowed_html_tags() );
                                                        ?>
                                                    </select>
                                                </td>
                                                <td class="column_<?php echo esc_attr( $cnt_category ); ?> condition-value">
                                                    <input type="number"
                                                           name="fees[ap_fees_ap_cat_min_qty][]"
                                                           class="text-class qty-class" id="ap_fees_ap_cat_min_qty[]"
                                                           placeholder="<?php esc_html_e( 'Min quantity', 'min-and-max-quantity-for-woocommerce' ); ?>"
                                                           value="<?php echo esc_attr( $ap_fees_ap_cat_min_qty ); ?>"
                                                           min="0">
                                                </td>
                                                <td class="column_<?php echo esc_attr( $cnt_category ); ?> condition-value">
                                                    <input type="number"
                                                           name="fees[ap_fees_ap_cat_max_qty][]"
                                                           class="text-class qty-class" id="ap_fees_ap_cat_max_qty[]"
                                                           placeholder="<?php esc_html_e( 'Max quantity', 'min-and-max-quantity-for-woocommerce' ); ?>"
                                                           value="<?php echo esc_attr( $ap_fees_ap_cat_max_qty ); ?>"
                                                           min="0">
                                                </td>
                                                <td class="column_<?php echo esc_attr( $cnt_category ); ?> condition-value">
                                                    <a id="ap-category-delete-field"
                                                       rel-id="<?php echo esc_attr( $cnt_category ); ?>"
                                                       title="Delete" class="delete-row" href="javascript:;">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                            $cnt_category++;
                                        }
                                        ?>
                                        <?php
                                    } else {
                                        $cnt_category = 1;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <input type="hidden" name="total_row_category" id="total_row_category"
                                       value="<?php echo esc_attr($cnt_category); ?>">
                                <!-- Advanced Pricing Category Section end here -->
                                </div>
                            </div>
                            <?php // Advanced Pricing Category Section end here  ?>

                            <!-- Advanced Pricing Category Subtotal start here -->
                            <div class="ap_country_container min_max_quantity_rule_box tab-content" id="tab-4" data-title="<?php esc_html_e('QTY on Country',
	                            'min-and-max-quantity-for-woocommerce'); ?>">
                                <div class="tap-class">
                                    <div class="predefined_elements">
                                        <div id="all_country_list">
                                            <?php
                                            echo wp_kses($mmqw_admin_object->mmqw_pro_get_country_list('', $json = true), Min_Max_Quantity_For_WooCommerce::mmqw_allowed_html_tags());
                                            ?>
                                        </div>
                                    </div>
                                    <div class="sub-title">
                                        <h2><?php esc_html_e('QTY on Country', 'min-and-max-quantity-for-woocommerce'); ?></h2>
                                        <div class="tap">
                                            <a id="ap-country-add-field" class="button button-primary button-large"
                                               href="javascript:;"><?php esc_html_e('+ Add Rule', 'min-and-max-quantity-for-woocommerce'); ?></a>
                                            <div class="switch_status_div">
                                                <label class="switch switch_in_pricing_rules">
                                                    <input type="checkbox" name="cost_on_country_status"
                                                           value="on" <?php echo esc_attr($cost_on_country_status); ?>>
                                                    <div class="slider round"></div>
                                                </label>
                                                <span class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description switch_in_pricing_rules_description" style="display:none;">
                                                    <?php esc_html_e(MMQW_PERTICULAR_FEE_AMOUNT_NOTICE, 'min-and-max-quantity-for-woocommerce'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="tbl_ap_country_method"
                                           class="tbl_country table-outer tap-cas form-table advance-country-method-table">
                                        <tbody>
                                        <tr class="heading">
                                            <th class="titledesc th_country_fees_conditions_condition"
                                                scope="row"><?php esc_html_e('Country', 'min-and-max-quantity-for-woocommerce'); ?>
                                                <span class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('Select country to apply the Min/Max quantity rule.', 'min-and-max-quantity-for-woocommerce');
                                                    ?></p></th>
                                            <th class="titledesc th_country_fees_conditions_condition" scope="row">
                                                <?php esc_html_e('Min Quantity ', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('You can set minimum quantity for selected countries to apply on cart page.', 'min-and-max-quantity-for-woocommerce'); ?>
                                                </p></th>
                                            <th class="titledesc th_country_fees_conditions_condition" scope="row">
                                                <?php esc_html_e('Max Quantity', 'min-and-max-quantity-for-woocommerce'); ?><span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php esc_html_e('You can set maximum quantity for selected countries to apply on cart page.', 'min-and-max-quantity-for-woocommerce'); ?>
                                                    <br/><?php esc_html_e('Add 999999 for the unlimited quantity', 'min-and-max-quantity-for-woocommerce'); ?>
                                                </p></th>
                                            <th class="titledesc th_country_fees_conditions_condition" scope="row"
                                                colspan="2"><?php esc_html_e('Action', 'min-and-max-quantity-for-woocommerce'); ?> <span
                                                        class="mmqw_for_woocommerce_tab_description"></span>
                                                <p class="description" style="display:none;">
                                                    <?php
                                                    esc_html_e('Click below icon to remove specific rule.', 'min-and-max-quantity-for-woocommerce');
                                                    ?>
                                            </th>
                                        </tr>
                                        <?php
                                        //check advanced pricing value fill proper or unset if not
                                        $filled_country = array();
                                        //check if category AP rules exist

                                        if (!empty($sm_metabox_ap_country) && is_array($sm_metabox_ap_country)):

                                            foreach ($sm_metabox_ap_country as $apcat_arr):
                                                //check that if required field fill or not once save the APR,  if match than fill in array
                                                if (!empty($apcat_arr) || $apcat_arr !== '') {
                                                    if (
                                                        ($apcat_arr['ap_fees_country'] !== '' && $apcat_arr['ap_fees_ap_price_country'] !== '') &&
                                                        ($apcat_arr['ap_fees_ap_country_min_subtotal'] !== '' || $apcat_arr['ap_fees_ap_country_max_subtotal'] !== '')
                                                    ) {
                                                        $filled_country[] = $apcat_arr;
                                                    }
                                                }
                                            endforeach;
                                        endif;
                                        //check APR exist
                                        if (isset($filled_country) && !empty($filled_country)) {
                                            $cnt_country = 2;
                                            foreach ($filled_country as $key => $productfees) {
                                                $fees_ap_fees_country = isset($productfees['ap_fees_country']) ? $productfees['ap_fees_country'] : '';
                                                $ap_fees_ap_country_min_subtotal = isset($productfees['ap_fees_ap_country_min_subtotal']) ? $productfees['ap_fees_ap_country_min_subtotal'] : '';
                                                $ap_fees_ap_country_max_subtotal = isset($productfees['ap_fees_ap_country_max_subtotal']) ? $productfees['ap_fees_ap_country_max_subtotal'] : '';
                                                $ap_fees_ap_price_country = isset($productfees['ap_fees_ap_price_country']) ? $productfees['ap_fees_ap_price_country'] : '';
                                                ?>
                                                <tr id="ap_country_row_<?php echo esc_attr( $cnt_country ); ?>"
                                                    valign="top" class="ap_country_row_tr">
                                                    <td class="titledesc" scope="row">
                                                        <select rel-id="<?php echo esc_attr( $cnt_country ); ?>"
                                                                id="ap_country_fees_conditions_condition_<?php echo esc_attr( $cnt_country ); ?>"
                                                                name="fees[ap_country_fees_conditions_condition][<?php echo esc_attr( $cnt_country ); ?>][]"
                                                                id="ap_country_fees_conditions_condition"
                                                                class="ap_country product_fees_conditions_values multiselect2 min_max_select"
                                                                multiple="multiple">
                                                            <?php
                                                            echo wp_kses( $mmqw_admin_object->mmqw_pro_get_country_list( $fees_ap_fees_country, $json = false ), $mmqw_object::mmqw_allowed_html_tags() );
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_country ); ?> condition-value">
                                                        <input type="number"
                                                               name="fees[ap_fees_ap_country_min_subtotal][]"
                                                               class="text-class qty-class" id="ap_fees_ap_country_min_subtotal[]"
                                                               placeholder="<?php esc_html_e( 'Min Quantity', 'min-and-max-quantity-for-woocommerce' ); ?>"
                                                               step="1"
                                                               value="<?php echo esc_attr( $ap_fees_ap_country_min_subtotal ); ?>"
                                                               min="0">
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_country ); ?> condition-value">
                                                        <input type="number"
                                                               name="fees[ap_fees_ap_country_max_subtotal][]"
                                                               class="text-class qty-class" id="ap_fees_ap_country_max_subtotal[]"
                                                               placeholder="<?php esc_html_e( 'Max Quantity', 'min-and-max-quantity-for-woocommerce' ); ?>"
                                                               step="1"
                                                               value="<?php echo esc_attr( $ap_fees_ap_country_max_subtotal ); ?>"
                                                               min="0">
                                                    </td>
                                                    <td class="column_<?php echo esc_attr( $cnt_country ); ?> condition-value">
                                                        <a id="ap-category-subtotal-delete-field"
                                                           rel-id="<?php echo esc_attr( $cnt_country ); ?>"
                                                           title="Delete" class="delete-row" href="javascript:;">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php
                                                $cnt_country++;
                                            }
                                            ?>
                                            <?php
                                        } else {
                                            $cnt_country = 1;
                                        } ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="total_row_country" id="total_row_country"
                                           value="<?php echo esc_attr($cnt_country); ?>">
                                    <!-- Advanced Pricing Category Section end here -->

                                </div>
                            </div>
                            <!-- Advanced Pricing Category Subtotal  end here -->
                        </div>
                    </div>
                </div>
                <?php // Advanced Pricing Section end  ?>
                <p class="submit">
                    <input type="submit" name="submitFee" class="button button-primary button-large"
                           value="<?php echo esc_attr($submit_text); ?>">
                </p>
            </form>
        </div>

    </div>
<?php
require_once(plugin_dir_path( __FILE__ ).'header/plugin-sidebar.php');
