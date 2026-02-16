<?php

include "includes/config.php";
if (session_status() == PHP_SESSION_NONE) session_start();

// Restructure sponsor_content table untuk per-event
$tblCheck = mysqli_query($conn, "SHOW TABLES LIKE 'sponsor_content'");
if ($tblCheck && mysqli_num_rows($tblCheck) > 0) {
    $colCheck = mysqli_query($conn, "SHOW COLUMNS FROM sponsor_content LIKE 'event_id'");
    if (!$colCheck || mysqli_num_rows($colCheck) == 0) {
        // Old structure (global) - rebuild for per-event
        mysqli_query($conn, "DROP TABLE sponsor_content");
        mysqli_query($conn, "CREATE TABLE sponsor_content (id INT AUTO_INCREMENT PRIMARY KEY, event_id INT NOT NULL UNIQUE, content TEXT DEFAULT '', status TINYINT(1) NOT NULL DEFAULT 1)");
    }
} else {
    mysqli_query($conn, "CREATE TABLE sponsor_content (id INT AUTO_INCREMENT PRIMARY KEY, event_id INT NOT NULL UNIQUE, content TEXT DEFAULT '', status TINYINT(1) NOT NULL DEFAULT 1)");
}

// Auto-populate sponsor_content untuk setiap event yang punya sponsor
$ews_res = mysqli_query($conn, "SELECT DISTINCT s.event_id FROM sponsors s WHERE s.event_id IS NOT NULL");
if ($ews_res) {
    while ($r = mysqli_fetch_assoc($ews_res)) {
        $eid = intval($r['event_id']);
        $check = mysqli_query($conn, "SELECT id FROM sponsor_content WHERE event_id = $eid");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO sponsor_content (event_id, content, status) VALUES ($eid, '', 1)");
        }
    }
}

// Ambil semua event settings (untuk admin UI)
$event_settings = [];
$es_res = mysqli_query($conn, "SELECT sc.*, e.event_name, e.event_year FROM sponsor_content sc JOIN events e ON sc.event_id = e.event_id ORDER BY e.event_year DESC, e.event_name ASC");
if ($es_res) {
    while ($r = mysqli_fetch_assoc($es_res)) { $event_settings[] = $r; }
}

// Simpan deskripsi + status per event
if (isset($_POST['SaveEventDesc'])) {
    $event_id = intval($_POST['event_id']);
    $desc = mysqli_real_escape_string($conn, $_POST['event_description']);
    $status = intval($_POST['event_status']);
    mysqli_query($conn, "UPDATE sponsor_content SET content = '$desc', status = $status WHERE event_id = $event_id");
    $_SESSION['sponsor_success'] = 'Pengaturan event sponsor berhasil disimpan.';
    header("Location: inputsponsor.php");
    exit;
}

// Ambil daftar events untuk dropdown (value = event_name)
$events = [];
$events_res = mysqli_query($conn, "SELECT event_id, event_name FROM events ORDER BY event_name");
if ($events_res) {
    while ($er = mysqli_fetch_assoc($events_res)) { $events[] = $er; }
}

// Cek apakah ada kolom order_number (opsional)
$col_order = mysqli_query($conn, "SHOW COLUMNS FROM sponsors LIKE 'order_number'");
$use_order = ($col_order && mysqli_num_rows($col_order) > 0);

// Cek apakah sponsors menggunakan event_id (relasi) atau event_name tersimpan secara langsung
$col_event = mysqli_query($conn, "SHOW COLUMNS FROM sponsors LIKE 'event_id'");
$use_event_id = ($col_event && mysqli_num_rows($col_event) > 0);

