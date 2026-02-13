<?php
ob_start();
session_start();

// Cek Login
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}


include "includes/config.php";

// Periksa apakah kolom menu_order ada, jika tidak coba buat
$has_menu_order = false;
$colRes = mysqli_query($conn, "SHOW COLUMNS FROM menu LIKE 'menu_order'");
if ($colRes && mysqli_num_rows($colRes) > 0) {
    $has_menu_order = true;
} else {
    // Coba tambahkan kolom (jika user punya hak)
    @mysqli_query($conn, "ALTER TABLE menu ADD COLUMN menu_order INT NULL DEFAULT NULL");
    $colRes2 = mysqli_query($conn, "SHOW COLUMNS FROM menu LIKE 'menu_order'");
    if ($colRes2 && mysqli_num_rows($colRes2) > 0) {
        $has_menu_order = true;
    } else {
        // tidak berhasil membuat kolom - biarkan flag false
        // (informasi kalau mau dibantu tambah manual bisa ditampilkan ke user)
    }
}

$parent_list = [];
$parent_res = mysqli_query($conn, "SELECT menu_id, menu_name FROM menu ORDER BY menu_name");
while ($r = mysqli_fetch_assoc($parent_res)) { $parent_list[] = $r; }

// Siapkan clause order kalau kolom menu_order ada
$order_clause = $has_menu_order ? "ORDER BY COALESCE(m.menu_order, 9999) ASC, m.menu_name ASC" : "ORDER BY m.menu_name ASC";

/* =====================================
   1. PROSES SIMPAN MENU (ADD NEW)
===================================== */
if (isset($_POST['Simpan'])) {
    $menu  = mysqli_real_escape_string($conn, $_POST['menu']);
    $parent   = isset($_POST['parent']) ? mysqli_real_escape_string($conn, $_POST['parent']) : '';
    $type      = mysqli_real_escape_string($conn, $_POST['type']);
    $link     = mysqli_real_escape_string($conn, $_POST['link']);

    $parent_sql = ($parent === '' || $parent === '0') ? "NULL" : "'" . $parent . "'";

    // handle order jika kolom tersedia
    if ($has_menu_order) {
        $order_val = isset($_POST['menu_order']) && $_POST['menu_order'] !== '' ? intval($_POST['menu_order']) : 'NULL';
        $sql = "INSERT INTO menu (menu_name, parent_id, menu_type, menu_link, menu_order) VALUES ('$menu', $parent_sql, '$type', '$link', $order_val)";
    } else {
        $sql = "INSERT INTO menu (menu_name, parent_id, menu_type, menu_link) VALUES ('$menu', $parent_sql, '$type', '$link')";
    }

    if (!mysqli_query($conn, $sql)) {
        $_SESSION['error'] = 'Terjadi kesalahan database: ' . mysqli_error($conn);
        header('Location: inputmenu.php');
        exit;
    } else {
        $_SESSION['success'] = 'Menu berhasil ditambahkan.';
        header('Location: inputmenu.php');
        exit;
    }
}  

