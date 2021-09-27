<?php
/**
 * Plugin review class.
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Heavily based on code by CoBlocks
 * https://github.com/coblocks/coblocks/blob/master/includes/admin/class-coblocks-feedback.php
 *
 * @package   MinMaxWoo
 * @author    theDotstore from MinMaxWoo
 * @link      https://editorsMinMaxWoo.com
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Feedback Notice Class
 */

class MinMaxWoo_User_Feedback {

	/**
	 * Slug.
	 *
	 * @var string $slug
	 */
	private $slug;

	/**
	 * Name.
	 *
	 * @var string $name
	 */
	private $name;

	/**
	 * Time limit.
	 *
	 * @var string $time_limit
	 */
	private $time_limit;

	/**
	 * No Bug Option.
	 *
	 * @var string $nobug_option
	 */
	public $nobug_option;

	/**
	 * Activation Date Option.
	 *
	 * @var string $date_option
	 */
	public $date_option;

	/**
	 * Class constructor.
	 *
	 * @param string $args Arguments.
	 */
	public function __construct( $args ) {

		$this->slug = $args['slug'];
		$this->name = $args['name'];

		$this->date_option  = $this->slug . '_activation_date';
		$this->nobug_option = $this->slug . '_no_bug';

		if ( isset( $args['time_limit'] ) ) {
			$this->time_limit = $args['time_limit'];
		} else {
			$this->time_limit = WEEK_IN_SECONDS;
		}

		// Add actions.
		add_action( 'admin_init', array( $this, 'check_installation_date' ) );
		add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
	}

	/**
	 * Seconds to words.
	 *
	 * @param string $seconds Seconds in time.
	 */
	public function seconds_to_words( $seconds ) {

		// Get the years.
		$years = ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
		if ( $years > 1 ) {
			/* translators: Number of years */
			return sprintf( __( '%s years', 'min-and-max-quantity-for-woocommerce' ), $years );
		} elseif ( $years > 0 ) {
			return __( 'a year', 'min-and-max-quantity-for-woocommerce' );
		}

		// Get the weeks.
		$weeks = ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
		if ( $weeks > 1 ) {
			/* translators: Number of weeks */
			return sprintf( __( '%s weeks', 'min-and-max-quantity-for-woocommerce' ), $weeks );
		} elseif ( $weeks > 0 ) {
			return __( 'a week', 'min-and-max-quantity-for-woocommerce' );
		}

		// Get the days.
		$days = ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
		if ( $days > 1 ) {
			/* translators: Number of days */
			return sprintf( __( '%s days', 'min-and-max-quantity-for-woocommerce' ), $days );
		} elseif ( $days > 0 ) {
			return __( 'a day', 'min-and-max-quantity-for-woocommerce' );
		}

		// Get the hours.
		$hours = ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
		if ( $hours > 1 ) {
			/* translators: Number of hours */
			return sprintf( __( '%s hours', 'min-and-max-quantity-for-woocommerce' ), $hours );
		} elseif ( $hours > 0 ) {
			return __( 'an hour', 'min-and-max-quantity-for-woocommerce' );
		}

		// Get the minutes.
		$minutes = ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
		if ( $minutes > 1 ) {
			/* translators: Number of minutes */
			return sprintf( __( '%s minutes', 'min-and-max-quantity-for-woocommerce' ), $minutes );
		} elseif ( $minutes > 0 ) {
			return __( 'a minute', 'min-and-max-quantity-for-woocommerce' );
		}

		// Get the seconds.
		$seconds = intval( $seconds ) % 60;
		if ( $seconds > 1 ) {
			/* translators: Number of seconds */
			return sprintf( __( '%s seconds', 'min-and-max-quantity-for-woocommerce' ), $seconds );
		} elseif ( $seconds > 0 ) {
			return __( 'a second', 'min-and-max-quantity-for-woocommerce' );
		}
	}

	/**
	 * Check date on admin initiation and add to admin notice if it was more than the time limit.
	 */
	public function check_installation_date() {

		
		if ( ! get_site_option( $this->nobug_option ) || false === get_site_option( $this->nobug_option ) ) {

			add_site_option( $this->date_option, time() );

			// Retrieve the activation date.
			$install_date = get_site_option( $this->date_option );
			
			// If difference between install date and now is greater than time limit, then display notice.
			if ( ( time() - $install_date ) > $this->time_limit ) {
				add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			}
		}
	}

