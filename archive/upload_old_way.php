<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHP settings for file uploads
ini_set('file_uploads', 'On');
ini_set('upload_max_filesize', '3G');
ini_set('post_max_size', '3.5G');
ini_set('max_file_uploads', '20');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');
ini_set('memory_limit', '4G');

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

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploaded_file)) {
            log_message("File moved to $uploaded_file");
            $python_script = '/var/www/html/it/get_video_transcript/transcribe.py';
            $command = escapeshellcmd("python3 " . $python_script . " " . $uploaded_file);
            log_message("Running command: $command");
            $output = shell_exec($command . " 2>&1"); // Capture both stdout and stderr
            log_message("Command output: $output");
            $transcript_file = trim($output);

            if (strpos($output, 'Error:') !== false) {
                log_message("Failed to generate transcript. Error: " . $output);
                echo "Failed to generate transcript. Error: " . htmlspecialchars($output);
                exit;
            }

            if (file_exists($transcript_file)) {
                log_message("Transcript file exists: $transcript_file");
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . basename($transcript_file));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($transcript_file));
                readfile($transcript_file);
                exit;
            } else {
                log_message("Transcript file does not exist: $transcript_file");
                echo "Failed to generate transcript.";
            }
        } else {
            log_message("Failed to upload file.");
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