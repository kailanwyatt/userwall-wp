<?php
/**
 * UserWallWP_Addon_Groups class
 *
 * @package Userwall_WP
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Userwall_Groups_List_Table class
 */
class Userwall_Groups_List_Table extends WP_List_Table {
	/**
	 * Constructor, we override the parent to pass our own arguments.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Group', 'userwall-wp' ),
				'plural'   => __( 'Groups', 'userwall-wp' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Retrieve groups data from the database
	 *
	 * @param int $per_page Number of items per page.
	 * @param int $page_number Current page number.
	 *
	 * @return mixed
	 */
	public static function get_groups( $per_page = 25, $page_number = 1 ) {
		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}userwall_groups";
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
			$order_by = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : '';
			$sql     .= ' ORDER BY ' . esc_sql( $order_by );
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
			$order = ! empty( $_REQUEST['order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'ASC';
			$sql  .= ' ' . esc_sql( $order );
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.PlaceholderNoop, WordPress.DB.PreparedSQL.PlaceholderNoop
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}

	/**
	 * Delete a group record.
	 *
	 * @param int $id group ID.
	 */
	public static function delete_group( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}userwall_groups",
			array( 'group_id' => $id ),
			array( '%d' )
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}userwall_groups";
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no group data is available */
	public function no_items() {
		esc_html_e( 'No groups available.', 'userwall-wp' );
	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array  $item    The current item.
	 * @param string $column_name The name of the column.
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'group_name':
			case 'group_description':
			case 'creation_date':
				return $item[ $column_name ];
			case 'creator_user_id':
				$user = get_user_by( 'ID', $item[ $column_name ] );
				return ! empty( $user ) ? $user->display_name : $item[ $column_name ];
		}
	}

	/**
	 *  Define the columns that are going to be used in the table
	 *
	 * @return array $columns, the array of columns to use with the table
	 */
	public function get_columns() {
		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'group_name'        => __( 'Name', 'userwall-wp' ),
			'group_description' => __( 'Description', 'userwall-wp' ),
			'creation_date'     => __( 'Date', 'userwall-wp' ),
			'creator_user_id'   => __( 'Creator By', 'userwall-wp' ),
		);

		return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'group_name'    => array( 'group_name', true ),
			'creation_date' => array( 'creation_date', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action.
	 *
	 * @return array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		// Ensure that $sortable is defined as an associative array.
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/** Process bulk action */

		$per_page     = $this->get_items_per_page( 'groups_per_page', 25 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // WE have to calculate the total number of items.
				'per_page'    => $per_page, // WE have to determine how many items to show on a page.
			)
		);

		$this->items = self::get_groups( $per_page, $current_page );
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item The current item.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="group[]" value="%s" />',
			$item['group_id']
		);
	}
}
