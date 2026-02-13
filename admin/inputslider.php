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
    $slider_title = mysqli_real_escape_string($conn, $_POST['slider_title']);
    $slider_link  = mysqli_real_escape_string($conn, $_POST['slider_link']);
    $order_number = (int) $_POST['order_number'];
    $status       = $_POST['status']; // 'Active' atau 'Inactive'

    $image_name = $_FILES['slider_image']['name'];
    $tmp        = $_FILES['slider_image']['tmp_name'];
    $ext        = pathinfo($image_name, PATHINFO_EXTENSION);

    $new_name = time() . "_" . uniqid() . "." . $ext;
    $folder   = "../frontend/images/sliders/"; // Simpan di folder frontend

    if (!is_dir($folder)) { mkdir($folder, 0777, true); }

    if (move_uploaded_file($tmp, $folder . $new_name)) {
        mysqli_query($conn, "INSERT INTO sliders (slider_title, slider_image, slider_link, order_number, status) 
                            VALUES ('$slider_title', '$new_name', '$slider_link', '$order_number', '$status')");
    }

    header("Location: inputslider.php?msg=success");
    exit;
}

/* =====================================
   2. PROSES UPDATE SLIDER (EDIT)
===================================== */
if (isset($_POST['Update'])) {
    $slider_id    = (int) $_POST['slider_id'];
    $old_image    = mysqli_real_escape_string($conn, $_POST['old_image']);
    $slider_title = mysqli_real_escape_string($conn, $_POST['slider_title']);
    $slider_link  = mysqli_real_escape_string($conn, $_POST['slider_link']);
    $order_number = (int) $_POST['order_number'];
    $status       = $_POST['status'];

    $final_image = $old_image;

    if ($_FILES['slider_image']['name'] != "") {
        $image_name = $_FILES['slider_image']['name'];
        $tmp        = $_FILES['slider_image']['tmp_name'];
        $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_name   = time() . "_" . uniqid() . "." . $ext;
        $folder     = "../frontend/images/sliders/";
        
        if (!is_dir($folder)) { mkdir($folder, 0777, true); }
        
        if (move_uploaded_file($tmp, $folder . $new_name)) {
            $final_image = $new_name;
            if (file_exists($folder . $old_image)) {
                unlink($folder . $old_image);
            }
        }
    }

    mysqli_query($conn, "UPDATE sliders SET 
                        slider_title='$slider_title', 
                        slider_image='$final_image', 
                        slider_link='$slider_link', 
                        order_number='$order_number', 
                        status='$status' 
                        WHERE slider_id='$slider_id'");

    header("Location: inputslider.php?msg=updated");
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
                <h1 class="mt-4">Manage Slider</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addSliderModal">
                            <i class="bi bi-plus-lg"></i> Add Slider
                        </button>

                        <div class="table-responsive">
                            <table id="sliderTable" class="table table-sm table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th width="100">Image</th>
                                        <th>Title</th>
                                        <th>Order</th>
                                        <th width="80">Status</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "SELECT * FROM sliders ORDER BY order_number ASC");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                        // Cek apakah gambar ada di frontend
                                        if (file_exists("../frontend/images/sliders/" . $row['slider_image'])) {
                                            $img_path = "../frontend/images/sliders/" . $row['slider_image'];
                                        } else {
                                            $img_path = "images/" . $row['slider_image']; // Fallback ke folder admin
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><img src="<?= $img_path ?>" width="80" class="img-thumbnail"></td>
                                        <td><?= htmlspecialchars($row['slider_title']) ?></td>
                                        <td><?= $row['order_number'] ?></td>

                                        <td>
                                            <span class="<?= $row['status'] == 'Active' ? 'text-primary' : 'text-danger' ?>">
                                                <?= $row['status'] ?>
                                            </span>
                                        </td>

                                        <td>
                                            <a href="javascript:void(0)" 
                                            class="btn-edit" 
                                            style="text-decoration: none; margin-right: 30px;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editSliderModal"
                                            data-id="<?= $row['slider_id'] ?>"
                                            data-image="<?= $row['slider_image'] ?>"
                                            data-title="<?= htmlspecialchars($row['slider_title']) ?>"
                                            data-link="<?= htmlspecialchars($row['slider_link']) ?>"
                                            data-order="<?= $row['order_number'] ?>"
                                            data-status="<?= $row['status'] ?>">
                                            Edit
                                            </a>
                                            
                                            <a href="hapusslider.php?id=<?= $row['slider_id'] ?>&image=<?= $row['slider_image'] ?>" 
                                            style="text-decoration: none;"
                                            onclick="return confirm('Hapus slider ini?')">
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

<div class="modal fade" id="addSliderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Slider</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Slider Title *</label>
                        <input type="text" name="slider_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link (URL)</label>
                        <input type="text" name="slider_link" class="form-control" placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order Number</label>
                        <input type="number" name="order_number" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slider Image *</label>
                        <input type="file" name="slider_image" class="form-control" accept=".jpg,.jpeg,.png" required>
                        <small class="text-muted">Recommended size: 1920x600px</small>
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
                    <button type="submit" name="Simpan" class="btn btn-success btn-sm">Save Slider</button>
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
                    <input type="hidden" name="slider_id" id="edit-id">
                    <input type="hidden" name="old_image" id="edit-old-image">
                    
                    <div class="mb-3">
                        <label class="form-label">Slider Title *</label>
                        <input type="text" name="slider_title" id="edit-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link (URL)</label>
                        <input type="text" name="slider_link" id="edit-link" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order Number</label>
                        <input type="number" name="order_number" id="edit-order" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Current Image</label>
                        <img src="" id="edit-preview" width="150" class="img-thumbnail mb-2">
                        <input type="file" name="slider_image" class="form-control" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Kosongkan jika tidak ingin ganti gambar.</small>
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
                    <button type="submit" name="Update" class="btn btn-primary btn-sm">Update Slider</button>
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
            const id     = this.getAttribute('data-id');
            const image  = this.getAttribute('data-image');
            const title  = this.getAttribute('data-title');
            const link   = this.getAttribute('data-link');
            const order  = this.getAttribute('data-order');
            const status = this.getAttribute('data-status');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-old-image').value = image;
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-link').value = link;
            document.getElementById('edit-order').value = order;
            document.getElementById('edit-status').value = status;
            
            // Preview image - cek di frontend dulu
            document.getElementById('edit-preview').src = '../frontend/images/sliders/' + image;
            document.getElementById('edit-preview').onerror = function() {
                this.src = 'images/' + image; // Fallback ke admin
            };
        });
    });
});
</script>
</body>
</html>