/* =====================================
   2. PROSES UPDATE MENU (EDIT)
===================================== */
if (isset($_POST['Update'])) {
    $menu      = mysqli_real_escape_string($conn, $_POST['menu']);
    $parent     = isset($_POST['parent']) ? mysqli_real_escape_string($conn, $_POST['parent']) : '';
    $type    = mysqli_real_escape_string($conn, $_POST['type']);
    $link     = mysqli_real_escape_string($conn, $_POST['link']);
    $menu_id = mysqli_real_escape_string($conn, $_POST['menu_id']);

    // Cegah memilih parent yang sama dengan menu itu sendiri
    if ($parent !== '' && $parent === $menu_id) {
        $_SESSION['error'] = 'Parent tidak boleh sama dengan menu yang sedang dibuay.';
        header('Location: inputmenu.php');
        exit;
    }

    $parent_sql = ($parent === '' || $parent === '0') ? "NULL" : "'" . $parent . "'";

    if ($has_menu_order) {
        $order_val = isset($_POST['menu_order']) && $_POST['menu_order'] !== '' ? intval($_POST['menu_order']) : 'NULL';
        $sql = "UPDATE menu SET menu_name='$menu', parent_id=$parent_sql, menu_type='$type', menu_link='$link', menu_order=$order_val WHERE menu_id='$menu_id'";
    } else {
        $sql = "UPDATE menu SET menu_name='$menu', parent_id=$parent_sql, menu_type='$type', menu_link='$link' WHERE menu_id='$menu_id'";
    }

    if (!mysqli_query($conn, $sql)) {
        $_SESSION['error'] = 'Terjadi kesalahan database saat mengupdate: ' . mysqli_error($conn);
        header('Location: inputmenu.php');
        exit;
    } else {
        $_SESSION['success'] = 'Menu berhasil diperbarui.';
        header('Location: inputmenu.php');
        exit;
    }
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
                <h1 class="mt-4">Menu</h1>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                            <i class="bi bi-plus-lg"></i> Add Menu
                        </button>

                        <div class="table-responsive">
                            <table id="sliderTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th width="100">Menu</th>
                                        <th>Parent</th>
                                        <th>Type</th>
                                        <th width="60">Link</th>
                                        <th width="60">Order</th>
                                        <th width="120">Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    // Ambil menu beserta nama parent jika ada
                                    $query = mysqli_query($conn, "SELECT m.*, p.menu_name AS parent_name FROM menu m LEFT JOIN menu p ON m.parent_id = p.menu_id $order_clause");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?> 
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['menu_name']) ?></td>
                                        <td><?= htmlspecialchars($row['parent_name'] ?? 'Main Menu') ?></td>
                                        <td><?= $row['menu_type'] === 'external-link' ? 'External Link' : 'Internal Link' ?></td>
                                        <td><?= htmlspecialchars($row['menu_link']) ?></td>
                                        <td><?= isset($row['menu_order']) && $row['menu_order'] !== null ? htmlspecialchars($row['menu_order']) : '-' ?></td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                                    class="btn-edit me-2" href="#" 
                                                    style="text-decoration: none; margin-right: 30px;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editMenuModal"
                                                    data-id="<?= $row['menu_id'] ?>"
                                                    data-menu="<?= htmlspecialchars($row['menu_name']) ?>"
                                                    data-parent="<?= htmlspecialchars($row['parent_id']) ?>"
                                                    data-type="<?= htmlspecialchars($row['menu_type']) ?>"
                                                    data-link="<?= htmlspecialchars($row['menu_link']) ?>"
                                                    data-order="<?= htmlspecialchars($row['menu_order'] ?? '') ?>">
                                                Edit
                                            </a>
                                            <a href="hapusmenu.php?id=<?= $row['menu_id'] ?>" style="text-decoration: none;"
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


<div class="modal fade" id="addMenuModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div> 
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Menu *</label>
                        <input type="text" name="menu" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent</label>
                        <select name="parent" class="form-select">
                            <option value="">Main Menu</option>
                            <?php foreach ($parent_list as $p): ?>
                                <option value="<?= $p['menu_id'] ?>"><?= htmlspecialchars($p['menu_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select name="type" class="form-select" required>
                            <option value="internal-link">Internal Link</option>
                            <option value="external-link">External Link</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link *</label>
                        <input type="text" name="link" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" name="menu_order" class="form-control" min="0" placeholder="Urutan (angka)" />
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


<div class="modal fade" id="editMenuModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="menu_id" id="edit-menu-id">
                    <div class="mb-3">
                        <label class="form-label">Menu *</label>
                        <input type="text" name="menu" id="edit-menu" class="form-control" required>
                    </div> 
                    <div class="mb-3">
                        <label class="form-label">Parent *</label>
                        <select name="parent" id="edit-parent" class="form-select">
                            <option value="">As Main Menu</option>
                            <?php foreach ($parent_list as $p): ?>
                                <option value="<?= $p['menu_id'] ?>"><?= htmlspecialchars($p['menu_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select name="type" id="edit-type" class="form-select" required>
                            <option value="internal-link">Internal Link</option>
                            <option value="external-link">External Link</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link *</label>
                        <input type="text" name="link" id="edit-link" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" name="menu_order" id="edit-order" class="form-control" min="0" placeholder="Urutan (angka)" />
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
            const id     = this.getAttribute('data-id');
            const menu   = this.getAttribute('data-menu');
            const parent = this.getAttribute('data-parent');
            const type   = this.getAttribute('data-type');
            const link   = this.getAttribute('data-link');
            const order  = this.getAttribute('data-order');

            document.getElementById('edit-menu-id').value = id;
            document.getElementById('edit-menu').value = menu;
            document.getElementById('edit-parent').value = parent;
            document.getElementById('edit-type').value = type;
            document.getElementById('edit-link').value = link;
            if (document.getElementById('edit-order')) document.getElementById('edit-order').value = order;
        });
    });
});
</script>

</body>
</html>