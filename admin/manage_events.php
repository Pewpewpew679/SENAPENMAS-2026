<?php
ob_start();
session_start();

if (!isset($_SESSION['useremail'])) {
    header("Location: login.php");
    exit;
}

include "includes/config.php";

// ==========================================
// FUNGSI HELPER
// ==========================================
function uploadPoster($file) {
    $folder = "images/events/";
    if (!is_dir($folder)) { mkdir($folder, 0777, true); }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_name = time() . "_" . uniqid() . "." . $ext;
    
    if (move_uploaded_file($file['tmp_name'], $folder . $new_name)) {
        return $new_name;
    }
    return false;
}

function sanitizeInput($conn, $data) {
    return mysqli_real_escape_string($conn, $data);
}

// ==========================================
// A. SET HOMEPAGE
// ==========================================
if (isset($_POST['process_homepage'])) {
    $id = (int) $_POST['id'];
    $status = $_POST['process_homepage']; 

    $query = "UPDATE events SET homepage = '$status' WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        header("Location: manage_events.php?msg=status_updated");
        exit;
    }
}

// ==========================================
// B. TAMBAH EVENT
// ==========================================
if (isset($_POST['Simpan'])) {
    $new_poster = uploadPoster($_FILES['poster']);
    
    if ($new_poster) {
        $data = [
            'category'   => sanitizeInput($conn, $_POST['event_category']),
            'name'       => sanitizeInput($conn, $_POST['event_name']),
            'topic'      => sanitizeInput($conn, $_POST['event_topic']),
            'year'       => (int) $_POST['event_year'],
            'desc'       => sanitizeInput($conn, $_POST['description']),
            'start'      => $_POST['start_date'],
            'end'        => $_POST['end_date'],
            'link_reg'   => sanitizeInput($conn, $_POST['link_registration']),
            'linkpage'   => sanitizeInput($conn, $_POST['linkpage_event']),
            'is_menu'    => $_POST['is_menu'],
            'menu_parent'=> (int) $_POST['menu_parent'],
            'menu_order' => (int) $_POST['menu_order'],
            'pub_date'   => $_POST['publish_date'],
            'status'     => $_POST['status']
        ];
        
        $query = "INSERT INTO events 
                  (event_category, event_name, event_topic, event_year, description, start_date, end_date, poster, link_registration, linkpage_event, is_menu, menu_parent, menu_order, publish_date, status, homepage) 
                  VALUES 
                  ('{$data['category']}', '{$data['name']}', '{$data['topic']}', {$data['year']}, '{$data['desc']}', '{$data['start']}', '{$data['end']}', '$new_poster', '{$data['link_reg']}', '{$data['linkpage']}', '{$data['is_menu']}', {$data['menu_parent']}, {$data['menu_order']}, '{$data['pub_date']}', '{$data['status']}', 'No')";
        
        if (mysqli_query($conn, $query)) {
            header("Location: manage_events.php?msg=success");
            exit;
        }
    }
}

