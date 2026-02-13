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
    // Menyesuaikan dengan kolom: link_name, link_url, link_order
    $link_name  = mysqli_real_escape_string($conn, $_POST['link_name']);
    $link_url   = mysqli_real_escape_string($conn, $_POST['link_url']);
    $link_order = (int)$_POST['link_order'];

    mysqli_query($conn, "
        INSERT INTO links (link_name, link_url, link_order)
        VALUES ('$link_name', '$link_url', '$link_order')
    ");

    header("Location: inputlinks.php");
    exit;
}

// ===== UPDATE =====
if (isset($_POST['Update'])) {
    // Menggunakan link_id sebagai primary key agar lebih aman
    $link_id    = mysqli_real_escape_string($conn, $_POST['link_id']);
    $link_name  = mysqli_real_escape_string($conn, $_POST['link_name']);
    $link_url   = mysqli_real_escape_string($conn, $_POST['link_url']);
    $link_order = (int)$_POST['link_order'];

    mysqli_query($conn, "
        UPDATE links SET
            link_name='$link_name',
            link_url='$link_url',
            link_order='$link_order'
        WHERE link_id='$link_id'
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
                                        <th>Link Name</th>
                                        <th>URL</th>
                                        <th width="50">Order</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    // Mengambil data dari tabel links sesuai gambar
                                    $query = mysqli_query($conn, "SELECT * FROM links ORDER BY link_order ASC");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['link_name']) ?></td>
                                        <td><a href="<?= htmlspecialchars($row['link_url']) ?>" target="_blank"><?= htmlspecialchars($row['link_url']) ?></a></td>
                                        <td><?= $row['link_order'] ?></td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                            class="btn-edit me-2" 
                                            style="text-decoration: none; margin-right: 30px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editLinksModal"
                                            data-id="<?= $row['link_id'] ?>"
                                            data-name="<?= htmlspecialchars($row['link_name']) ?>"
                                            data-url="<?= htmlspecialchars($row['link_url']) ?>"
                                            data-order="<?= $row['link_order'] ?>">
                                            Edit
                                            </a>

                                            <a href="hapuslinks.php?id=<?= $row['link_id'] ?>"
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Link Name</label>
                        <input type="text" name="link_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="url" name="link_url" class="form-control" placeholder="https://..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order Number</label>
                        <input type="number" name="link_order" class="form-control" value="0" required>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="link_id" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label">Link Name</label>
                        <input type="text" name="link_name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <input type="url" name="link_url" id="edit-url" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order Number</label>
                        <input type="number" name="link_order" id="edit-order" class="form-control" required>
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
    if (document.getElementById("linkTable")) {
        new simpleDatatables.DataTable("#linkTable");
    }

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit-id').value    = this.dataset.id;
            document.getElementById('edit-name').value  = this.dataset.name;
            document.getElementById('edit-url').value   = this.dataset.url;
            document.getElementById('edit-order').value = this.dataset.order;
        });
    });
});
</script>
</body>
</html>