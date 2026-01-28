<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

// ===== SIMPAN =====
if (isset($_POST['Simpan'])) {
    $link_name  = mysqli_real_escape_string($conn, $_POST['link_name']);
    $url        = mysqli_real_escape_string($conn, $_POST['url']);
    $order_urut = mysqli_real_escape_string($conn, $_POST['order_urut']);

    mysqli_query($conn, "
        INSERT INTO links (link_name, url, order_urut)
        VALUES ('$link_name', '$url', '$order_urut')
    ");

    header("Location: inputlinks.php");
    exit;
}

// ===== UPDATE =====
if (isset($_POST['Update'])) {
    $old_name   = mysqli_real_escape_string($conn, $_POST['old_name']);
    $link_name  = mysqli_real_escape_string($conn, $_POST['link_name']);
    $url        = mysqli_real_escape_string($conn, $_POST['url']);
    $order_urut = mysqli_real_escape_string($conn, $_POST['order_urut']);

    mysqli_query($conn, "
        UPDATE links SET
            link_name='$link_name',
            url='$url',
            order_urut='$order_urut'
        WHERE link_name='$old_name'
    ");

    header("Location: inputlinks.php");
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
                <h1 class="mt-4">Links</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addLinkModal">
                            <i class="bi bi-plus-lg"></i> Add Link
                        </button>

                        <div class="table-responsive">
                            <table id="linkTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="20">No</th>
                                        <th width="100">Link Name</th>
                                        <th>URL</th>
                                        <th>Order</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "SELECT * FROM links ORDER BY order_urut ASC");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['link_name']) ?></td>
                                        <td><?= htmlspecialchars($row['url']) ?></td>
                                        <td><?= htmlspecialchars($row['order_urut']) ?></td>
                                        <td>
                                           <a href="javascript:void(0)"   
                                                style="text-decoration: none; margin-right: 30px;"
                                                class="btn-edit me-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editLinksModal"
                                                data-old="<?= htmlspecialchars($row['link_name']) ?>"
                                                data-name="<?= htmlspecialchars($row['link_name']) ?>"
                                                data-url="<?= htmlspecialchars($row['url']) ?>"
                                                data-order="<?= htmlspecialchars($row['order_urut']) ?>">
                                                Edit
                                            </a>


                                            <a href="hapuslinks.php?link_name=<?= urlencode($row['link_name']) ?>"
                                                style="text-decoration: none;"
                                                onclick="return confirm('Hapus data ini?')">
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


<div class="modal fade" id="addLinkModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Link Name</label>
                        <input type="text" name="link_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>URL</label>
                        <input type="text" name="url" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Order</label>
                        <input type="text" name="order_urut" class="form-control" required>
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

<div class="modal fade" id="editLinksModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-3">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <input type="hidden" name="old_name" id="edit-old-name">

                <div class="mb-3">
                    <label>Link Name</label>
                    <input type="text" name="link_name" id="edit-name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>URL</label>
                    <input type="text" name="url" id="edit-url" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Order</label>
                    <input type="number" name="order_urut" id="edit-order" class="form-control" required>
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

    if (document.getElementById("linkTable")) {
        new simpleDatatables.DataTable("#linkTable");
    }

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {

            document.getElementById('edit-old-name').value = this.dataset.old;
            document.getElementById('edit-name').value    = this.dataset.name;
            document.getElementById('edit-url').value     = this.dataset.url;
            document.getElementById('edit-order').value   = this.dataset.order;

        });
    });

});
</script>





</body>
</html>