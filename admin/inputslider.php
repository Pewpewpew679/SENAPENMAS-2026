<?php
ob_start();
session_start();

// Cek Login
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

/* =====================================
   1. PROSES SIMPAN SLIDER (ADD NEW)
===================================== */
if (isset($_POST['Simpan'])) {
    $title  = mysqli_real_escape_string($conn, $_POST['title']);
    $link   = mysqli_real_escape_string($conn, $_POST['link']);
    $status = (int) $_POST['status'];

    $image_name = $_FILES['image_slider']['name'];
    $tmp        = $_FILES['image_slider']['tmp_name'];
    $ext        = pathinfo($image_name, PATHINFO_EXTENSION);

    $new_name = time() . "_" . uniqid() . "." . $ext;
    $folder   = "images/";

    if (move_uploaded_file($tmp, $folder . $new_name)) {
        mysqli_query($conn, "INSERT INTO slider (image_slider, title, link, status) VALUES ('$new_name', '$title', '$link', '$status')");
    }

    header("Location: inputslider.php");
    exit;
}

/* =====================================
   2. PROSES UPDATE SLIDER (EDIT)
===================================== */
if (isset($_POST['Update'])) {
    $old_image = mysqli_real_escape_string($conn, $_POST['old_image']);
    $title     = mysqli_real_escape_string($conn, $_POST['title']);
    $link      = mysqli_real_escape_string($conn, $_POST['link']);
    $status    = (int) $_POST['status'];

    if ($_FILES['image_slider']['name'] != "") {
        // Jika upload gambar baru
        $image_name = $_FILES['image_slider']['name'];
        $tmp        = $_FILES['image_slider']['tmp_name'];
        $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_name   = time() . "_" . uniqid() . "." . $ext;
        
        move_uploaded_file($tmp, "images/" . $new_name);
        
        // Hapus file fisik lama
        if (file_exists("images/" . $old_image)) {
            unlink("images/" . $old_image);
        }

        mysqli_query($conn, "UPDATE slider SET image_slider='$new_name', title='$title', link='$link', status='$status' WHERE image_slider='$old_image'");
    } else {
        // Jika tidak ganti gambar
        mysqli_query($conn, "UPDATE slider SET title='$title', link='$link', status='$status' WHERE image_slider='$old_image'");
    }
    header("Location: inputslider.php");
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
                <h1 class="mt-4">Slider</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addSliderModal">
                            <i class="bi bi-plus-lg"></i> Add Slider
                        </button>

                        <div class="table-responsive">
                            <table id="sliderTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th width="100">Image Slider</th>
                                        <th>Title</th>
                                        <th>Link</th>
                                        <th width="60">Status</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "SELECT * FROM slider ORDER BY image_slider DESC");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><img src="images/<?= $row['image_slider'] ?>" width="90" class="img-thumbnail"></td>
                                        <td><?= htmlspecialchars($row['title']) ?></td>
                                        <td><?= htmlspecialchars($row['link']) ?></td>
                                        <td>
                                            <?= $row['status'] == 1 ? '<span>Aktif</span>' : '<span>Non Aktif</span>' ?>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                                    class="btn-edit me-2"href="#" 
                                                    style="text-decoration: none; margin-right: 30px;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editSliderModal"
                                                    data-image="<?= $row['image_slider'] ?>"
                                                    data-title="<?= htmlspecialchars($row['title']) ?>"
                                                    data-link="<?= htmlspecialchars($row['link']) ?>"
                                                    data-status="<?= $row['status'] ?>">
                                                Edit
                                            </a>
                                            <a href="hapusslider.php?image=<?= $row['image_slider'] ?>" style="text-decoration: none;"
                                               onclick="return confirm('Hapus data ini?')">Delete</a>
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


<div class="modal fade" id="addSliderModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Slider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="text" name="link" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image *</label>
                        <input type="file" name="image_slider" class="form-control" accept=".jpg,.jpeg,.png" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
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


<div class="modal fade" id="editSliderModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Slider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="old_image" id="edit-old-image">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" id="edit-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="text" name="link" id="edit-link" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Current Image</label>
                        <img src="" id="edit-preview" width="120" class="img-thumbnail mb-2">
                        <input type="file" name="image_slider" class="form-control" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" id="edit-status" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
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
    // Inisialisasi DataTable
    if (document.getElementById("sliderTable")) {
        new simpleDatatables.DataTable("#sliderTable");
    }

    // Fungsi klik tombol Edit
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const image  = this.getAttribute('data-image');
            const title  = this.getAttribute('data-title');
            const link   = this.getAttribute('data-link');
            const status = this.getAttribute('data-status');

            document.getElementById('edit-old-image').value = image;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-link').value = link;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-preview').src = 'images/' + image;
        });
    });
});
</script>

</body>
</html>