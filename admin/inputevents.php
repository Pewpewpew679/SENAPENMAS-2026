<?php
    include "includes/config.php";

    // ==========================================
    // A. LOGIC: TAMBAH DATA (ADD EVENT)
    // ==========================================
    if (isset($_POST['Simpan'])) {
        $category   = mysqli_real_escape_string($conn, $_POST['event_category']);
        $name       = mysqli_real_escape_string($conn, $_POST['event_name']);
        $topic      = mysqli_real_escape_string($conn, $_POST['event_topic']);
        $year       = (int) $_POST['event_year'];
        $desc       = mysqli_real_escape_string($conn, $_POST['description']);
        $start      = $_POST['start_date'];
        $end        = $_POST['end_date'];
        $link_reg   = mysqli_real_escape_string($conn, $_POST['link_registration']);
        $linkpage   = mysqli_real_escape_string($conn, $_POST['linkpage_event']);
        
        $is_menu    = $_POST['is_menu']; 
        $menu_parent= (int) $_POST['menu_parent']; 
        $menu_order = (int) $_POST['menu_order'];
        $pub_date   = $_POST['publish_date'];
        $status     = $_POST['status']; 

        if ($status == 1) {
            mysqli_query($conn, "UPDATE events SET status = 0");
        }

        // Upload Poster Utama
        $image_name = $_FILES['poster']['name'];
        $tmp        = $_FILES['poster']['tmp_name'];
        $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
        $new_poster = time() . "_" . uniqid() . "." . $ext;
        $folder     = "images/events/"; 

        if (!is_dir($folder)) { mkdir($folder, 0777, true); }

        if (move_uploaded_file($tmp, $folder . $new_poster)) {
            $query = "INSERT INTO events 
                      (event_category, event_name, event_topic, event_year, description, start_date, end_date, poster, link_registration, linkpage_event, is_menu, menu_parent, menu_order, publish_date, status) 
                      VALUES 
                      ('$category', '$name', '$topic', '$year', '$desc', '$start', '$end', '$new_poster', '$link_reg', '$linkpage', '$is_menu', '$menu_parent', '$menu_order', '$pub_date', '$status')";
            
            if (mysqli_query($conn, $query)) {
                header("Location: inputevents.php?msg=success");
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
        exit;
    }

    // ==========================================
    // B. LOGIC: EDIT DATA (UPDATE EVENT)
    // ==========================================
    if (isset($_POST['Update'])) {
        $event_id   = (int) $_POST['event_id'];
        $old_poster = $_POST['old_poster'];
        
        $category   = mysqli_real_escape_string($conn, $_POST['event_category']);
        $name       = mysqli_real_escape_string($conn, $_POST['event_name']);
        $topic      = mysqli_real_escape_string($conn, $_POST['event_topic']);
        $year       = (int) $_POST['event_year'];
        $desc       = mysqli_real_escape_string($conn, $_POST['description']);
        $start      = $_POST['start_date'];
        $end        = $_POST['end_date'];
        $link_reg   = mysqli_real_escape_string($conn, $_POST['link_registration']);
        $linkpage   = mysqli_real_escape_string($conn, $_POST['linkpage_event']);
        
        $is_menu    = $_POST['is_menu'];
        $menu_parent= (int) $_POST['menu_parent'];
        $menu_order = (int) $_POST['menu_order'];
        $pub_date   = $_POST['publish_date'];
        $status     = $_POST['status'];

        if ($status == 1) {
            mysqli_query($conn, "UPDATE events SET status = 0 WHERE event_id != $event_id");
        }

        $final_poster = $old_poster;

        if ($_FILES['poster']['name'] != "") {
            $image_name = $_FILES['poster']['name'];
            $tmp        = $_FILES['poster']['tmp_name'];
            $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
            $new_poster = time() . "_" . uniqid() . "." . $ext;
            $folder     = "images/events/";

            if (move_uploaded_file($tmp, $folder . $new_poster)) {
                $final_poster = $new_poster;
                if (file_exists($folder . $old_poster)) { unlink($folder . $old_poster); }
            }
        }

        $query = "UPDATE events SET 
                  event_category='$category', event_name='$name', event_topic='$topic', 
                  event_year='$year', description='$desc', start_date='$start', end_date='$end', 
                  poster='$final_poster', link_registration='$link_reg', linkpage_event='$linkpage',
                  is_menu='$is_menu', menu_parent='$menu_parent', menu_order='$menu_order', publish_date='$pub_date', status='$status'
                  WHERE event_id='$event_id'";

        if(mysqli_query($conn, $query)){
            header("Location: inputevents.php?msg=updated");
        } else {
            echo "Error: " . mysqli_error($conn);
        }
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
                    <h1 class="mt-4">Manage Events</h1>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addEventModal">
                                <i class="bi bi-plus-lg"></i> + Add Event
                            </button>

                            <div class="table-responsive">
                                <table id="eventTable" class="table table-sm table-bordered table-hover align-middle" style="font-size: 0.9rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30">No</th>
                                            <th width="160">Poster</th>
                                            <th width="200">Event Name</th>
                                            <th width="60">Year</th>
                                            <th width="250">Topic</th>
                                            <th width="180">Date</th>
                                            <th width="80">Link Registration</th>
                                            <th width="100">Link page event</th>
                                            <th width="70">Status</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = mysqli_query($conn, "SELECT * FROM events ORDER BY event_id DESC");
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            $startDate = date("M j, Y", strtotime($row['start_date'])); 
                                            $endDate   = date("M j, Y", strtotime($row['end_date']));
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><img src="images/events/<?= $row['poster'] ?>" width="150" class="img-thumbnail" loading="lazy"></td>
                                            <td><?= htmlspecialchars($row['event_name']) ?></td>
                                            <td><?= $row['event_year'] ?></td>
                                            <td><?= htmlspecialchars($row['event_topic']) ?></td>
                                            <td><small>Start: <?= $startDate ?><br>End: <?= $endDate ?></small></td>
                                            <td><a href="<?= htmlspecialchars($row['link_registration']) ?>" target="_blank">#</a></td>
                                            <td><small><?= htmlspecialchars($row['linkpage_event']) ?></small></td>
                                            <td>
                                                <span class="<?= $row['status'] == 1 ? 'text-primary' : 'text-danger' ?>">
                                                    <?= $row['status'] == 1 ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="#" class="text-primary me-2 btn-edit" style="text-decoration:none;"
                                                   data-bs-toggle="modal" data-bs-target="#editEventModal"
                                                   data-id="<?= $row['event_id'] ?>"
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

                                                <a href="hapusevents.php?id=<?= $row['event_id'] ?>&poster=<?= $row['poster'] ?>" 
                                                   class="text-primary" style="text-decoration:none;"
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

    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg"> 
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Category*</label>
                                <select name="event_category" class="form-select" required>
                                    <option value="Conference">Conference</option><option value="Seminar">Seminar</option><option value="Workshop">Workshop</option>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label">Year*</label>
                                <select name="event_year" class="form-select"><?php for($y=date('Y')+2; $y>=2020; $y--) { echo "<option value='$y'>$y</option>"; } ?></select>
                            </div>
                        </div>
                        <div class="mb-3"><label class="form-label">Event Name*</label><input type="text" name="event_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Topic / Theme*</label><input type="text" name="event_topic" class="form-control" required></div>
                        
                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="editor_add"></textarea></div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Start Date*</label><input type="date" name="start_date" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">End Date*</label><input type="date" name="end_date" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Poster (JPG/PNG)*</label><input type="file" name="poster" class="form-control" accept=".jpg,.jpeg,.png" required></div>
                        
                        <div class="mb-3"><label class="form-label">Link Registration</label><input type="text" name="link_registration" class="form-control" placeholder="https://..."></div>
                        <div class="mb-3"><label class="form-label">Link Page Event</label><input type="text" name="linkpage_event" class="form-control" placeholder="event/1/NAMA-EVENT"></div>
                        
                        <div class="mb-3">
                            <label class="form-label">Make This Event As a Menu?</label>
                            <select name="is_menu" class="form-select">
                                <option value="No">No</option><option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Main Menu/Submenu</label>
                            <select name="menu_parent" class="form-select">
                                <option value="0">As Main Menu</option>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Menu Order</label><input type="number" name="menu_order" class="form-control" placeholder="Urutan"></div>
                        <div class="mb-3"><label class="form-label">Publish Date*</label><input type="date" name="publish_date" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Status*</label>
                            <select name="status" class="form-select">
                                <option value="1">Active</option><option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="Simpan" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg"> 
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="event_id" id="edit-id">
                        <input type="hidden" name="old_poster" id="edit-old-poster">
                        
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Category</label><select name="event_category" id="edit-category" class="form-select"><option value="Conference">Conference</option><option value="Seminar">Seminar</option><option value="Workshop">Workshop</option></select></div>
                            <div class="col-md-6"><label class="form-label">Year</label><select name="event_year" id="edit-year" class="form-select"><?php for($y=date('Y')+2; $y>=2020; $y--) { echo "<option value='$y'>$y</option>"; } ?></select></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Event Name</label><input type="text" name="event_name" id="edit-name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Topic</label><input type="text" name="event_topic" id="edit-topic" class="form-control" required></div>
                        
                        <div class="mb-3"><label class="form-label">Description</label><textarea name="description" id="editor_edit"></textarea></div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Start Date</label><input type="date" name="start_date" id="edit-start" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">End Date</label><input type="date" name="end_date" id="edit-end" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label">Poster</label><br><img src="" id="edit-preview" width="100" class="img-thumbnail mb-2"><input type="file" name="poster" class="form-control" accept=".jpg,.jpeg,.png"></div>
                        
                        <div class="mb-3"><label class="form-label">Link Registration</label><input type="text" name="link_registration" id="edit-link" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Link Page Event</label><input type="text" name="linkpage_event" id="edit-linkpage" class="form-control"></div>
                        
                        <div class="mb-3"><label class="form-label">Make This Event As a Menu?</label><select name="is_menu" id="edit-ismenu" class="form-select"><option value="No">No</option><option value="Yes">Yes</option></select></div>
                        <div class="mb-3"><label class="form-label">Main Menu/Submenu</label><select name="menu_parent" id="edit-menuparent" class="form-select"><option value="0">As Main Menu</option></select></div>
                        <div class="mb-3"><label class="form-label">Menu Order</label><input type="number" name="menu_order" id="edit-menuorder" class="form-control"></div>
                        <div class="mb-3"><label class="form-label">Publish Date</label><input type="date" name="publish_date" id="edit-pubdate" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Status</label><select name="status" id="edit-status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="Update" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include "bagiankode/jsscript.php"; ?>
    
    <script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>

    <input type="file" id="hidden-image-upload" style="display: none;" accept="image/jpeg, image/png, image/gif, image/webp">

    <script>
        // 1. Matikan Notifikasi Version Check
        CKEDITOR.config.versionCheck = false;

        // CSS Dasar Gambar
        CKEDITOR.addCss('img { max-width: 100%; height: auto !important; }');
        CKEDITOR.addCss('.cke_widget_wrapper { max-width: 100% !important; }');

        // CSS ALIGNMENT (Sama persis dengan yang ada di main.php, tapi tanpa .event-description)
        CKEDITOR.addCss('.image-left { float: left; margin: 0 20px 10px 0; clear: left; }');
        CKEDITOR.addCss('.image-right { float: right; margin: 0 0 10px 20px; clear: right; }');
        CKEDITOR.addCss('.image-center { display: block; margin-left: auto; margin-right: auto; text-align: center; clear: both; }');
        
        // --- TAMBAHAN PENTING ---
        // Memaksa Editor meniru jarak spasi Bootstrap (Main.php)
        CKEDITOR.addCss('p { margin-top: 0; margin-bottom: 1rem; }'); 
        CKEDITOR.addCss('h1, h2, h3, h4, h5 { margin-top: 0.5rem; margin-bottom: 0.5rem; font-weight: 500; line-height: 1.2; }');

        function createCustomEditor(elementId) {
            if (CKEDITOR.instances[elementId]) {
                CKEDITOR.instances[elementId].destroy(true);
            }

            CKEDITOR.replace(elementId, {
                height: 400,
                width: '750px',
                
                // Plugin Lengkap + Image2 (Resize)
                extraPlugins: 'uploadimage,image2,widget,lineutils,widgetselection,notification,filetools', 
                removePlugins: 'image,easyimage,cloudservices', 

                // Izinkan Resize
                image2_alignClasses: [ 'image-left', 'image-center', 'image-right' ],
                image2_disableResizer: false, 
                allowedContent: true, 

                // --- HAPUS BAGIAN 'contentsCss' DARI SINI ---
                // (Agar font kembali ke default aslinya)

                // Toolbar Lengkap + Tombol Upload Kita
                toolbar: [
                    { name: 'document', items: [ 'Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates' ] },
                    { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                    { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
                    '/',
                    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
                    { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
                    { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                    { name: 'insert', items: [ 'BtnCustomImage', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
                    '/',
                    { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                    { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                    { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
                ],

                on: {
                    pluginsLoaded: function() {
                        var editor = this;
                        
                        // Command: Buka input file hidden
                        editor.addCommand('cmdOpenUpload', {
                            exec: function(editor) {
                                var input = document.getElementById('hidden-image-upload');
                                input.setAttribute('data-target', elementId);
                                input.click();
                            }
                        });

                        // UI Tombol Toolbar
                        editor.ui.addButton('BtnCustomImage', {
                            label: 'Upload Gambar (Bisa Resize)',
                            command: 'cmdOpenUpload',
                            toolbar: 'insert',
                            icon: 'https://cdn-icons-png.flaticon.com/512/3342/3342137.png' 
                        });
                    }
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            if (document.getElementById("eventTable")) {
                new simpleDatatables.DataTable("#eventTable", { perPage: 10 });
            }

            createCustomEditor('editor_add');
            createCustomEditor('editor_edit');

            // --- LOGIC TOMBOL EDIT ---
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    var id = this.getAttribute('data-id');
                    var desc = this.getAttribute('data-desc');
                    
                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-old-poster').value = this.getAttribute('data-poster');
                    document.getElementById('edit-category').value = this.getAttribute('data-category');
                    document.getElementById('edit-year').value = this.getAttribute('data-year');
                    document.getElementById('edit-name').value = this.getAttribute('data-name');
                    document.getElementById('edit-topic').value = this.getAttribute('data-topic');
                    document.getElementById('edit-start').value = this.getAttribute('data-start');
                    document.getElementById('edit-end').value = this.getAttribute('data-end');
                    document.getElementById('edit-link').value = this.getAttribute('data-link');
                    document.getElementById('edit-linkpage').value = this.getAttribute('data-linkpage');
                    document.getElementById('edit-preview').src = 'images/events/' + this.getAttribute('data-poster');
                    
                    document.getElementById('edit-ismenu').value = this.getAttribute('data-ismenu');
                    document.getElementById('edit-menuparent').value = this.getAttribute('data-menuparent');
                    document.getElementById('edit-menuorder').value = this.getAttribute('data-menuorder');
                    document.getElementById('edit-pubdate').value = this.getAttribute('data-pubdate');
                    document.getElementById('edit-status').value = this.getAttribute('data-status');

                    if (CKEDITOR.instances['editor_edit']) {
                        CKEDITOR.instances['editor_edit'].setData(desc);
                    } else {
                        setTimeout(function() {
                            CKEDITOR.instances['editor_edit'].setData(desc);
                        }, 500);
                    }
                });
            });
        });

        // 3. LOGIC UPLOAD KE PHP
        document.getElementById('hidden-image-upload').addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            var targetEditorId = this.getAttribute('data-target');
            var formData = new FormData();
            formData.append('upload', file);

            document.body.style.cursor = 'wait';

            fetch('ckeditor_upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.uploaded === 1) {
                    // SUKSES: Insert gambar dengan lebar default 500px (aman)
                    var imgHtml = '<img src="' + data.url + '" style="max-width: 100%; height: auto;" width="500" alt="image" />';
                    CKEDITOR.instances[targetEditorId].insertHtml(imgHtml);
                } else {
                    alert("Upload Gagal: " + (data.error ? data.error.message : 'Error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Koneksi Error. Pastikan ckeditor_upload.php ada.");
            })
            .finally(() => {
                e.target.value = ''; 
                document.body.style.cursor = 'default';
            });
        });
    </script>
</body>
</html>