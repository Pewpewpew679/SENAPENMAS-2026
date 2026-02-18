<?php
ob_start();
session_start();

// Cek Login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include "includes/config.php";

/* =====================================
   1. PROSES SIMPAN FILE (ADD NEW)
===================================== */
if (isset($_POST['Simpan'])) {
    $event_id  = (int) $_POST['event_id'];
    $file_name = mysqli_real_escape_string($conn, $_POST['file_name']);
    $status    = $_POST['status']; 

    $file_upload = $_FILES['file_download']['name'];
    $tmp         = $_FILES['file_download']['tmp_name'];
    
    // Simpan nama asli
    $original_name = $file_upload;
    
    // Penamaan file unik untuk storage
    $new_name = time() . "_" . str_replace(' ', '_', $file_upload);
    
    // Folder tujuan
    $folder   = "upload/unduhan/"; 

    if (!is_dir($folder)) { mkdir($folder, 0777, true); }

    if (move_uploaded_file($tmp, $folder . $new_name)) {
        // Simpan nama file unik dan nama asli
        $query = "INSERT INTO downloadablefiles (event_id, file_name, file_link, original_filename, status) 
                  VALUES ('$event_id', '$file_name', '$new_name', '$original_name', '$status')";
        mysqli_query($conn, $query);
    }

    header("Location: inputdownloadablefiles.php?msg=success");
    exit;
}

/* =====================================
   2. PROSES UPDATE FILE (EDIT)
===================================== */
if (isset($_POST['Update'])) {
    $file_id   = (int) $_POST['file_id'];
    $event_id  = (int) $_POST['event_id'];
    $file_name = mysqli_real_escape_string($conn, $_POST['file_name']);
    $status    = $_POST['status'];
    $old_link  = $_POST['old_link'];
    $old_original = $_POST['old_original'];

    $final_link = $old_link;
    $final_original = $old_original;
    $folder     = "upload/unduhan/";

    // Jika ada file baru diupload
    if ($_FILES['file_download']['name'] != "") {
        $file_upload = $_FILES['file_download']['name'];
        $tmp         = $_FILES['file_download']['tmp_name'];
        
        $final_original = $file_upload;
        $new_name = time() . "_" . str_replace(' ', '_', $file_upload);

        if (move_uploaded_file($tmp, $folder . $new_name)) {
            $final_link = $new_name;
            
            // Hapus file fisik lama
            if (file_exists($folder . $old_link) && $old_link != "") {
                unlink($folder . $old_link);
            }
        }
    }

    $query = "UPDATE downloadablefiles SET 
              event_id='$event_id', 
              file_name='$file_name', 
              file_link='$final_link',
              original_filename='$final_original',
              status='$status' 
              WHERE file_id='$file_id'";
    
    mysqli_query($conn, $query);

    header("Location: inputdownloadablefiles.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include "bagiankode/head.php"; ?>

<body class="sb-nav-fixed">
<?php include "bagiankode/menunav.php"; ?>

<div id="layoutSidenav">
    <?php include "bagiankode/menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Downloadable Files</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addDownloadModal">
                            <i class="bi bi-plus-lg"></i> Add File
                        </button>

                        <div class="table-responsive">
                            <table id="downloadTable" class="table table-sm table-bordered table-hover align-middle" style="font-size: 0.9rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30">No</th>
                                        <th width="250">File Name</th>
                                        <th width="150">Link</th>
                                        <th width="200">Event</th>
                                        <th width="100">Status</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "
                                        SELECT d.*, e.event_name 
                                        FROM downloadablefiles d
                                        LEFT JOIN events e ON d.event_id = e.event_id
                                        ORDER BY d.file_id DESC
                                    ");
                                    
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        $statusText = '';
                                        if ($row['status'] == 'Publish') {
                                            $statusText = '<span class="text-success">Publish</span>';
                                        } else {
                                            $statusText = '<span class="text-secondary">Unpublish</span>';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['file_name']) ?></td>
                                        <td>
                                            <a href="download.php?id=<?= $row['file_id'] ?>" class="text-decoration-none" style="font-size: 0.9em;">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                                        <td><?= $statusText ?></td>
                                        <td>
                                            <a href="#" class="text-primary me-2 btn-edit" style="text-decoration:none;"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#editDownloadModal"
                                               data-id="<?= $row['file_id'] ?>"
                                               data-event="<?= $row['event_id'] ?>"
                                               data-name="<?= htmlspecialchars($row['file_name']) ?>"
                                               data-link="<?= $row['file_link'] ?>"
                                               data-original="<?= htmlspecialchars($row['original_filename']) ?>"
                                               data-status="<?= $row['status'] ?>">
                                               Edit
                                            </a>
                                            <a href="hapusdownloadablefiles.php?id=<?= $row['file_id'] ?>" 
                                               class="text-primary" style="text-decoration:none;"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?')">
                                               Delete
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include "bagiankode/footer.php"; ?>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addDownloadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Downloadable File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Event *</label>
                        <select name="event_id" class="form-select" required>
                            <option value="">Pilih Event</option>
                            <?php
                            $q_event = mysqli_query($conn, "SELECT event_id, event_name FROM events ORDER BY event_id DESC");
                            while($evt = mysqli_fetch_assoc($q_event)){
                                echo '<option value="'.$evt['event_id'].'">'.$evt['event_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File Name*</label>
                        <input type="text" name="file_name" class="form-control" placeholder="Ex: Template Paper" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload File (PDF/DOC)*</label>
                        <input type="file" name="file_download" class="form-control" required>
                        <small class="text-muted">Max size recommended: 10MB</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status*</label>
                        <select name="status" class="form-select">
                            <option value="Publish">Publish</option>
                            <option value="Unpublish">Unpublish</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="Simpan" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editDownloadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="file_id" id="edit-id">
                    <input type="hidden" name="old_link" id="edit-old-link">
                    <input type="hidden" name="old_original" id="edit-old-original">
                    
                    <div class="mb-3">
                        <label class="form-label">Select Event*</label>
                        <select name="event_id" id="edit-event" class="form-select" required>
                            <option value="">-- Choose Event --</option>
                            <?php
                            mysqli_data_seek($q_event, 0); 
                            while($evt = mysqli_fetch_assoc($q_event)){
                                echo '<option value="'.$evt['event_id'].'">'.$evt['event_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">File Name*</label>
                        <input type="text" name="file_name" id="edit-name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Update File</label>
                        <input type="file" name="file_download" class="form-control">
                        <small class="text-muted d-block mt-1">Current: <span id="current-file-label" class="fw-bold text-primary"></span></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status*</label>
                        <select name="status" id="edit-status" class="form-select">
                            <option value="Publish">Publish</option>
                            <option value="Unpublish">Unpublish</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="Update" class="btn btn-primary btn-sm">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "bagiankode/jsscript.php"; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("downloadTable")) {
        new simpleDatatables.DataTable("#downloadTable", { perPage: 10 });
    }

    // Logic Edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit-id').value          = this.dataset.id;
            document.getElementById('edit-old-link').value    = this.dataset.link;
            document.getElementById('edit-old-original').value = this.dataset.original;
            document.getElementById('edit-event').value       = this.dataset.event;
            document.getElementById('edit-name').value        = this.dataset.name;
            document.getElementById('edit-status').value      = this.dataset.status;
            
            // Preview nama file asli
            const originalName = this.dataset.original;
            if (originalName) {
                document.getElementById('current-file-label').innerText = originalName;
            } else {
                document.getElementById('current-file-label').innerText = "-";
            }
        });
    });
});
</script>
</body>
</html>