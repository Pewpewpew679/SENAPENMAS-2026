<?php
include "includes/config.php";

// ==========================================
// 1. AMBIL DATA EVENTS UNTUK DROPDOWN
// ==========================================
$events = [];
$events_res = mysqli_query($conn, "SELECT event_id, event_name FROM events ORDER BY event_name ASC");
if ($events_res) {
    while ($er = mysqli_fetch_assoc($events_res)) {
        $events[] = $er;
    }
}

/* =====================================
   2. PROSES SIMPAN SCHEDULE (ADD NEW)
===================================== */
if (isset($_POST['Simpan'])) {
    $event_id    = mysqli_real_escape_string($conn, $_POST['event_id']);
    $date_old    = mysqli_real_escape_string($conn, $_POST['date_old']);
    $date_new    = mysqli_real_escape_string($conn, $_POST['date_new']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Pastikan nama tabel sesuai (schedules atau schedule)
    $query = "INSERT INTO schedules (event_id, date_old, date_new, description) 
              VALUES ('$event_id', '$date_old', '$date_new', '$description')";
    
    if(mysqli_query($conn, $query)){
        header("Location: inputschedule.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

/* =====================================
   3. PROSES UPDATE SCHEDULE (EDIT)
===================================== */
if (isset($_POST['Update'])) {
    $schedule_id = mysqli_real_escape_string($conn, $_POST['schedule_id']);
    $event_id    = mysqli_real_escape_string($conn, $_POST['event_id']);
    $date_old    = mysqli_real_escape_string($conn, $_POST['date_old']);
    $date_new    = mysqli_real_escape_string($conn, $_POST['date_new']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "UPDATE schedules SET 
              event_id='$event_id', 
              date_old='$date_old', 
              date_new='$date_new', 
              description='$description' 
              WHERE schedule_id='$schedule_id'";

    if(mysqli_query($conn, $query)){
        header("Location: inputschedule.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
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
                <h1 class="mt-4">Schedule</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                            <i class="bi bi-plus-lg"></i> Add Schedule
                        </button>

                        <div class="table-responsive">
                            <table id="scheduleTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th>Date New</th>
                                        <th>Date Old</th>
                                        <th>Description</th>
                                        <th>Event Name</th> 
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "SELECT s.*, e.event_name 
                                                                  FROM schedules s 
                                                                  LEFT JOIN events e ON s.event_id = e.event_id 
                                                                  ORDER BY s.date_new DESC");
                                    
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('F jS Y', strtotime($row['date_new'])) ?></td>
                                        
                                        <td>
                                            <?php 
                                            // Jika date_old terisi, beri efek coret (strikethrough)
                                            if (!empty($row['date_old'])) {
                                                echo '<span style="text-decoration: line-through; color: #888;">' . htmlspecialchars($row['date_old']) . '</span>';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['description']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($row['event_name'] ?? ('ID: ' . $row['event_id'])) ?>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                               class="btn-edit me-2"
                                               style="text-decoration: none;"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#editScheduleModal"
                                               data-id="<?= $row['schedule_id'] ?>" 
                                               data-eventid="<?= $row['event_id'] ?>"
                                               data-dateold="<?= htmlspecialchars($row['date_old']) ?>"
                                               data-datenew="<?= $row['date_new'] ?>"
                                               data-desc="<?= htmlspecialchars($row['description']) ?>">
                                               Edit
                                            </a>
                                            <a href="hapusschedule.php?id=<?= $row['schedule_id'] ?>" 
                                               style="text-decoration: none; color: red;"
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

<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Event *</label>
                        <select name="event_id" class="form-select" required>
                            <option value="">-- Pilih Event --</option>
                            <?php foreach ($events as $e): ?>
                                <option value="<?= htmlspecialchars($e['event_id']) ?>">
                                    <?= htmlspecialchars($e['event_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date New (Tanggal Baru) *</label>
                        <input type="date" name="date_new" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Old (Keterangan Tanggal Lama)</label>
                        <input type="text" name="date_old" class="form-control" placeholder="Contoh: 12 Januari 2025">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea name="description" rows="3" class="form-control" required></textarea>
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

<div class="modal fade" id="editScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="schedule_id" id="edit-id">

                    <div class="mb-3">
                        <label class="form-label">Event *</label>
                        <select name="event_id" id="edit-eventid" class="form-select" required>
                            <option value="">-- Pilih Event --</option>
                            <?php foreach ($events as $e): ?>
                                <option value="<?= htmlspecialchars($e['event_id']) ?>">
                                    <?= htmlspecialchars($e['event_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date New *</label>
                        <input type="date" name="date_new" id="edit-datenew" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date Old</label>
                        <input type="text" name="date_old" id="edit-dateold" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea name="description" id="edit-desc" rows="3" class="form-control" required></textarea>
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
    if (document.getElementById("scheduleTable")) {
        new simpleDatatables.DataTable("#scheduleTable");
    }

    // Fungsi klik tombol Edit
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Ambil data dari atribut
            const id       = this.getAttribute('data-id');
            const eventId  = this.getAttribute('data-eventid');
            const dateOld  = this.getAttribute('data-dateold');
            const dateNew  = this.getAttribute('data-datenew');
            const desc     = this.getAttribute('data-desc');

            // Masukkan ke dalam input form modal
            document.getElementById('edit-id').value       = id;
            document.getElementById('edit-eventid').value  = eventId;
            document.getElementById('edit-dateold').value  = dateOld;
            document.getElementById('edit-datenew').value  = dateNew;
            document.getElementById('edit-desc').value     = desc;
        });
    });
});
</script>

</body>
</html>