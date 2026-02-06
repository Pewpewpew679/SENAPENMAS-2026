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
    $status = $_POST['status']; // Mengambil nilai 'Active' atau 'Inactive'
    $order  = (int)$_POST['order_number'];

    $image_name = $_FILES['image_slider']['name'];
    $tmp        = $_FILES['image_slider']['tmp_name'];
    $ext        = pathinfo($image_name, PATHINFO_EXTENSION);

    $new_name = time() . "_" . uniqid() . "." . $ext;
    $folder   = "images/";

    if (move_uploaded_file($tmp, $folder . $new_name)) {
        // Nama kolom disesuaikan: slider_title, slider_image, slider_link, order_number, status
        mysqli_query($conn, "INSERT INTO sliders (slider_title, slider_image, slider_link, order_number, status) 
                            VALUES ('$title', '$new_name', '$link', '$order', '$status')");
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
    $status    = $_POST['status'];
    $order     = (int)$_POST['order_number'];

    if ($_FILES['image_slider']['name'] != "") {
        // Jika upload gambar baru
        $image_name = $_FILES['image_slider']['name'];
        $tmp        = $_FILES['image_slider']['tmp_name'];
        $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_name   = time() . "_" . uniqid() . "." . $ext;
        
        move_uploaded_file($tmp, "images/" . $new_name);
        
        if (file_exists("images/" . $old_image)) {
            unlink("images/" . $old_image);
        }

        mysqli_query($conn, "UPDATE sliders SET slider_image='$new_name', slider_title='$title', slider_link='$link', order_number='$order', status='$status' WHERE slider_image='$old_image'");
    } else {
        // Jika tidak ganti gambar
        mysqli_query($conn, "UPDATE sliders SET slider_title='$title', slider_link='$link', order_number='$order', status='$status' WHERE slider_image='$old_image'");
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
                                        <th width="40">Order</th>
                                        <th width="60">Status</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "SELECT * FROM sliders ORDER BY order_number ASC");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><img src="images/<?= $row['slider_image'] ?>" width="90" class="img-thumbnail"></td>
                                        <td><?= htmlspecialchars($row['slider_title']) ?></td>
                                        <td><?= htmlspecialchars($row['slider_link']) ?></td>
                                        <td><?= $row['order_number'] ?></td>
                                        <td>
                                            <span class="badge <?= $row['status'] == 'Active' ? 'bg-success' : 'bg-danger' ?>">
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                               class="btn-edit btn btn-warning btn-sm"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#editSliderModal"
                                               data-image="<?= $row['slider_image'] ?>"
                                               data-title="<?= htmlspecialchars($row['slider_title']) ?>"
                                               data-link="<?= htmlspecialchars($row['slider_link']) ?>"
                                               data-order="<?= $row['order_number'] ?>"
                                               data-status="<?= $row['status'] ?>">
                                                Edit
                                            </a>
                                            <a href="hapusslider.php?image=<?= $row['slider_image'] ?>" 
                                               class="btn btn-danger btn-sm"
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
    <div class="modal-dialog">
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
                        <label class="form-label">Order Number</label>
                        <input type="number" name="order_number" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image *</label>
                        <input type="file" name="image_slider" class="form-control" accept=".jpg,.jpeg,.png" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
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
    <div class="modal-dialog">
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
                        <label class="form-label">Order Number</label>
                        <input type="number" name="order_number" id="edit-order" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Current Image</label>
                        <img src="" id="edit-preview" width="120" class="img-thumbnail mb-2">
                        <input type="file" name="image_slider" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" id="edit-status" class="form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
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
    if (document.getElementById("sliderTable")) {
        new simpleDatatables.DataTable("#sliderTable");
    }

    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('edit-old-image').value = this.getAttribute('data-image');
            document.getElementById('edit-title').value = this.getAttribute('data-title');
            document.getElementById('edit-link').value = this.getAttribute('data-link');
            document.getElementById('edit-order').value = this.getAttribute('data-order');
            document.getElementById('edit-status').value = this.getAttribute('data-status');
            document.getElementById('edit-preview').src = 'images/' + this.getAttribute('data-image');
        });
    });
});
</script>

</body>
</html>