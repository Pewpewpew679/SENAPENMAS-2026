<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

/* ================= ICON MAP ================= */
$iconMap = [
    "Twitter"   => "bi-twitter-x",
    "Instagram" => "bi-instagram",
    "Facebook"  => "bi-facebook",
    "Tiktok"    => "bi-tiktok",
    "Youtube"   => "bi-youtube",
    "Linkedin"  => "bi-linkedin",
    "Whatsapp"  => "bi-whatsapp",
    "Telegram"  => "bi-telegram",
    "Web"       => "bi-globe"
];

/* ================= SIMPAN ================= */
if (isset($_POST['Simpan'])) {
    $akun = mysqli_real_escape_string($conn, $_POST['akun']);
    $link = mysqli_real_escape_string($conn, $_POST['link']);
    $icon = isset($iconMap[$akun]) ? $iconMap[$akun] : "bi-globe";

    mysqli_query($conn, "INSERT INTO sosmed (akun, link, icon) VALUES ('$akun','$link','$icon')");

    header("Location: inputsosmed.php");
    exit;
}

/* ================= UPDATE ================= */
if (isset($_POST['Update'])) {
    $old = mysqli_real_escape_string($conn, $_POST['old_akun']);
    $akun = mysqli_real_escape_string($conn, $_POST['akun']);
    $link = mysqli_real_escape_string($conn, $_POST['link']);
    $icon = isset($iconMap[$akun]) ? $iconMap[$akun] : "bi-globe";

    mysqli_query($conn, "UPDATE sosmed SET akun='$akun', link='$link', icon='$icon' WHERE akun='$old'");

    header("Location: inputsosmed.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "bagiankode/head.php"; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="sb-nav-fixed">
<?php include "bagiankode/menunav.php"; ?>

<div id="layoutSidenav">
    <?php include "bagiankode/menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Social Media</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus-lg"></i> Add Sosmed
                        </button>

                        <div class="table-responsive">
                            <table id="sosmedTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Akun</th>
                                        <th>Link</th>
                                        <th width="80" class="text-center">Icon</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $q = mysqli_query($conn, "SELECT * FROM sosmed");
                                    while ($row = mysqli_fetch_assoc($q)) {
                                        $displayIcon = !empty($row['icon']) ? $row['icon'] : ($iconMap[$row['akun']] ?? 'bi-globe');
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= htmlspecialchars($row['akun']) ?></strong></td>
                                        <td><?= htmlspecialchars($row['link']) ?></td>
                                        <td class="text-center" style="vertical-align: middle;">
                                            <i class="bi <?= $displayIcon ?>" style="font-size:1.2rem; display: block;"></i>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                               class="btn-edit" 
                                               style="text-decoration: none; margin-right: 15px;"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#editModal"
                                               data-old="<?= $row['akun'] ?>"
                                               data-akun="<?= $row['akun'] ?>"
                                               data-link="<?= $row['link'] ?>">
                                                Edit
                                            </a>
                                            <a href="hapussosmed.php?akun=<?= urlencode($row['akun']) ?>" 
                                               style="text-decoration: none; color: #0d6efd;"
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

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Social Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Akun *</label>
                        <select name="akun" class="form-select" required>
                            <option value="">-- Pilih Sosmed --</option>
                            <?php foreach ($iconMap as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $k ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL / Link *</label>
                        <input type="url" name="link" class="form-control" placeholder="Link" required>
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

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="old_akun" id="edit-old">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Social Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Akun</label>
                        <select name="akun" id="edit-akun" class="form-select">
                            <?php foreach ($iconMap as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $k ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL / Link</label>
                        <input type="url" name="link" id="edit-link" class="form-control" required>
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
    if (document.getElementById("sosmedTable")) {
        new simpleDatatables.DataTable("#sosmedTable");
    }

    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('edit-old').value = this.getAttribute('data-old');
            document.getElementById('edit-akun').value = this.getAttribute('data-akun');
            document.getElementById('edit-link').value = this.getAttribute('data-link');
        });
    });
});
</script>
</body>
</html>