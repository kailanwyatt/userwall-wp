<?php
/**
 * UserWall_WP_Base_Addon class
 *
 * @package UserWall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * UserWall_WP_Base_Addon class
 */
class UserWall_WP_Base_Addon {
	/**
	 * Addon name
	 *
	 * @var string
	 */
	protected $addon_name = '';
	/**
	 * Addon description
	 *
	 * @var string
	 */
	protected $addon_description = '';
	/**
	 * Addon author
	 *
	 * @var string
	 */
	protected $addon_author = '';
	/**
	 * Addon version
	 *
	 * @var string
	 */
	protected $addon_version = '';
	/**
	 * Addon ID
	 *
	 * @var string
	 */
	protected $addon_id = '';
	/**
	 * Addon file
	 *
	 * @var string
	 */
	public $file = '';
	/**
	 * Whether the addon is active or not
	 *
	 * @var bool
	 */
	protected $is_active = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->addon_name        = $this->get_name();
		$this->addon_description = $this->get_description();
		$this->addon_author      = $this->get_author();
		$this->addon_version     = $this->get_version();
		$this->addon_id          = $this->get_id();
		$this->file              = $this->get_file_path();
		$this->hooks();
		add_filter( 'userwall_wp_inline_js', array( $this, 'add_inline_js' ) );
	}

	/**
	 * Get the addon ID.
	 */
	public function get_id() {
		$this->addon_id;
	}

	/**
	 * Get the addon name.
	 */
	public function get_name() {
		return $this->addon_name;
	}

	/**
	 * Get the addon description.
	 */
	public function get_description() {
		return $this->addon_name;
	}

	/**
	 * Get the addon author.
	 */
	public function get_author() {
		return $this->addon_author;
	}

	/**
	 * Get the addon version.
	 */
	public function get_version() {
		return $this->addon_version;
	}

	/**
	 * Get the addon file path.
	 */
	public function get_file_path() {
		return __FILE__;
	}

	/**
	 * Activate the addon.
	 */
	public function activate_addon() {
		// Add addon-specific activation logic here.
	}

	/**
	 * Deactivate the addon.
	 */
	public function deactivate_addon() {
		// Add addon-specific deactivation logic here.
	}

	/**
	 * Check if the addon is currently active.
	 *
	 * @return bool Whether the addon is active or not.
	 */
	public function is_active() {
		return $this->is_active;
	}

	/**
	 * Add hooks for the addon.
	 */
	public function hooks() {
		// Add addon-specific hooks here.
	}

	/**
	 * This will render JS in the footer of the frontend / admin page where userwall are rendered or submitted.
	 */
	public function add_js() {
		// Add addon-specific JS here.
	}

	/**
	 * This will render JS in the footer of the admin page where userwall are rendered or submitted.
	 */
	public function add_admin_footer_js() {
		$current_screen = get_current_screen();
		if ( 'userwall-wp_page_userwall-wp-posts' === $current_screen->id ) {
			$this->add_js();
		}
	}

	/**
	 * Check if the addon is ready to be used.
	 *
	 * @return bool Whether the addon is ready to be used or not.
	 */
	public function is_ready() {
		return true;
	}

	/**
	 * Add inline JS for the addon.
	 *
	 * @param string $inline_js The inline JS.
	 * @return string The modified inline JS.
	 */
	public function add_inline_js( $inline_js ) {
		ob_start();
		$this->add_js();
		$inline_js .= ob_get_clean();
		return $inline_js;
	}
}
