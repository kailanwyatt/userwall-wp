<?php
class UserWall_WP_FileManager {
	private $upload_dir; // Directory path for file uploads
	private $post_type; // Post type (user or post)
	private $user_id; // User ID or group ID

	public function __construct( $id = '', $post_type = 'user' ) {
		$this->post_type  = $post_type;
		$this->user_id    = $id;
		$this->upload_dir = $this->generate_upload_directory_path();
	}

	// Method to generate the upload directory path based on post type and ID
	private function generate_upload_directory_path() {
		$upload_dir = wp_upload_dir();

		// The uploads directory path is stored in the 'path' element of the returned array
		$uploads_path = $upload_dir['basedir'];

		$directory = $uploads_path . '/userwall-wp/';
		if ( 'user' === $this->post_type ) {
			$directory .= 'users-uploads/';
		} elseif ( 'post' === $this->post_type ) {
			$directory .= 'group-uploads/';
		}
		$directory .= $this->user_id . '/';

		return $directory;
	}

	public function upload_file( $file = array() ) {
		// Ensure the upload directory exists
		if ( ! is_dir( $this->upload_dir ) ) {
			wp_mkdir_p( $this->upload_dir );
		}

		// Use pathinfo() to get the file name and extension
		$file_info = pathinfo( $file['name'] );

		$file_name      = $file_info['filename']; // File name without extension
		$file_extension = $file_info['extension']; // File extension

		// Generate a unique file name based on user/group ID and a timestamp
		$unique_filename = hash( 'sha256', $this->user_id . '_' . time() . '_' . sanitize_title( $file_name ) );

		// Construct the full path to the uploaded file
		$file_path = $this->upload_dir . $unique_filename . '.' . $file_extension;

		// Move the uploaded file to the specified path
		if ( move_uploaded_file( $file['tmp_name'], $file_path ) ) {
			return $file_path; // Return the file path on success
		} else {
			return false; // Return false on failure
		}
	}

	// Method to delete a specific file by its path
	public function delete_file( $file_path ) {
		if ( file_exists( $file_path ) ) {
			unlink( $file_path ); // Delete the file

			$parent_directory = dirname( $file_path );
			if ( $this->is_directory_empty( $parent_directory ) ) {
				// If the parent directory is empty, delete it
				rmdir( $parent_directory );
			}
			return true;
		}
		return false;
	}

	private function is_directory_empty( $dir ) {
		if ( ! is_readable( $dir ) ) {
			return null;
		}
		$handle = opendir( $dir );
		while ( false !== ( $entry = readdir( $handle ) ) ) {
			if ( $entry != '.' && $entry != '..' ) {
				closedir( $handle );
				return false;
			}
		}
		closedir( $handle );
		return true;
	}

	// Method to delete all user files by user ID
	public function delete_all_user_files() {
		$user_files_dir = $this->upload_dir . 'users-uploads/' . $this->user_id . '/';
		if ( file_exists( $user_files_dir ) ) {
			$this->delete_directory( $user_files_dir );
		}
	}

	// Method to delete all group files by group ID
	public function delete_all_group_files( $group_id ) {
		$group_files_dir = $this->upload_dir . 'group-uploads/' . $group_id . '/';
		if ( file_exists( $group_files_dir ) ) {
			$this->delete_directory( $group_files_dir );
		}
	}

	// Method to delete all post files by post ID
	public function delete_all_post_files( $post_id ) {
		$post_files_dir = $this->upload_dir . 'post-uploads/' . $post_id . '/';
		if ( file_exists( $post_files_dir ) ) {
			$this->delete_directory( $post_files_dir );
		}
	}

	// Recursive method to delete a directory and its contents
	private function delete_directory( $dir ) {
		if ( is_dir( $dir ) ) {
			$objects = scandir( $dir );
			foreach ( $objects as $object ) {
				if ( $object != '.' && $object != '..' ) {
					if ( is_dir( $dir . $object ) ) {
						$this->delete_directory( $dir . $object . '/' );
					} else {
						unlink( $dir . $object );
					}
				}
			}
			rmdir( $dir );
		}
	}

	public function get_file_url( $file_name, $post_id = '', $group_id = '' ) {
		if ( 'user' === $this->post_type && ! empty( $this->user_id ) ) {
			$directory = 'users-uploads/' . $this->user_id . '/';
		} elseif ( 'post' === $this->post_type && ! empty( $post_id ) ) {
			$directory = 'group-uploads/' . $group_id . '/';
		} else {
			return false; // Cannot determine the file directory
		}

		$upload_dir = wp_upload_dir();

		// The uploads directory path is stored in the 'path' element of the returned array
		$uploads_path = $upload_dir['basedir'];

		//$directory = $uploads_path . '/userwall-wp/';
		$file_path = $this->upload_dir . $file_name;
		if ( file_exists( $file_path ) ) {
			$upload_dir = wp_upload_dir();
			$base_url   = $upload_dir['baseurl'];
			return $base_url . '/userwall-wp/' . $directory . $file_name;
		}

		return false; // File does not exist
	}
}
