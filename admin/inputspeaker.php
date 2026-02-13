<?php
ob_start();
session_start();

// Cek Login
if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

// Cek Login


include "includes/config.php";

/* =====================================
   1. PROSES SIMPAN SPEAKER (ADD NEW)
===================================== */
if (isset($_POST['Simpan'])) {
    $speaker_name = mysqli_real_escape_string($conn, $_POST['speakername']);
    $information  = mysqli_real_escape_string($conn, $_POST['information']);
    $event        = mysqli_real_escape_string($conn, $_POST['event']);

    $image_name = $_FILES['photo']['name'];
    $tmp        = $_FILES['photo']['tmp_name'];
    $ext        = pathinfo($image_name, PATHINFO_EXTENSION);

    $new_name = time() . "_" . uniqid() . "." . $ext;
    $folder   = "images/";

    if (move_uploaded_file($tmp, $folder . $new_name)) {
        mysqli_query($conn, "INSERT INTO speaker (photo, speaker_name, information, event) VALUES ('$new_name', '$speaker_name', '$information', '$event')");
    }

    header("Location: inputspeaker.php");
    exit;
}

/* =====================================
   2. PROSES UPDATE SPEAKER (EDIT)
===================================== */
if (isset($_POST['Update'])) {
    $old_image   = mysqli_real_escape_string($conn, $_POST['old_image']);
    $speaker_name = mysqli_real_escape_string($conn, $_POST['speakername']);
    $information  = mysqli_real_escape_string($conn, $_POST['information']);
    $event        = mysqli_real_escape_string($conn, $_POST['event']);

    if ($_FILES['photo']['name'] != "") {
        $image_name = $_FILES['photo']['name'];
        $tmp        = $_FILES['photo']['tmp_name'];
        $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_name   = time() . "_" . uniqid() . "." . $ext;
        
        move_uploaded_file($tmp, "images/" . $new_name);
        
        if (file_exists("images/" . $old_image)) {
            unlink("images/" . $old_image);
        }

        mysqli_query($conn, "UPDATE speaker SET photo='$new_name', speaker_name='$speaker_name', information='$information', event='$event' WHERE photo='$old_image'");
    } else {
        mysqli_query($conn, "UPDATE speaker SET speaker_name='$speaker_name', information='$information', event='$event' WHERE photo='$old_image'");
    }
    header("Location: inputspeaker.php");
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
                <h1 class="mt-4">Speaker</h1>

                <div class="card mb-4">
                    <div class="card-body">
                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addSpeakerModal">
                            <i class="bi bi-plus-lg"></i> Add Speaker
                        </button>

                        <div class="table-responsive">
                            <table id="speakerTable" class="table table-sm table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">No</th>
                                        <th width="100">Photo</th>
                                        <th>Speaker Name</th>
                                        <th>Information</th>
                                        <th width="60">Event</th>
                                        <th width="120">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($conn, "SELECT * FROM speaker ORDER BY speaker_name DESC");
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><img src="images/<?= $row['photo'] ?>" width="90" class="img-thumbnail"></td>
                                        <td><?= htmlspecialchars($row['speaker_name']) ?></td>
                                        <td><?= htmlspecialchars($row['information']) ?></td>
                                        <td><?= htmlspecialchars($row['event']) ?></td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                                    class="btn-edit me-2"href="#" 
                                                    style="text-decoration: none; margin-right: 30px;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editSpeakerModal"
                                                    data-photo="<?= $row['photo'] ?>"
                                                    data-speaker_name="<?= htmlspecialchars($row['speaker_name']) ?>"
                                                    data-information="<?= htmlspecialchars($row['information']) ?>"
                                                    data-event="<?= htmlspecialchars($row['event']) ?>">
                                                Edit
                                            </a>
                                            <a href="hapus_speaker.php?speaker_name=<?= urlencode($row['speaker_name']) ?>" style="text-decoration: none;"
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


<div class="modal fade" id="addSpeakerModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Add Speaker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Speaker Name *</label>
                        <input type="text" name="speakername" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Information *</label>
                        <input type="text" name="information" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photo *</label>
                        <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event *</label>
                        <select name="event" class="form-select">
                            <option value="1">SENAPENMAS 2025</option>
                            <option value="0">SENAPENMAS 2026</option>
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


<div class="modal fade" id="editSpeakerModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Speaker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="old_image" id="edit-old-image">
                    <div class="mb-3">
                        <label class="form-label">Speaker Name *</label>
                        <input type="text" name="speakername" id="edit-speakername" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Information *</label>
                        <input type="text" name="information" id="edit-information" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Current Image</label>
                        <img src="" id="edit-preview" width="120" class="img-thumbnail mb-2">
                        <input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Event *</label>
                        <select name="event" id="edit-event" class="form-select">
                            <option value="1">SENAPENMAS 2025</option>
                            <option value="0">SENAPENMAS 2026</option>
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
    if (document.getElementById("speakerTable")) {
        new simpleDatatables.DataTable("#speakerTable");
    }

    // Fungsi klik tombol Edit
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const photo  = this.getAttribute('data-photo');
            const speakername  = this.getAttribute('data-speaker_name');
            const information   = this.getAttribute('data-information');
            const event = this.getAttribute('data-event');

            document.getElementById('edit-old-image').value = photo;
            document.getElementById('edit-speakername').value = speakername;
            document.getElementById('edit-information').value = information;
            document.getElementById('edit-event').value = event;
            document.getElementById('edit-preview').src = 'images/' + photo;
        });
    });
});
</script>

</body>
</html>