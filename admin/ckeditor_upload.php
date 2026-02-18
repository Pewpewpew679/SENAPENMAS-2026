<?php
// File: admin/ckeditor_upload.php

// 1. PENTING: Set header response JSON
header('Content-Type: application/json');

session_start();

// 2. Cek Login (Security)
// Ganti 'useremail' dengan nama session login Anda yang sebenarnya jika berbeda
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Unauthorized: Silahkan login terlebih dahulu.']]);
    exit;
}

// 3. Konfigurasi Upload
// Path Folder Fisik (Relative terhadap file ini)
$upload_dir = '../frontend/images/pages/content/'; 

// URL Akses Web (PENTING: Sesuaikan nama folder project 'senapenmas-2026' jika di server berubah)
$base_url = '/senapenmas-2026/frontend/images/pages/content/'; 

$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 5 * 1024 * 1024; // 5MB

// Buat folder jika belum ada
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// 4. Proses Upload
if (isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    
    // Cek Error System
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Upload error code: ' . $file['error']]]);
        exit;
    }
    
    // Validasi Tipe File (MIME Type)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_types)) {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Tipe file salah. Hanya JPG, PNG, GIF, WebP.']]);
        exit;
    }
    
    // Validasi Ukuran
    if ($file['size'] > $max_size) {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'File terlalu besar (Maks 5MB)']]);
        exit;
    }
    
    // Generate Nama File Unik
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = time() . '_' . uniqid() . '.' . $ext;
    $target_file = $upload_dir . $new_filename;
    
    // Pindahkan File
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        
        // URL yang akan dikirim kembali ke CKEditor
        $url = $base_url . $new_filename;

        echo json_encode([
            'uploaded' => 1,
            'fileName' => $new_filename,
            'url' => $url
        ]);

    } else {
        echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Gagal memindahkan file. Cek permission folder.']]);
    }
} else {
    echo json_encode(['uploaded' => 0, 'error' => ['message' => 'Tidak ada file yang dikirim.']]);
}
?>