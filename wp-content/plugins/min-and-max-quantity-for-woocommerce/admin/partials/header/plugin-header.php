<?php
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$plugin_name = MMQW_PLUGIN_NAME;
$plugin_version = MMQW_PLUGIN_VERSION;
?>
<div id="dotsstoremain">
    <div class="all-pad">
        <header class="dots-header">
            <div class="dots-logo-main">
                <img src="<?php echo esc_url(MMQW_PLUGIN_URL . 'admin/images/min-max-logo.png'); ?>">
            </div>
            <div class="dots-header-right">
                <div class="logo-detail">
                    <strong><?php esc_html_e($plugin_name, 'min-and-max-quantity-for-woocommerce'); ?></strong>
                    <span><?php esc_html_e('Free Version', 'min-and-max-quantity-for-woocommerce'); ?> <?php echo esc_html__( $plugin_version, 'min-and-max-quantity-for-woocommerce'); ?></span>
                </div>
                <div class="button-group">
                    <div class="button-dots">
                        <span class="support_dotstore_image"><a target="_blank" href="<?php echo esc_url('http://www.thedotstore.com/support/'); ?>">
                                <img src="<?php echo esc_url(MMQW_PLUGIN_URL . 'admin/images/support_new.png'); ?>"></a>
                        </span>
                    </div>
                </div>
            </div>

            <?php
            $current_page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING);
            $add_zone = filter_input(INPUT_GET,'add_zone',FILTER_SANITIZE_STRING);
            $min_max_quantity_list = isset($current_page) && 'mmqw-rules-list' === $current_page  ? 'active' : '';
            $min_max_quantity_add = isset($current_page) && 'mmqw-add-rules' === $current_page ? 'active' : '';
            $min_max_quantity_edit = isset($current_page) && 'mmqw-edit-rule' === $current_page ? 'active' : '';
            $min_max_quantity_zones = isset($current_page) && 'mmqw-checkout-settings' === $current_page ? 'active' : '';
            $mmqw_manage_messages = isset($current_page) && 'mmqw-manage-messages' === $current_page ? 'active' : '';
            $mmqw_getting_started = isset($current_page) && 'mmqw-get-started' === $current_page ? 'active' : '';
            $mmqw_information = isset($current_page) && 'mmqw-information' === $current_page ? 'active' : '';
            $mmqw_validate = isset($current_page) && 'mmqw-pro-validate' === $current_page ? 'active' : '';
            if (isset($current_page) && 'mmqw-information' === $current_page || isset($current_page) && 'mmqw-get-started' === $current_page) {
                $fee_about = 'active';
            } else {
                $fee_about = '';
            }

            $mmqw_action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);
            if (isset($mmqw_action) && !empty($mmqw_action)) {
                if ('add' === $mmqw_action || 'edit' === $mmqw_action) {
                    $min_max_quantity_add = 'active';
                }
            }
            ?>
            <div class="dots-menu-main">
                <nav>
                    <ul>
                        <li>
                            <a class="dotstore_plugin <?php echo esc_attr( $min_max_quantity_list ); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-rules-list'), admin_url('admin.php'))); ?>"><?php esc_html_e('Manage Min/Max Rules', 'min-and-max-quantity-for-woocommerce'); ?></a>
                        </li>
                        <li>
                            <?php
                            if (isset($current_page) && 'mmqw-add-rules' === $current_page) {
                                ?>
                                <a class="dotstore_plugin <?php echo esc_attr( $min_max_quantity_add ); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-add-rules'), admin_url('admin.php'))); ?>"><?php esc_html_e('Add New Min/Max Rule', 'min-and-max-quantity-for-woocommerce'); ?></a>
                                <?php
                            } else if (isset($current_page) && 'mmqw-edit-rule' === $current_page) {
                                ?>
                                <a class="dotstore_plugin <?php echo esc_attr( $min_max_quantity_edit ); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-add-rules'), admin_url('admin.php'))); ?>"><?php esc_html_e('Edit Min/Max rule', 'min-and-max-quantity-for-woocommerce'); ?></a>
                                <?php
                            } else {
                                ?>
                                <a class="dotstore_plugin <?php echo esc_attr( $min_max_quantity_add ); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-add-rules'), admin_url('admin.php'))); ?>"><?php esc_html_e('Add New Min/Max Rule', 'min-and-max-quantity-for-woocommerce'); ?></a>
                                <?php
                            }
                            ?>
                        </li>
                        <li>
                            <a class="dotstore_plugin <?php echo esc_attr( $min_max_quantity_zones ); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-checkout-settings'), admin_url('admin.php'))); ?>"><?php esc_html_e('Checkout Settings', 'min-and-max-quantity-for-woocommerce'); ?></a>
                        </li>
	                    <li>
		                    <a class="dotstore_plugin <?php echo esc_attr($mmqw_manage_messages); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-manage-messages'), admin_url('admin.php'))); ?>"><?php esc_html_e('Manage Messages', 'min-and-max-quantity-for-woocommerce'); ?></a>
	                    </li>
                        <li>
                            <a class="dotstore_plugin <?php echo esc_attr( $fee_about ); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-get-started'), admin_url('admin.php'))); ?>"><?php esc_html_e('About Plugin', 'min-and-max-quantity-for-woocommerce'); ?></a>
                            <ul class="sub-menu">
                                <li><a class="dotstore_plugin <?php echo esc_attr( $mmqw_getting_started ); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-get-started'), admin_url('admin.php'))); ?>"><?php esc_html_e('Getting Started', 'min-and-max-quantity-for-woocommerce'); ?></a></li>
                                <li><a class="dotstore_plugin <?php echo esc_attr( $mmqw_information ); ?>" href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-information'), admin_url('admin.php'))); ?>"><?php esc_html_e('Quick info', 'min-and-max-quantity-for-woocommerce'); ?></a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="dotstore_plugin"><?php esc_html_e('Dotstore', 'min-and-max-quantity-for-woocommerce'); ?></a>
                            <ul class="sub-menu">
                                <li><a target="_blank" href="<?php echo esc_url('www.thedotstore.com/woocommerce-plugins'); ?>"><?php esc_html_e('WooCommerce Plugins', 'min-and-max-quantity-for-woocommerce'); ?></a></li>
                                <li><a target="_blank" href="<?php echo esc_url('www.thedotstore.com/wordpress-plugins'); ?>"><?php esc_html_e('Wordpress Plugins', 'min-and-max-quantity-for-woocommerce'); ?></a></li><br>
                                <li><a target="_blank" href="<?php echo esc_url('www.thedotstore.com/support'); ?>"><?php esc_html_e('Contact Support', 'min-and-max-quantity-for-woocommerce'); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>