/* =====================================
   1. PROSES SIMPAN SPONSOR (ADD NEW)
===================================== */
if (isset($_POST['Simpan'])) {
    $event  = isset($_POST['event']) ? mysqli_real_escape_string($conn, $_POST['event']) : '';
    $name  = mysqli_real_escape_string($conn, $_POST['sponsor_name']);
    $link   = mysqli_real_escape_string($conn, $_POST['link']);
    $order  = isset($_POST['order_number']) ? mysqli_real_escape_string($conn, $_POST['order_number']) : ''; // order_number from form

    $image_name = isset($_FILES['image_sponsor']['name']) ? $_FILES['image_sponsor']['name'] : '';
    $tmp        = isset($_FILES['image_sponsor']['tmp_name']) ? $_FILES['image_sponsor']['tmp_name'] : '';
    $ext        = $image_name ? pathinfo($image_name, PATHINFO_EXTENSION) : '';

    if ($image_name) {
        $new_name = time() . "_" . uniqid() . "." . $ext;
        $folder   = "images/";

        if (move_uploaded_file($tmp, $folder . $new_name)) {
            if ($use_event_id) {
                $event_sql = $event === '' ? "NULL" : intval($event);
                if ($use_order) {
                    $sql = "INSERT INTO sponsors (sponsor_name, sponsor_logo, website_link, order_number, event_id) VALUES ('$name', '$new_name', '$link', '$order', $event_sql)";
                } else {
                    $sql = "INSERT INTO sponsors (sponsor_name, sponsor_logo, website_link, event_id) VALUES ('$name', '$new_name', '$link', $event_sql)";
                }
            } else {
                $event_sql = $event === '' ? "NULL" : "'" . mysqli_real_escape_string($conn,$event) . "'";
                if ($use_order) {
                    $sql = "INSERT INTO sponsors (sponsor_name, sponsor_logo, website_link, order_number, event_name) VALUES ('$name', '$new_name', '$link', '$order', $event_sql)";
                } else {
                    $sql = "INSERT INTO sponsors (sponsor_name, sponsor_logo, website_link, event_name) VALUES ('$name', '$new_name', '$link', $event_sql)";
                }
            }

            $res = mysqli_query($conn, $sql);
            if (!$res) {
                $_SESSION['sponsor_error'] = mysqli_error($conn);
            } else {
                $_SESSION['sponsor_success'] = 'Sponsor berhasil ditambahkan.';
            }
        }
    }

    header("Location: inputsponsor.php");
    exit;
} 

