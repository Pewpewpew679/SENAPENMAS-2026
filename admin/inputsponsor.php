<?php

include "includes/config.php";
if (session_status() == PHP_SESSION_NONE) session_start();

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
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addSponsorModal">
                            <i class="bi bi-plus-lg"></i> Add Sponsor
                        </button>

                        <?php if (!empty($_SESSION['sponsor_error'])): ?>
                            <div class="alert alert-danger alert-sm"><?= htmlspecialchars($_SESSION['sponsor_error']) ?></div>
                            <?php unset($_SESSION['sponsor_error']); ?>
                        <?php endif; ?>
                        <?php if (!empty($_SESSION['sponsor_success'])): ?>
                            <div class="alert alert-success alert-sm"><?= htmlspecialchars($_SESSION['sponsor_success']) ?></div>
                            <?php unset($_SESSION['sponsor_success']); ?>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table id="sponsorTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th width="100">Image</th>
                                        <th>Sponsor Name</th>
                                        <th>Event</th>
                                        <th>Link</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    // Choose query depending on whether sponsors uses event_id
                                    if ($use_event_id) {
                                        $query = mysqli_query($conn, "SELECT s.*, e.event_name FROM sponsors s LEFT JOIN events e ON s.event_id = e.event_id ORDER BY COALESCE(s.order_number, 9999) ASC, s.sponsor_name ASC");
                                    } else {
                                        $query = mysqli_query($conn, "SELECT * FROM sponsors ORDER BY COALESCE(order_number, 9999) ASC, sponsor_name ASC");
                                    }

                                    if ($query) {
                                        while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><img src="images/<?= htmlspecialchars($row['sponsor_logo']) ?>" width="90" class="img-thumbnail"></td>
                                        <td><?= htmlspecialchars($row['sponsor_name']) ?></td>
                                        <td><?= htmlspecialchars($use_event_id ? ($row['event_name'] ?? ($row['event_id'] ?? '-')) : ($row['event_name'] ?? '-')) ?></td>
                                        <td><a href="<?= htmlspecialchars($row['website_link']) ?>" target="_blank"><?= htmlspecialchars($row['website_link']) ?></a></td>
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi DataTable
    if (document.getElementById("sponsorTable")) {
        new simpleDatatables.DataTable("#sponsorTable");
    }

    // Fungsi klik tombol Edit
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
});
</script>

</body>
</html>