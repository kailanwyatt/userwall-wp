<?php
/**
 * Class for Userwall Comments List Table
 *
 * @package Userwall_WP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class for Userwall Comments List Table
 */
class Userwall_Comments_List_Table extends WP_List_Table {

	/**
	 * Constructor, we override the parent to pass our own arguments.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Comment', 'userwall-wp' ),
				'plural'   => __( 'Comments', 'userwall-wp' ),
				'ajax'     => false,
			)
		);
	}

	/**
	 * Text displayed when no comment data is available.
	 */
	public function no_items() {
		esc_html_e( 'No comments available.', 'userwall-wp' );
	}

	/**
	 * Define the columns that are going to be used in the table.
	 *
	 * @return array $columns, the array of columns to use with the table.
	 */
	public function get_columns() {
		$columns = array(
			'cb'              => '<input type="checkbox" />',
			'comment_content' => __( 'Content', 'userwall-wp' ),
			'comment_date'    => __( 'Date', 'userwall-wp' ),
			'user_id'         => __( 'User ID', 'userwall-wp' ),
			'post_id'         => __( 'Post ID', 'userwall-wp' ),
		);
		return $columns;
	}

	/**
	 * Render a column when no specific column method exists.
	 *
	 * @param array  $item    The current item.
	 * @param string $column_name The name of the column.
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'comment_content':
			case 'comment_date':
			case 'user_id':
			case 'post_id':
				return $item[ $column_name ];
		}
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET.
	 *
	 * @param array $a The first item to compare.
	 * @param array $b The second item to compare.
	 * @return mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults.
		$orderby = 'comment_date';
		$order   = 'desc';

		// If orderby is set, use this as the sort column.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['orderby'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
		}

		// If order is set use this as the order.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['order'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order = sanitize_text_field( wp_unslash( $_GET['order'] ) );
		}

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( 'asc' === $order ) {
			return $result;
		}
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements.
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array(); // You can hide columns if you want.
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Retrieve the comment data.
		$data = $this->get_comments_data();

		usort( $data, array( &$this, 'sort_data' ) );

		$per_page     = 5;
		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;
	}

	/**
	 * Define the sortable columns
	 *
	 * @return array $sortable_columns, the array of columns that can be sorted by the user.
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'comment_date' => array( 'comment_date', false ),
			'user_id'      => array( 'user_id', false ),
			'post_id'      => array( 'post_id', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Retrieve the comment data from the database.
	 *
	 * @return array $data, the array of comment data.
	 */
	private function get_comments_data() {
		global $wpdb;
		$sql = $wpdb->prepare( 'SELECT * FROM %i', $wpdb->prefix . 'userwall_comments' );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$data = $wpdb->get_results( $sql, ARRAY_A );
		return $data;
	}

	/**
	 * Checkbox column for bulk actions.
	 *
	 * @param array $item The current item.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="comment[]" value="%s" />',
			$item['comment_id']
		);
	}
}
