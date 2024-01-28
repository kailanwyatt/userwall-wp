<?php
/**
 * Class for Userwall Comments List Table
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

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
		_e( 'No comments available.', 'userwall-wp' );
	}

	/**
	 * Define the columns that are going to be used in the table.
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
	 * @param array $item
	 * @param string $column_name
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'comment_content':
			case 'comment_date':
			case 'user_id':
			case 'post_id':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET.
	 * @return mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'comment_date';
		$order   = 'desc';

		// If orderby is set, use this as the sort column.
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order.
		if ( ! empty( $_GET['order'] ) ) {
			$order = $_GET['order'];
		}

		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( $order === 'asc' ) {
			return $result;
		}

		return -$result;
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

		$perPage     = 5;
		$currentPage = $this->get_pagenum();
		$totalItems  = count( $data );

		$this->set_pagination_args(
			array(
				'total_items' => $totalItems,
				'per_page'    => $perPage,
			)
		);

		$data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );

		$this->items = $data;
	}

	/**
	 * Define the sortable columns
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
	 * @return array $data, the array of comment data.
	 */
	private function get_comments_data() {
		global $wpdb;
		$sql  = "SELECT * FROM {$wpdb->prefix}userwall_comments";
		$data = $wpdb->get_results( $sql, ARRAY_A );
		return $data;
	}

	/**
	 * Checkbox column for bulk actions.
	 * @param array $item
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="comment[]" value="%s" />',
			$item['comment_id']
		);
	}
}
