<?php
// File: admin/ckeditor_upload.php
session_start();

// Cek login
if (!isset($_SESSION['useremail'])) {
    echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Unauthorized']]);
    exit;
}

// Konfigurasi upload
$upload_dir = '../frontend/images/pages/content/';
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

// Buat folder jika belum ada
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Proses upload
if (isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    
    // Validasi
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Upload error']]);
        exit;
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Invalid file type']]);
        exit;
    }
    
    if ($file['size'] > $max_size) {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'File too large']]);
        exit;
    }
    
    // Generate nama file unik
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = time() . '_' . uniqid() . '.' . $ext;
    $upload_path = $upload_dir . $new_filename;
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Return URL untuk CKEditor
        $url = '/senapenmas-2026/frontend/images/pages/content/' . $new_filename;
        
        echo json_encode([
            'uploaded' => 1,
            'fileName' => $new_filename,
            'url' => $url
        ]);
    } else {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Upload failed']]);
    }
} else {
    echo json_encode(['uploaded' => 0, 'error' => ['message' => 'No file uploaded']]);
}
?>