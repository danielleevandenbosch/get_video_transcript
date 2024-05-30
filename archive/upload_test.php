<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHP settings for file uploads
ini_set('file_uploads', 'On');
ini_set('upload_max_filesize', '3G');
ini_set('post_max_size', '3.5G');
ini_set('max_file_uploads', '20');  // Allow up to 20 files to be uploaded simultaneously
ini_set('max_execution_time', '300');  // Increase max execution time to 300 seconds
ini_set('max_input_time', '300');  // Increase max input parsing time to 300 seconds
ini_set('memory_limit', '4G');  // Increase memory limit to 4G

function log_message($message) {
    $log_file = '/tmp/upload_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " " . $message . "\n", FILE_APPEND);
}

log_message("Script start");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    log_message("Handling POST request");

    if (isset($_FILES['file'])) {
        $max_size = 3.5 * 1024 * 1024 * 1024; // 3.5 GB in bytes
        log_message("File uploaded: " . $_FILES['file']['name'] . ", size: " . $_FILES['file']['size']);

        if ($_FILES['file']['size'] > $max_size) {
            log_message("File is too large. Maximum allowed size is 3.5 GB.");
            echo "File is too large. Maximum allowed size is 3.5 GB.";
            exit;
        }

        $upload_dir = '/tmp/';
        $uploaded_file = $upload_dir . basename($_FILES['file']['name']);

        // Log the temporary file name
        log_message("Temporary file: " . $_FILES['file']['tmp_name']);

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file)) {
            log_message("File moved to $uploaded_file");
            echo "File uploaded successfully to $uploaded_file";
        } else {
            log_message("Failed to upload file. Error code: " . $_FILES['file']['error']);
            echo "Failed to upload file.";
        }
    } else {
        log_message("No file uploaded.");
        echo "No file uploaded.";
    }
} else {
    log_message("Invalid request method.");
    echo "Invalid request method.";
}

log_message("Script end");
?>
