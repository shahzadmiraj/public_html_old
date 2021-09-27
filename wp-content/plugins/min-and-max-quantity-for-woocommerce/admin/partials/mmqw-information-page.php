<?php
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once(plugin_dir_path( __FILE__ ).'header/plugin-header.php');
?>

<div class="mmqw-section-left">
    <div class="mmqw-main-table res-cl">
        <h2><?php esc_html_e('Quick info', 'min-and-max-quantity-for-woocommerce'); ?></h2>
        <table class="table-outer">
            <tbody>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Product Type', 'min-and-max-quantity-for-woocommerce'); ?></td>
                    <td class="fr-2"><?php esc_html_e('WooCommerce Plugin', 'min-and-max-quantity-for-woocommerce'); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Product Name', 'min-and-max-quantity-for-woocommerce'); ?></td>
                    <td class="fr-2"><?php esc_html_e($plugin_name, 'min-and-max-quantity-for-woocommerce'); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Installed Version', 'min-and-max-quantity-for-woocommerce'); ?></td>
                    <td class="fr-2"><?php esc_html_e('Free Version', 'min-and-max-quantity-for-woocommerce'); ?> <?php echo esc_html_e($plugin_version, 'min-and-max-quantity-for-woocommerce'); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('License & Terms of use', 'min-and-max-quantity-for-woocommerce'); ?></td>
                    <td class="fr-2"><a target="_blank"  href="<?php echo esc_url('www.thedotstore.com/terms-and-conditions'); ?>"><?php esc_html_e('Click here', 'min-and-max-quantity-for-woocommerce'); ?></a><?php esc_html_e(' to view license and terms of use.', 'min-and-max-quantity-for-woocommerce'); ?></td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Help & Support', 'min-and-max-quantity-for-woocommerce'); ?></td>
                    <td class="fr-2">
                        <ul>
                            <li><a href="<?php echo esc_url(add_query_arg(array('page' => 'mmqw-get-started'), admin_url('admin.php'))); ?>"><?php esc_html_e('Quick Start', 'min-and-max-quantity-for-woocommerce'); ?></a></li>
                            <li><a target="_blank" href="<?php echo esc_url('#'); ?>"><?php esc_html_e('Guide Documentation', 'min-and-max-quantity-for-woocommerce');
                            ?></a></li>
                            <li><a target="_blank" href="<?php echo esc_url('www.thedotstore.com/support'); ?>"><?php esc_html_e('Support Forum', 'min-and-max-quantity-for-woocommerce'); ?></a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td class="fr-1"><?php esc_html_e('Localization', 'min-and-max-quantity-for-woocommerce'); ?></td>
                    <td class="fr-2"><?php esc_html_e('English, German', 'min-and-max-quantity-for-woocommerce'); ?></td>
                </tr>

            </tbody>
        </table>
    </div>
</div>
<?php
require_once(plugin_dir_path( __FILE__ ).'header/plugin-sidebar.php');