// ==========================================
// C. UPDATE EVENT
// ==========================================
if (isset($_POST['Update'])) {
    $id = (int) $_POST['id'];
    $old_poster = $_POST['old_poster'];
    
    $data = [
        'category'   => sanitizeInput($conn, $_POST['event_category']),
        'name'       => sanitizeInput($conn, $_POST['event_name']),
        'topic'      => sanitizeInput($conn, $_POST['event_topic']),
        'year'       => (int) $_POST['event_year'],
        'desc'       => sanitizeInput($conn, $_POST['description']),
        'start'      => $_POST['start_date'],
        'end'        => $_POST['end_date'],
        'link_reg'   => sanitizeInput($conn, $_POST['link_registration']),
        'linkpage'   => sanitizeInput($conn, $_POST['linkpage_event']),
        'is_menu'    => $_POST['is_menu'],
        'menu_parent'=> (int) $_POST['menu_parent'],
        'menu_order' => (int) $_POST['menu_order'],
        'pub_date'   => $_POST['publish_date'],
        'status'     => $_POST['status']
    ];

    $final_poster = $old_poster;

    if (!empty($_FILES['poster']['name'])) {
        $new_poster = uploadPoster($_FILES['poster']);
        if ($new_poster) {
            $final_poster = $new_poster;
            $old_path = "images/events/" . $old_poster;
            if (file_exists($old_path)) { unlink($old_path); }
        }
    }

    $query = "UPDATE events SET 
              event_category='{$data['category']}', event_name='{$data['name']}', event_topic='{$data['topic']}', 
              event_year={$data['year']}, description='{$data['desc']}', start_date='{$data['start']}', end_date='{$data['end']}', 
              poster='$final_poster', link_registration='{$data['link_reg']}', linkpage_event='{$data['linkpage']}',
              is_menu='{$data['is_menu']}', menu_parent={$data['menu_parent']}, menu_order={$data['menu_order']}, publish_date='{$data['pub_date']}', status='{$data['status']}'
              WHERE id=$id";

    if(mysqli_query($conn, $query)){
        header("Location: manage_events.php?msg=updated");
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
                    <h1 class="mt-4">Manage Events</h1>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="bi bi-plus-lg"></i> Add Event
                            </button>

                            <div class="table-responsive">
                                <table id="eventTable" class="table table-sm table-bordered table-hover align-middle" style="font-size: 0.9rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30">No</th>
                                            <th width="80">Poster</th>
                                            <th>Event Name</th>
                                            <th>Year</th>
                                            <th>Topic</th>
                                            <th width="150">Date</th>
                                            <th>Link</th>
                                            <th>Status</th>
                                            <th>Homepage</th>
                                            <th>Page Link</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = mysqli_query($conn, "SELECT * FROM events ORDER BY id DESC");
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            $startDate = date("M j, Y", strtotime($row['start_date'])); 
                                            $endDate = date("M j, Y", strtotime($row['end_date']));
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><img src="images/events/<?= $row['poster'] ?>" width="70" class="img-thumbnail" loading="lazy"></td>
                                            <td><?= htmlspecialchars($row['event_name']) ?></td>
                                            <td><?= $row['event_year'] ?></td>
                                            <td><?= htmlspecialchars(substr($row['event_topic'], 0, 50)) ?>...</td>
                                            <td><small>Start: <?= $startDate ?><br>End: <?= $endDate ?></small></td>
                                            <td><a href="<?= htmlspecialchars($row['link_registration']) ?>" target="_blank" class="btn btn-sm btn-link">Link</a></td>
                                            <td>
                                                <span class="badge bg-<?= $row['status'] == 1 ? 'success' : 'secondary' ?>">
                                                    <?= $row['status'] == 1 ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $row['homepage'] == 'Yes' ? 'primary' : 'light text-dark border' ?>">
                                                    <?= $row['homepage'] ?>
                                                </span>
                                            </td>
                                            <td><small><?= htmlspecialchars($row['linkpage_event']) ?></small></td>
                                            <td>
                                                <a href="#" class="text-primary d-block mb-1 btn-set-homepage" 
                                                   style="text-decoration:none;"
                                                   data-id="<?= $row['id'] ?>"
                                                   data-name="<?= htmlspecialchars($row['event_name']) ?>">
                                                   Set Homepage
                                                </a>
                                                
                                                <a href="#" class="text-info me-2 btn-edit" style="text-decoration:none;"
                                                   data-bs-toggle="modal" data-bs-target="#editEventModal"
                                                   data-id="<?= $row['id'] ?>"
                                                   data-poster="<?= $row['poster'] ?>"
                                                   data-category="<?= $row['event_category'] ?>"
                                                   data-name="<?= htmlspecialchars($row['event_name']) ?>"
                                                   data-topic="<?= htmlspecialchars($row['event_topic']) ?>"
                                                   data-year="<?= $row['event_year'] ?>"
                                                   data-desc="<?= htmlspecialchars($row['description']) ?>"
                                                   data-start="<?= $row['start_date'] ?>"
                                                   data-end="<?= $row['end_date'] ?>"
                                                   data-link="<?= htmlspecialchars($row['link_registration']) ?>"
                                                   data-linkpage="<?= htmlspecialchars($row['linkpage_event']) ?>"
                                                   data-ismenu="<?= $row['is_menu'] ?>"
                                                   data-menuparent="<?= $row['menu_parent'] ?>"
                                                   data-menuorder="<?= $row['menu_order'] ?>"
                                                   data-pubdate="<?= $row['publish_date'] ?>"
                                                   data-status="<?= $row['status'] ?>">
                                                   Edit
                                                </a>

                                                <a href="hapusevent.php?id=<?= $row['id'] ?>&poster=<?= $row['poster'] ?>" 
                                                   class="text-danger" style="text-decoration:none;"
                                                   onclick="return confirm('Yakin hapus data ini?')">Delete</a>
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

    <!-- Modal Set Homepage -->
    <div class="modal fade" id="setHomepageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered"> 
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Set Event Homepage</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="homepage-id">
                        <p>Display event "<strong id="homepage-event-name"></strong>" on homepage?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="process_homepage" value="No" class="btn btn-secondary">No</button>
                        <button type="submit" name="process_homepage" value="Yes" class="btn btn-primary">Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Add Event -->
    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category*</label>
                                <select name="event_category" class="form-select" required>
                                    <option value="Conference">Conference</option>
                                    <option value="Seminar">Seminar</option>
                                    <option value="Workshop">Workshop</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year*</label>
                                <select name="event_year" class="form-select">
                                    <?php for($y=date('Y')+2; $y>=2020; $y--) { echo "<option value='$y'>$y</option>"; } ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Event Name*</label>
                            <input type="text" name="event_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Topic / Theme*</label>
                            <input type="text" name="event_topic" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editor_add" rows="4"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date*</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date*</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Poster (JPG/PNG)*</label>
                            <input type="file" name="poster" class="form-control" accept=".jpg,.jpeg,.png" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link Registration</label>
                            <input type="url" name="link_registration" class="form-control" placeholder="https://...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link Page Event</label>
                            <input type="text" name="linkpage_event" class="form-control" placeholder="event/1/NAMA-EVENT">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">As Menu?</label>
                                <select name="is_menu" class="form-select">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Menu Parent</label>
                                <select name="menu_parent" class="form-select">
                                    <option value="0">Main Menu</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Menu Order</label>
                                <input type="number" name="menu_order" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Publish Date*</label>
                                <input type="date" name="publish_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status*</label>
                                <select name="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="Simpan" class="btn btn-primary">Save Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Event -->
    <div class="modal fade" id="editEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <input type="hidden" name="old_poster" id="edit-old-poster">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="event_category" id="edit-category" class="form-select">
                                    <option value="Conference">Conference</option>
                                    <option value="Seminar">Seminar</option>
                                    <option value="Workshop">Workshop</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year</label>
                                <select name="event_year" id="edit-year" class="form-select">
                                    <?php for($y=date('Y')+2; $y>=2020; $y--) { echo "<option value='$y'>$y</option>"; } ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Event Name</label>
                            <input type="text" name="event_name" id="edit-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Topic</label>
                            <input type="text" name="event_topic" id="edit-topic" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editor_edit" rows="4"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" id="edit-start" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" id="edit-end" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Poster</label><br>
                            <img src="" id="edit-preview" width="120" class="img-thumbnail mb-2">
                            <input type="file" name="poster" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Leave empty to keep current poster</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link Registration</label>
                            <input type="url" name="link_registration" id="edit-link" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link Page Event</label>
                            <input type="text" name="linkpage_event" id="edit-linkpage" class="form-control">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">As Menu?</label>
                                <select name="is_menu" id="edit-ismenu" class="form-select">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Menu Parent</label>
                                <select name="menu_parent" id="edit-menuparent" class="form-select">
                                    <option value="0">Main Menu</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Menu Order</label>
                                <input type="number" name="menu_order" id="edit-menuorder" class="form-control">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Publish Date</label>
                                <input type="date" name="publish_date" id="edit-pubdate" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit-status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="Update" class="btn btn-primary">Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "bagiankode/jsscript.php"; ?>
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script>
        // Config CKEditor
        CKEDITOR.config.versionCheck = false;
        CKEDITOR.replace('editor_add', { height: 150 });
        const editorEdit = CKEDITOR.replace('editor_edit', { height: 150 });

        document.addEventListener("DOMContentLoaded", function () {
            // Init DataTable
            if (document.getElementById("eventTable")) {
                new simpleDatatables.DataTable("#eventTable", { 
                    perPage: 10,
                    labels: {
                        placeholder: "Search events...",
                        noRows: "No events found"
                    }
                });
            }

            // Set Homepage Modal
            document.querySelectorAll('.btn-set-homepage').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const modal = new bootstrap.Modal(document.getElementById('setHomepageModal'));
                    document.getElementById('homepage-id').value = this.dataset.id;
                    document.getElementById('homepage-event-name').textContent = this.dataset.name;
                    modal.show();
                });
            });

            // Edit Event Modal
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function () {
                    const data = this.dataset;
                    document.getElementById('edit-id').value = data.id;
                    document.getElementById('edit-old-poster').value = data.poster;
                    document.getElementById('edit-category').value = data.category;
                    document.getElementById('edit-year').value = data.year;
                    document.getElementById('edit-name').value = data.name;
                    document.getElementById('edit-topic').value = data.topic;
                    document.getElementById('edit-start').value = data.start;
                    document.getElementById('edit-end').value = data.end;
                    document.getElementById('edit-link').value = data.link;
                    document.getElementById('edit-linkpage').value = data.linkpage;
                    document.getElementById('edit-preview').src = 'images/events/' + data.poster;
                    document.getElementById('edit-ismenu').value = data.ismenu;
                    document.getElementById('edit-menuparent').value = data.menuparent;
                    document.getElementById('edit-menuorder').value = data.menuorder;
                    document.getElementById('edit-pubdate').value = data.pubdate;
                    document.getElementById('edit-status').value = data.status;
                    editorEdit.setData(data.desc);
                });
            });
        });
    </script>
</body>
</html>