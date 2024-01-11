<?php
class Threads_WP_FileManager {
    private $upload_dir; // Directory path for file uploads
    private $post_type; // Post type (user or post)
    private $user_id; // User ID or group ID

    public function __construct( $id = '', $post_type = 'user' ) {
        $this->post_type = $post_type;
        $this->user_id = $id;
        $this->upload_dir = $this->generateUploadDirectoryPath();
    }

    // Method to generate the upload directory path based on post type and ID
    private function generateUploadDirectoryPath() {
        $upload_dir = wp_upload_dir();

        // The uploads directory path is stored in the 'path' element of the returned array
        $uploads_path = $upload_dir['basedir'];

        $directory = $uploads_path . '/threads-wp/';
        if ($this->post_type === 'user') {
            $directory .= 'users-uploads/';
        } elseif ($this->post_type === 'post') {
            $directory .= 'group-uploads/';
        }
        $directory .= $this->user_id . '/';

        return $directory;
    }

    public function uploadFile($file = array() ) {
        // Ensure the upload directory exists
        if (!is_dir($this->upload_dir)) {
            wp_mkdir_p($this->upload_dir);
        }

        // Generate a unique file name based on user/group ID and a timestamp
        $unique_filename = $this->user_id . '_' . time() . '_' . sanitize_title( $file['name'] );

        // Construct the full path to the uploaded file
        $file_path = $this->upload_dir . $unique_filename;

        // Move the uploaded file to the specified path
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return $file_path; // Return the file path on success
        } else {
            return false; // Return false on failure
        }
    }

    // Method to delete a specific file by its path
    public function deleteFile($file_path) {
        if (file_exists($file_path)) {
            unlink($file_path); // Delete the file
            return true;
        }
        return false;
    }

    // Method to delete all user files by user ID
    public function deleteAllUserFiles() {
        $user_files_dir = $this->upload_dir . 'users-uploads/' . $this->user_id . '/';
        if (file_exists($user_files_dir)) {
            $this->deleteDirectory($user_files_dir);
        }
    }

    // Method to delete all group files by group ID
    public function deleteAllGroupFiles($group_id) {
        $group_files_dir = $this->upload_dir . 'group-uploads/' . $group_id . '/';
        if (file_exists($group_files_dir)) {
            $this->deleteDirectory($group_files_dir);
        }
    }

    // Method to delete all post files by post ID
    public function deleteAllPostFiles($post_id) {
        $post_files_dir = $this->upload_dir . 'post-uploads/' . $post_id . '/';
        if (file_exists($post_files_dir)) {
            $this->deleteDirectory($post_files_dir);
        }
    }

    // Recursive method to delete a directory and its contents
    private function deleteDirectory($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . $object)) {
                        $this->deleteDirectory($dir . $object . '/');
                    } else {
                        unlink($dir . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