/* =====================================
   2. PROSES UPDATE SPONSOR (EDIT)
===================================== */
if (isset($_POST['Update'])) {
    $old_image = mysqli_real_escape_string($conn, $_POST['old_image'] ?? '');
    $sponsor_id = isset($_POST['sponsor_id']) ? intval($_POST['sponsor_id']) : 0;
    $where = $sponsor_id ? "sponsor_id={$sponsor_id}" : "sponsor_logo='{$old_image}'";

    $name      = mysqli_real_escape_string($conn, $_POST['sponsor_name']);
    $link      = mysqli_real_escape_string($conn, $_POST['link']);
    $event     = isset($_POST['event']) ? mysqli_real_escape_string($conn, $_POST['event']) : '';
    $order     = isset($_POST['order_number']) ? mysqli_real_escape_string($conn, $_POST['order_number']) : '';

    if (isset($_FILES['image_sponsor']) && $_FILES['image_sponsor']['name'] != "") {
        $image_name = $_FILES['image_sponsor']['name'];
        $tmp        = $_FILES['image_sponsor']['tmp_name'];
        $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_name   = time() . "_" . uniqid() . "." . $ext;
        
        move_uploaded_file($tmp, "images/" . $new_name);
        // Hapus file fisik lama
        if ($old_image && file_exists("images/" . $old_image)) {
            @unlink("images/" . $old_image);
        }

        if ($use_event_id) {
            $event_sql = $event === '' ? "NULL" : intval($event);
            if ($use_order) {
                $sql = "UPDATE sponsors SET sponsor_logo='$new_name', sponsor_name='$name', website_link='$link', order_number='$order', event_id=$event_sql WHERE {$where}";
            } else {
                $sql = "UPDATE sponsors SET sponsor_logo='$new_name', sponsor_name='$name', website_link='$link', event_id=$event_sql WHERE {$where}";
            }
        } else {
            $event_sql = $event === '' ? "NULL" : "'" . mysqli_real_escape_string($conn,$event) . "'";
            if ($use_order) {
                $sql = "UPDATE sponsors SET sponsor_logo='$new_name', sponsor_name='$name', website_link='$link', order_number='$order', event_name=$event_sql WHERE {$where}";
            } else {
                $sql = "UPDATE sponsors SET sponsor_logo='$new_name', sponsor_name='$name', website_link='$link', event_name=$event_sql WHERE {$where}";
            }
        }
    } else {
        if ($use_event_id) {
            $event_sql = $event === '' ? "NULL" : intval($event);
            if ($use_order) {
                $sql = "UPDATE sponsors SET sponsor_name='$name', website_link='$link', order_number='$order', event_id=$event_sql WHERE {$where}";
            } else {
                $sql = "UPDATE sponsors SET sponsor_name='$name', website_link='$link', event_id=$event_sql WHERE {$where}";
            }
        } else {
            $event_sql = $event === '' ? "NULL" : "'" . mysqli_real_escape_string($conn,$event) . "'";
            if ($use_order) {
                $sql = "UPDATE sponsors SET sponsor_name='$name', website_link='$link', order_number='$order', event_name=$event_sql WHERE {$where}";
            } else {
                $sql = "UPDATE sponsors SET sponsor_name='$name', website_link='$link', event_name=$event_sql WHERE {$where}";
            }
        }
    }

    $res = mysqli_query($conn, $sql);
    if (!$res) {
        $_SESSION['sponsor_error'] = mysqli_error($conn);
    } else {
        $_SESSION['sponsor_success'] = 'Sponsor berhasil diperbarui.';
    }

    header("Location: inputsponsor.php");
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
                <h1 class="mt-4">Sponsor</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <!-- TOP BAR: Add Sponsor + Event Dropdown + Status -->
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSponsorModal">
                                <i class="bi bi-plus-lg"></i> Add Sponsor
                            </button>

                            <?php if (!empty($event_settings)): ?>
                            <select id="event-select" class="form-select form-select-sm" style="width: auto; min-width: 200px;">
                                <?php foreach ($event_settings as $i => $es): ?>
                                    <option value="<?= $es['event_id'] ?>" <?= $i === 0 ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($es['event_name']) ?> (<?= $es['event_year'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select id="event-status" class="form-select form-select-sm" style="width: auto;">
                                <option value="1">Publish</option>
                                <option value="0">Unpublish</option>
                            </select>

                            <span id="status-badge"></span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($_SESSION['sponsor_error'])): ?>
                            <div class="alert alert-danger alert-sm"><?= htmlspecialchars($_SESSION['sponsor_error']) ?></div>
                            <?php unset($_SESSION['sponsor_error']); ?>
                        <?php endif; ?>
                        <?php if (!empty($_SESSION['sponsor_success'])): ?>
                            <div class="alert alert-success alert-sm"><?= htmlspecialchars($_SESSION['sponsor_success']) ?></div>
                            <?php unset($_SESSION['sponsor_success']); ?>
                        <?php endif; ?>

                        <!-- SPONSOR TABLE -->
                        <div class="table-responsive">
                            <table id="sponsorTable" class="table table-sm table-bordered table-hover align-middle" style="font-size: 0.9rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30">No</th>
                                        <th width="80">Image</th>
                                        <th width="200">Sponsor Name</th>
                                        <th width="180">Event</th>
                                        <th width="150" style="max-width: 150px;">Link</th>
                                        <th width="100">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    if ($use_event_id) {
                                        $query = mysqli_query($conn, "SELECT s.*, e.event_name FROM sponsors s LEFT JOIN events e ON s.event_id = e.event_id ORDER BY COALESCE(s.order_number, 9999) ASC, s.sponsor_name ASC");
                                    } else {
                                        $query = mysqli_query($conn, "SELECT * FROM sponsors ORDER BY COALESCE(order_number, 9999) ASC, sponsor_name ASC");
                                    }

                                    if ($query) {
                                        while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr data-event-id="<?= htmlspecialchars($row['event_id'] ?? '') ?>">
                                        <td><?= $no++ ?></td>
                                        <td><img src="images/<?= htmlspecialchars($row['sponsor_logo']) ?>" width="90" class="img-thumbnail"></td>
                                        <td><?= htmlspecialchars($row['sponsor_name']) ?></td>
                                        <td><?= htmlspecialchars($use_event_id ? ($row['event_name'] ?? ($row['event_id'] ?? '-')) : ($row['event_name'] ?? '-')) ?></td>
                                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><a href="<?= htmlspecialchars($row['website_link']) ?>" target="_blank" title="<?= htmlspecialchars($row['website_link']) ?>"><?= htmlspecialchars($row['website_link']) ?></a></td>
                                        <td>
                                            <a href="javascript:void(0)" class="btn-edit me-2" href="#"
                                               style="text-decoration: none; margin-right: 30px;"
                                               data-bs-toggle="modal" data-bs-target="#editSponsorModal"
                                               data-id="<?= htmlspecialchars($row['sponsor_id']) ?>"
                                               data-image="<?= htmlspecialchars($row['sponsor_logo']) ?>"
                                               data-sponsor-name="<?= htmlspecialchars($row['sponsor_name']) ?>"
                                               data-event="<?= $use_event_id ? htmlspecialchars($row['event_id'] ?? '') : htmlspecialchars($row['event_name'] ?? '') ?>"
                                               data-link="<?= htmlspecialchars($row['website_link']) ?>"
                                               data-order="<?= htmlspecialchars($row['order_number'] ?? '') ?>">Edit</a>
                                            <a href="hapussponsor.php?image=<?= htmlspecialchars($row['sponsor_logo']) ?>" style="text-decoration: none;"
                                               onclick="return confirm('Hapus data ini?')">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="6">Terjadi kesalahan database: '.htmlspecialchars(mysqli_error($conn)).'</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- EVENT DESCRIPTION -->
                        <?php if (!empty($event_settings)): ?>
                        <div id="event-settings-section" style="margin-top: 20px; border-top: 1px solid #dee2e6; padding-top: 20px;">
                            <form method="POST">
                                <input type="hidden" name="event_id" id="form-event-id" value="">
                                <input type="hidden" name="event_status" id="form-event-status" value="1">
                                <h6 class="mb-3"><i class="bi bi-card-text"></i> Event Description</h6>
                                <textarea name="event_description" id="editor_sponsor"></textarea>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" name="SaveEventDesc" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-lg"></i> Save Event Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </main>
        <?php include "bagiankode/footer.php"; ?>
    </div>
</div>


<div class="modal fade" id="addSponsorModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Sponsor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Event *</label>
                        <select name="event" class="form-select" required>
                            <option value="">Pilih Event</option>
                            <?php foreach ($events as $e): ?>
                                <option value="<?= $use_event_id ? htmlspecialchars($e['event_id']) : htmlspecialchars($e['event_name']) ?>"><?= htmlspecialchars($e['event_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sponsor Name *</label>
                        <input type="text" name="sponsor_name" class="form-control" required>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="text" name="link" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image (JPG/PNG) *</label>
                        <input type="file" name="image_sponsor" class="form-control" accept=".jpg,.jpeg,.png" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order Number</label>
                        <input type="text" name="order_number" class="form-control">
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


<div class="modal fade" id="editSponsorModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sponsor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="old_image" id="edit-old-image">
                    <input type="hidden" name="sponsor_id" id="edit-sponsor-id">
                    <div class="mb-3">
                        <label class="form-label">Event</label>
                        <select name="event" id="edit-event" class="form-select">
                            <option value="">Pilih Event</option>
                            <?php foreach ($events as $e): ?>
                                <option value="<?= $use_event_id ? htmlspecialchars($e['event_id']) : htmlspecialchars($e['event_name']) ?>"><?= htmlspecialchars($e['event_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sponsor Name *</label>
                        <input type="text" name="sponsor_name" id="edit-sponsor-name" class="form-control" required>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="text" name="link" id="edit-link" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Current Image</label>
                        <img src="" id="edit-preview" width="120" class="img-thumbnail mb-2">
                        <input type="file" name="image_sponsor" class="form-control" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order Number</label>
                        <input type="text" name="order_number" id="edit-order" class="form-control">
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

<script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>

<input type="file" id="hidden-image-upload" style="display: none;" accept="image/jpeg, image/png, image/gif, image/webp">

<script>
    CKEDITOR.config.versionCheck = false;
    CKEDITOR.addCss('img { max-width: 100%; height: auto !important; }');
    CKEDITOR.addCss('.cke_widget_wrapper { max-width: 100% !important; }');
    CKEDITOR.addCss('.image-left { display: block !important; margin-left: 0 !important; margin-right: auto !important; margin-bottom: 10px !important; text-align: left !important; clear: both !important; }');
    CKEDITOR.addCss('.image-right { display: block !important; margin-left: auto !important; margin-right: 0 !important; margin-bottom: 10px !important; text-align: right !important; clear: both !important; }');
    CKEDITOR.addCss('.image-center { display: block !important; margin-left: auto !important; margin-right: auto !important; margin-bottom: 10px !important; text-align: center !important; clear: both !important; }');
    CKEDITOR.addCss('p { margin-top: 0; margin-bottom: 1rem; }');

    function createCustomEditor(elementId) {
        if (CKEDITOR.instances[elementId]) {
            CKEDITOR.instances[elementId].destroy(true);
        }
        CKEDITOR.replace(elementId, {
            height: 300,
            width: '100%',
            extraPlugins: 'uploadimage,image2,widget,lineutils,widgetselection,notification,filetools',
            removePlugins: 'image,easyimage,cloudservices',
            image2_alignClasses: ['image-left', 'image-center', 'image-right'],
            image2_disableResizer: false,
            allowedContent: true,
            toolbar: [
                { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
                '/',
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'insert', items: [ 'BtnCustomImage', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
                '/',
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
            ],
            on: {
                pluginsLoaded: function() {
                    var editor = this;
                    editor.addCommand('cmdOpenUpload', {
                        exec: function(editor) {
                            var input = document.getElementById('hidden-image-upload');
                            input.setAttribute('data-target', elementId);
                            input.click();
                        }
                    });
                    editor.ui.addButton('BtnCustomImage', {
                        label: 'Upload Gambar',
                        command: 'cmdOpenUpload',
                        toolbar: 'insert',
                        icon: 'https://cdn-icons-png.flaticon.com/512/3342/3342137.png'
                    });
                }
            }
        });
    }

    // Data per-event dari PHP
    var eventData = <?php echo json_encode(array_map(function($es) {
        return ['event_id' => $es['event_id'], 'content' => $es['content'], 'status' => $es['status']];
    }, $event_settings)); ?>;

    var eventDataMap = {};
    eventData.forEach(function(item) { eventDataMap[item.event_id] = item; });

    var editorInitialized = false;

    function filterTable(eventId) {
        var rows = document.querySelectorAll('#sponsorTable tbody tr');
        rows.forEach(function(row) {
            if (eventId === 'all' || row.getAttribute('data-event-id') === eventId) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function loadEventData(eventId) {
        var data = eventDataMap[eventId];
        if (!data) return;

        // Update status dropdown & sync hidden field
        document.getElementById('event-status').value = data.status;
        document.getElementById('form-event-id').value = eventId;
        document.getElementById('form-event-status').value = data.status;

        // Update status badge
        var badge = document.getElementById('status-badge');
        if (data.status == 1) {
            badge.innerHTML = '<span class="badge bg-primary">Publish</span>';
        } else {
            badge.innerHTML = '<span class="badge bg-danger">Unpublish</span>';
        }

        // Load CKEditor content
        if (editorInitialized && CKEDITOR.instances['editor_sponsor']) {
            CKEDITOR.instances['editor_sponsor'].setData(data.content || '');
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Fungsi klik tombol Edit sponsor
        const editButtons = document.querySelectorAll('.btn-edit');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const id     = this.getAttribute('data-id');
                const image  = this.getAttribute('data-image');
                const sponsor_name  = this.getAttribute('data-sponsor-name');
                const event  = this.getAttribute('data-event');
                const link   = this.getAttribute('data-link');
                const order  = this.getAttribute('data-order');

                document.getElementById('edit-sponsor-id').value = id;
                document.getElementById('edit-old-image').value = image;
                document.getElementById('edit-sponsor-name').value = sponsor_name;
                if (document.getElementById('edit-event')) document.getElementById('edit-event').value = event;
                document.getElementById('edit-link').value = link;
                document.getElementById('edit-order').value = order;
                document.getElementById('edit-preview').src = 'images/' + image;
            });
        });

        // Event select handler
        var eventSelect = document.getElementById('event-select');
        var statusSelect = document.getElementById('event-status');
        var statusBadge = document.getElementById('status-badge');
        var settingsSection = document.getElementById('event-settings-section');

        if (eventSelect) {
            // Auto-load first event on page init
            var firstVal = eventSelect.value;
            filterTable(firstVal);

            if (document.getElementById('editor_sponsor')) {
                createCustomEditor('editor_sponsor');
                editorInitialized = true;
                CKEDITOR.instances['editor_sponsor'].on('instanceReady', function() {
                    loadEventData(firstVal);
                });
            }

            // Switch event handler
            eventSelect.addEventListener('change', function() {
                var val = this.value;
                filterTable(val);
                loadEventData(val);
            });

            // Status change → sync hidden field + update badge
            if (statusSelect) {
                statusSelect.addEventListener('change', function() {
                    document.getElementById('form-event-status').value = this.value;
                    var badge = document.getElementById('status-badge');
                    if (this.value == 1) {
                        badge.innerHTML = '<span class="badge bg-primary">Publish</span>';
                    } else {
                        badge.innerHTML = '<span class="badge bg-danger">Unpublish</span>';
                    }
                });
            }
        }
    });

    // Upload gambar untuk CKEditor
    document.getElementById('hidden-image-upload').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;
        var targetEditorId = this.getAttribute('data-target');
        var formData = new FormData();
        formData.append('upload', file);
        document.body.style.cursor = 'wait';
        fetch('ckeditor_upload.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.uploaded === 1) {
                var imgHtml = '<img src="' + data.url + '" style="max-width: 100%; height: auto;" width="500" alt="image" />';
                CKEDITOR.instances[targetEditorId].insertHtml(imgHtml);
            } else {
                alert("Upload Gagal: " + (data.error ? data.error.message : 'Error'));
            }
        })
        .catch(error => { alert("Koneksi Error."); })
        .finally(() => { e.target.value = ''; document.body.style.cursor = 'default'; });
    });
</script>

</body>
</html>