	/**
	 * Display the admin notice.
	 */
	public function display_admin_notice() {

		$screen = get_current_screen();
		if ( isset( $screen->base ) && ( 'plugins' === $screen->base || 'dotstore-plugins_page_mmqw-rules-list' === $screen->base )) {
			$no_bug_url = wp_nonce_url( admin_url( 'plugins.php?' . $this->nobug_option . '=true' ), 'editorsMinMaxWoo-feedback-nounce' );
			$time       = $this->seconds_to_words( time() - get_site_option( $this->date_option ) );
			?>

			<style>
			.notice.editorsMinMaxWoo-notice {
				border-left-color: #272c51 !important;
				padding: 20px;
			}
			.rtl .notice.editorsMinMaxWoo-notice {
				border-right-color: #272c51 !important;
			}
			.notice.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner {
				display: table;
				width: 100%;
			}
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner .editorsMinMaxWoo-notice-icon,
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner .editorsMinMaxWoo-notice-content,
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner .editorsMinMaxWoo-install-now {
				display: table-cell;
				vertical-align: middle;
			}
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-icon {
				color: #509ed2;
				font-size: 13px;
				width: 60px;
			}
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-icon img {
				width: 64px;
			}
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-content {
				padding: 0 40px 0 20px;
			}
			.notice.editorsMinMaxWoo-notice p {
				padding: 0;
				margin: 0;
			}
			.notice.editorsMinMaxWoo-notice h3 {
				margin: 0 0 5px;
			}
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-install-now {
				text-align: center;
			}
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-install-now .editorsMinMaxWoo-install-button {
				padding: 6px 50px;
				height: auto;
				line-height: 20px;
				background: #32396a;
				border-color: #272c51 #0f153e #040823;
				box-shadow: 0 1px 0 #0d1f82;
				text-shadow: 0 -1px 1px #272c51, 1px 0 1px #171b3e, 0 1px 1px #0a1035, -1px 0 1px #040721;
			}
			.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-install-now .editorsMinMaxWoo-install-button:hover {
				background: #272c51;
			}
			.notice.editorsMinMaxWoo-notice a.no-thanks {
				display: block;
				margin-top: 10px;
				color: #72777c;
				text-decoration: none;
			}

			.notice.editorsMinMaxWoo-notice a.no-thanks:hover {
				color: #444;
			}

			@media (max-width: 767px) {

				.notice.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner {
					display: block;
				}
				.notice.editorsMinMaxWoo-notice {
					padding: 20px !important;
				}
				.notice.editorsMinMaxWoo-noticee .editorsMinMaxWoo-notice-inner {
					display: block;
				}
				.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner .editorsMinMaxWoo-notice-content {
					display: block;
					padding: 0;
				}
				.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner .editorsMinMaxWoo-notice-icon {
					display: none;
				}

				.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner .editorsMinMaxWoo-install-now {
					margin-top: 20px;
					display: block;
					text-align: left;
				}

				.notice.editorsMinMaxWoo-notice .editorsMinMaxWoo-notice-inner .no-thanks {
					display: inline-block;
					margin-left: 15px;
				}
			}
			</style>
			<div class="notice updated editorsMinMaxWoo-notice">
				<div class="editorsMinMaxWoo-notice-inner">
					<div class="editorsMinMaxWoo-notice-icon">
						<?php /* translators: 1. Name */ ?>
						<img src="<?php echo esc_url( plugins_url( 'admin/images/min-max-logo.png', dirname( __FILE__ ))); ?>" alt="<?php printf( esc_attr__( '%s WordPress Plugin', 'min-and-max-quantity-for-woocommerce' ), esc_attr( $this->name ) ); ?>" />
					</div>
					<div class="editorsMinMaxWoo-notice-content">
						<?php /* translators: 1. Name */ ?>
						<h3><?php printf( esc_html__( 'Are you enjoying %s Plugin?', 'min-and-max-quantity-for-woocommerce' ), esc_html( $this->name ) ); ?></h3>
						<p>
							<?php /* translators: 1. Name, 2. Time */ ?>
							<?php printf( esc_html__( 'You have been using %1$s for %2$s now. Mind leaving a review to let us know know what you think? We\'d really appreciate it!', 'min-and-max-quantity-for-woocommerce' ), esc_html( $this->name ), esc_html( $time ) ); ?>
						</p>
					</div>
					<div class="editorsMinMaxWoo-install-now">
						<?php 
							$review_url = '';
							$review_url = esc_url( 'https://wordpress.org/plugins/min-and-max-quantity-for-woocommerce/#reviews' );
							printf( '<a href="%1$s" class="button button-primary editorsMinMaxWoo-install-button" target="_blank">%2$s</a>', esc_url( $review_url ), esc_html__( 'Leave a Review', 'min-and-max-quantity-for-woocommerce' ) ); 
						?>
						<a href="<?php echo esc_url( $no_bug_url ); ?>" class="no-thanks"><?php echo esc_html__( 'No thanks / I already have', 'min-and-max-quantity-for-woocommerce' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Set the plugin to no longer bug users if user asks not to be.
	 */
	public function set_no_bug() {

		// Bail out if not on correct page.
		// phpcs:ignore
		if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'editorsMinMaxWoo-feedback-nounce' ) || ! is_admin() || ! isset( $_GET[ $this->nobug_option ] ) || ! current_user_can( 'manage_options' ) ) ) {
			return;
		}

		add_site_option( $this->nobug_option, true );
	}
}

/*
* Instantiate the MinMaxWoo_User_Feedback class.
*/
new MinMaxWoo_User_Feedback(
	array(
		'slug'       => 'editorsmin_max_woo_plugin_feedback',
		'name'       => __( 'Minimum and Maximum Quantity for WooCommerce', 'min-and-max-quantity-for-woocommerce' ),
		'time_limit' => WEEK_IN_SECONDS,
	)
);
