<?php
ob_start();
session_start();

// Cek Login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

/* ================= ICON MAP ================= */
// Digunakan untuk menampilkan icon berdasarkan nama platform
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
    // Sesuaikan dengan kolom database Anda
    $platform_name = mysqli_real_escape_string($conn, $_POST['platform_name']);
    $social_link   = mysqli_real_escape_string($conn, $_POST['social_link']);
    $profile_id    = 1; // Default profile_id jika belum ada sistem multi-user

    mysqli_query($conn, "INSERT INTO sosmed (profile_id, platform_name, social_link) 
                        VALUES ('$profile_id', '$platform_name', '$social_link')");

    header("Location: inputsosmed.php");
    exit;
}

/* ================= UPDATE ================= */
if (isset($_POST['Update'])) {
    $social_id     = mysqli_real_escape_string($conn, $_POST['social_id']);
    $platform_name = mysqli_real_escape_string($conn, $_POST['platform_name']);
    $social_link   = mysqli_real_escape_string($conn, $_POST['social_link']);

    // Update menggunakan social_id agar lebih akurat (Primary Key)
    mysqli_query($conn, "UPDATE sosmed SET platform_name='$platform_name', social_link='$social_link' 
                        WHERE social_id='$social_id'");

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
                            <i class="bi bi-plus-lg"></i> Add Social Media
                        </button>

                        <div class="table-responsive">
                            <table id="sosmedTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Platform</th>
                                        <th>Link</th>
                                        <th width="80" class="text-center">Icon</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    // Query sesuai nama tabel Anda
                                    $q = mysqli_query($conn, "SELECT * FROM sosmed ORDER BY created_at DESC");
                                    while ($row = mysqli_fetch_assoc($q)) {
                                        // Cari icon di iconMap, jika tidak ada pakai globe
                                        $displayIcon = $iconMap[$row['platform_name']] ?? 'bi-globe';
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= htmlspecialchars($row['platform_name']) ?></strong></td>
                                        <td><a href="<?= htmlspecialchars($row['social_link']) ?>" target="_blank"><?= htmlspecialchars($row['social_link']) ?></a></td>
                                        <td class="text-center">
                                            <i class="bi <?= $displayIcon ?>" style="font-size:1.2rem;"></i>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                            class="btn-edit me-2" 
                                            style="text-decoration: none; margin-right: 30px;"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editModal"
                                            data-id="<?= $row['social_id'] ?>"
                                            data-platform="<?= htmlspecialchars($row['platform_name']) ?>"
                                            data-link="<?= htmlspecialchars($row['social_link']) ?>">
                                            Edit
                                            </a>

                                            <a href="hapussosmed.php?id=<?= $row['social_id'] ?>" 
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
                        <label class="form-label">Platform *</label>
                        <select name="platform_name" class="form-select" required>
                            <option value="">-- Select Platform --</option>
                            <?php foreach ($iconMap as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $k ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL / Link *</label>
                        <input type="url" name="social_link" class="form-control" placeholder="https://..." required>
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
                <input type="hidden" name="social_id" id="edit-id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Social Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Platform</label>
                        <select name="platform_name" id="edit-platform" class="form-select">
                            <?php foreach ($iconMap as $k => $v): ?>
                                <option value="<?= $k ?>"><?= $k ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL / Link</label>
                        <input type="url" name="social_link" id="edit-link" class="form-control" required>
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
            document.getElementById('edit-id').value = this.getAttribute('data-id');
            document.getElementById('edit-platform').value = this.getAttribute('data-platform');
            document.getElementById('edit-link').value = this.getAttribute('data-link');
        });
    });
});
</script>
</body>
</html>