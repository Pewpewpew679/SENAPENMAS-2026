<?php
ob_start();
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

// ===== SIMPAN =====
if (isset($_POST['Simpan'])) {
    $supported_name = mysqli_real_escape_string($conn, $_POST['supported_name']);
    $supported_link = mysqli_real_escape_string($conn, $_POST['supported_link']);
    $event_id       = (int)$_POST['event_id']; 

    $image_name = $_FILES['supported_image']['name'];
    $tmp        = $_FILES['supported_image']['tmp_name'];
    $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
    $new_name   = time() . "_" . uniqid() . "." . $ext;
    $folder     = "images/";

    if (move_uploaded_file($tmp, $folder . $new_name)) {
        mysqli_query($conn, "
            INSERT INTO supported (event_id, supported_name, supported_image, supported_link)
            VALUES ('$event_id', '$supported_name', '$new_name', '$supported_link')
        ");
    }

    header("Location: inputsupported.php?msg=success");
    exit;
}

// ===== UPDATE =====
if (isset($_POST['Update'])) {
    $supported_id   = mysqli_real_escape_string($conn, $_POST['supported_id']);
    $supported_name = mysqli_real_escape_string($conn, $_POST['supported_name']);
    $supported_link = mysqli_real_escape_string($conn, $_POST['supported_link']);
    $event_id       = (int)$_POST['event_id'];
    $old_image      = $_POST['old_image'];

    if ($_FILES['supported_image']['name'] != "") {
        $image_name = $_FILES['supported_image']['name'];
        $tmp        = $_FILES['supported_image']['tmp_name'];
        $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_name   = time() . "_" . uniqid() . "." . $ext;
        
        move_uploaded_file($tmp, "images/" . $new_name);
        if (file_exists("images/" . $old_image)) { unlink("images/" . $old_image); }

        mysqli_query($conn, "UPDATE supported SET event_id='$event_id', supported_name='$supported_name', supported_image='$new_name', supported_link='$supported_link' WHERE supported_id='$supported_id'");
    } else {
        mysqli_query($conn, "UPDATE supported SET event_id='$event_id', supported_name='$supported_name', supported_link='$supported_link' WHERE supported_id='$supported_id'");
    }

    header("Location: inputsupported.php?msg=updated");
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
                <h1 class="mt-4">Supported</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus-lg"></i> Add Supported
                        </button>

                        <div class="table-responsive">
                            <table id="supportedTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="20">No</th>
                                        <th width="100">Logo</th>
                                        <th>Supported Name</th>
                                        <th>Event Name</th>
                                        <th>Link</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    // Query JOIN untuk mendapatkan Nama Event
                                    $query = mysqli_query($conn, "SELECT s.*, e.event_name 
                                                                  FROM supported s 
                                                                  LEFT JOIN events e ON s.event_id = e.event_id 
                                                                  ORDER BY s.supported_id DESC");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><img src="images/<?= $row['supported_image'] ?>" width="80" class="img-thumbnail"></td>
                                        <td><?= htmlspecialchars($row['supported_name']) ?></td>
                                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['event_name'] ?? 'Unlinked') ?></span></td>
                                        <td><small><?= htmlspecialchars($row['supported_link']) ?></small></td>
                                        <td>
                                            <a href="#" class="text-primary me-2 btn-edit" style="text-decoration:none;"
                                                data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="<?= $row['supported_id'] ?>"
                                                data-name="<?= htmlspecialchars($row['supported_name']) ?>"
                                                data-event="<?= $row['event_id'] ?>"
                                                data-link="<?= htmlspecialchars($row['supported_link']) ?>"
                                                data-image="<?= $row['supported_image'] ?>">
                                               Edit
                                            </a>
                                            <a href="hapussupported.php?id=<?= $row['supported_id'] ?>&image=<?= $row['supported_image'] ?>" 
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


<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Supported</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Target Event</label>
                        <select name="event_id" class="form-select" required>
                            <option value="">-- Choose Event --</option>
                            <?php
                            $ev_res = mysqli_query($conn, "SELECT event_id, event_name FROM events ORDER BY event_name ASC");
                            while($ev = mysqli_fetch_assoc($ev_res)) {
                                echo "<option value='".$ev['event_id']."'>".$ev['event_name']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supported Name</label>
                        <input type="text" name="supported_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL / Website</label>
                        <input type="text" name="supported_link" class="form-control" placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo Image</label>
                        <input type="file" name="supported_image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="Simpan" class="btn btn-primary btn-sm">Save Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supported</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="supported_id" id="edit-id">
                    <input type="hidden" name="old_image" id="edit-old-image">
                    
                    <div class="mb-3">
                        <label class="form-label">Target Event</label>
                        <select name="event_id" id="edit-event" class="form-select" required>
                            <?php
                            mysqli_data_seek($ev_res, 0); // Reset pointer query
                            while($ev = mysqli_fetch_assoc($ev_res)) {
                                echo "<option value='".$ev['event_id']."'>".$ev['event_name']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supported Name</label>
                        <input type="text" name="supported_name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link/Website</label>
                        <input type="text" name="supported_link" id="edit-link" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Current Logo</label>
                        <img src="" id="edit-preview" width="100" class="img-thumbnail mb-2">
                        <input type="file" name="supported_image" class="form-control" accept="image/*">
                        <small class="text-muted">*Leave blank if not changing image</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="Update" class="btn btn-primary btn-sm">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "bagiankode/jsscript.php"; ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("supportedTable")) {
        new simpleDatatables.DataTable("#supportedTable");
    }

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-name').value = this.dataset.name;
            document.getElementById('edit-event').value = this.dataset.event; // Otomatis pilih dropdown
            document.getElementById('edit-link').value = this.dataset.link;
            document.getElementById('edit-old-image').value = this.dataset.image;
            document.getElementById('edit-preview').src = "images/" + this.dataset.image;
        });
    });
});
</script>
</body>
</html>