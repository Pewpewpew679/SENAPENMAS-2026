<?php
    ob_start();
    session_start();

    if (!isset($_SESSION['useremail'])) {
        header("Location: login.php");
        exit;
    }

    include "includes/config.php";

    // Ambil data menu untuk dropdown parent
    $parent_list = [];
    $parent_res = mysqli_query($conn, "SELECT menu_id, menu_name FROM menu WHERE parent_id IS NULL ORDER BY menu_name");
    while ($r = mysqli_fetch_assoc($parent_res)) { 
        $parent_list[] = $r; 
    }

    // ==========================================
    // A. LOGIC: TAMBAH DATA (ADD PAGE)
    // ==========================================
    if (isset($_POST['Simpan'])) {
        $page_title = mysqli_real_escape_string($conn, $_POST['page_title']);
        $content    = mysqli_real_escape_string($conn, $_POST['page_content']);
        $pub_date   = $_POST['publish_date'];
        $status     = $_POST['status'];
        
        // Upload Cover
        $cover_name = "";
        if ($_FILES['page_cover']['name'] != "") {
            $image_name = $_FILES['page_cover']['name'];
            $tmp        = $_FILES['page_cover']['tmp_name'];
            $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
            $new_cover  = time() . "_" . uniqid() . "." . $ext;
            $folder     = "../frontend/images/pages/";

            if (!is_dir($folder)) { mkdir($folder, 0777, true); }

            if (move_uploaded_file($tmp, $folder . $new_cover)) {
                $cover_name = $new_cover;
            }
        }

        $query = "INSERT INTO pages 
                  (page_title, page_content, page_cover, publish_date, status) 
                  VALUES 
                  ('$page_title', '$content', '$cover_name', '$pub_date', '$status')";
        
        if (mysqli_query($conn, $query)) {
            $page_id = mysqli_insert_id($conn);
            
            // Cek apakah page ini dibuat sebagai menu
            if (isset($_POST['as_menu']) && $_POST['as_menu'] == 'Yes') {
                $parent_id = isset($_POST['parent_menu']) && $_POST['parent_menu'] != '' ? mysqli_real_escape_string($conn, $_POST['parent_menu']) : NULL;
                $menu_order = isset($_POST['menu_order']) && $_POST['menu_order'] != '' ? intval($_POST['menu_order']) : NULL;
                
                $parent_sql = $parent_id ? "'" . $parent_id . "'" : "NULL";
                $order_sql = $menu_order ? $menu_order : "NULL";
                
                // Generate SEO-friendly URL: page/{id}/{page-title}
                $page_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $page_title)));
                $menu_link = "page/" . $page_id . "/" . $page_slug;
                
                $menu_query = "INSERT INTO menu (menu_name, parent_id, menu_type, menu_link, menu_order) 
                               VALUES ('$page_title', $parent_sql, 'internal-link', '$menu_link', $order_sql)";
                mysqli_query($conn, $menu_query);
            }
            
            header("Location: inputpage.php?msg=success");
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        exit;
    }

    // ==========================================
    // B. LOGIC: EDIT DATA (UPDATE PAGE)
    // ==========================================
    if (isset($_POST['Update'])) {
        $page_id    = (int) $_POST['page_id'];
        $old_cover  = $_POST['old_cover'];
        
        $page_title = mysqli_real_escape_string($conn, $_POST['page_title']);
        $content    = mysqli_real_escape_string($conn, $_POST['page_content']);
        $pub_date   = $_POST['publish_date'];
        $status     = $_POST['status'];

        $final_cover = $old_cover;

        if ($_FILES['page_cover']['name'] != "") {
            $image_name = $_FILES['page_cover']['name'];
            $tmp        = $_FILES['page_cover']['tmp_name'];
            $ext        = pathinfo($image_name, PATHINFO_EXTENSION);
            $new_cover  = time() . "_" . uniqid() . "." . $ext;
            $folder     = "../frontend/images/pages/";

            if (move_uploaded_file($tmp, $folder . $new_cover)) {
                $final_cover = $new_cover;
                if ($old_cover != "" && file_exists($folder . $old_cover)) { 
                    unlink($folder . $old_cover); 
                }
            }
        }

        $query = "UPDATE pages SET 
                  page_title='$page_title', page_content='$content', page_cover='$final_cover',
                  publish_date='$pub_date', status='$status'
                  WHERE page_id='$page_id'";

        if(mysqli_query($conn, $query)){
            // Update menu jika ada
            if (isset($_POST['as_menu']) && $_POST['as_menu'] == 'Yes') {
                $parent_id = isset($_POST['parent_menu']) && $_POST['parent_menu'] != '' ? mysqli_real_escape_string($conn, $_POST['parent_menu']) : NULL;
                $menu_order = isset($_POST['menu_order']) && $_POST['menu_order'] != '' ? intval($_POST['menu_order']) : NULL;
                
                $parent_sql = $parent_id ? "'" . $parent_id . "'" : "NULL";
                $order_sql = $menu_order ? $menu_order : "NULL";
                
                $page_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $page_title)));
                $menu_link = "page/" . $page_id . "/" . $page_slug;
                
                $check_menu = mysqli_query($conn, "SELECT menu_id FROM menu WHERE menu_link LIKE 'page/$page_id/%'");
                if (mysqli_num_rows($check_menu) > 0) {
                    $menu_query = "UPDATE menu SET menu_name='$page_title', parent_id=$parent_sql, menu_link='$menu_link', menu_order=$order_sql WHERE menu_link LIKE 'page/$page_id/%'";
                } else {
                    $menu_query = "INSERT INTO menu (menu_name, parent_id, menu_type, menu_link, menu_order) 
                                   VALUES ('$page_title', $parent_sql, 'internal-link', '$menu_link', $order_sql)";
                }
                mysqli_query($conn, $menu_query);
            } else {
                mysqli_query($conn, "DELETE FROM menu WHERE menu_link LIKE 'page/$page_id/%'");
            }
            
            header("Location: inputpage.php?msg=updated");
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
                    <h1 class="mt-4">Page</h1>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addPageModal">
                                        <i class="bi bi-plus-lg"></i> Add Page
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="pageTable" class="table table-sm table-bordered table-hover align-middle" style="font-size: 0.9rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30">No</th>
                                            <th width="80">Cover</th>
                                            <th width="250">Page Title</th>
                                            <th width="100">Publish Status</th>
                                            <th width="150">Link Page</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = mysqli_query($conn, "SELECT * FROM pages ORDER BY page_id DESC");
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            $statusText = '';
                                            if ($row['status'] == 'Publish') {
                                                $statusText = '<span class="text-success">Publish</span>';
                                            } else {
                                                $statusText = '<span class="text-secondary">Draft</span>';
                                            }
                                            
                                            if (!empty($row['page_cover']) && file_exists('../frontend/images/pages/' . $row['page_cover'])) {
                                                $coverImage = '../frontend/images/pages/' . $row['page_cover'];
                                            } else {
                                                $coverImage = 'images/no-image-found.png';
                                            }
                                            
                                            $page_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['page_title'])));
                                            $page_link = "page/" . $row['page_id'] . "/" . $page_slug;
                                            $menu_check = mysqli_query($conn, "SELECT m.*, p.menu_name as parent_name FROM menu m LEFT JOIN menu p ON m.parent_id = p.menu_id WHERE m.menu_link LIKE 'page/" . $row['page_id'] . "/%'");
                                            $menu_data = mysqli_fetch_assoc($menu_check);
                                            $is_menu = $menu_data ? 'Yes' : 'No';
                                            $parent_id = $menu_data && $menu_data['parent_id'] ? $menu_data['parent_id'] : '';
                                            $menu_order_val = $menu_data && $menu_data['menu_order'] ? $menu_data['menu_order'] : '';
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <img src="<?= $coverImage ?>" width="70" class="img-thumbnail" loading="lazy">
                                            </td>
                                            <td><?= htmlspecialchars($row['page_title']) ?></td>
                                            <td><?= $statusText ?></td>
                                            <td><a href="<?= $page_link ?>" target="_blank"><?= $page_link ?></a></td>
                                            <td>
                                                <a href="#" class="text-primary me-2 btn-edit" style="text-decoration:none;"
                                                   data-bs-toggle="modal" data-bs-target="#editPageModal"
                                                   data-id="<?= $row['page_id'] ?>"
                                                   data-cover="<?= $row['page_cover'] ?>"
                                                   data-title="<?= htmlspecialchars($row['page_title']) ?>"
                                                   data-content="<?= htmlspecialchars($row['page_content']) ?>"
                                                   data-pubdate="<?= $row['publish_date'] ?>"
                                                   data-status="<?= $row['status'] ?>"
                                                   data-asmenu="<?= $is_menu ?>"
                                                   data-parentid="<?= $parent_id ?>"
                                                   data-menuorder="<?= $menu_order_val ?>">
                                                   Edit
                                                </a>

                                                <a href="hapuspage.php?id=<?= $row['page_id'] ?>&cover=<?= $row['page_cover'] ?>" 
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

    <div class="modal fade" id="addPageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Page</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Page Title*</label>
                            <input type="text" name="page_title" class="form-control" placeholder="Page Title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Page Content*</label>
                            <textarea name="page_content" id="editor_add"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cover (JPG/PNG)</label>
                            <input type="file" name="page_cover" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Max: 5MB</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Make This Page As a Menu?*</label>
                            <select name="as_menu" id="add-as-menu" class="form-select" required>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        
                        <div id="add-menu-options" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Main Menu/Submenu</label>
                                <select name="parent_menu" id="add-parent-menu" class="form-select">
                                    <option value="">As Main Menu</option>
                                    <?php foreach ($parent_list as $p): ?>
                                        <option value="<?= $p['menu_id'] ?>">Submenu > <?= htmlspecialchars($p['menu_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Menu Order (Leave blank if it's not a menu)</label>
                                <input type="number" name="menu_order" id="add-menu-order" class="form-control" min="0" placeholder="Urutan">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Publish Date*</label>
                            <input type="date" name="publish_date" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status*</label>
                            <select name="status" class="form-select">
                                <option value="Draft">Draft</option>
                                <option value="Publish">Publish</option>
                                <option value="Un Publish">Un Publish</option>
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

    <div class="modal fade" id="editPageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Page</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="page_id" id="edit-id">
                        <input type="hidden" name="old_cover" id="edit-old-cover">
                        
                        <div class="mb-3">
                            <label class="form-label">Page Title*</label>
                            <input type="text" name="page_title" id="edit-title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Page Content*</label>
                            <textarea name="page_content" id="editor_edit"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Cover</label><br>
                            <img src="" id="edit-preview" width="100" class="img-thumbnail mb-2">
                            <input type="file" name="page_cover" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Max: 5MB</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Make This Page As a Menu?*</label>
                            <select name="as_menu" id="edit-as-menu" class="form-select" required>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        
                        <div id="edit-menu-options" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Main Menu/Submenu</label>
                                <select name="parent_menu" id="edit-parent-menu" class="form-select">
                                    <option value="">As Main Menu</option>
                                    <?php foreach ($parent_list as $p): ?>
                                        <option value="<?= $p['menu_id'] ?>">Submenu > <?= htmlspecialchars($p['menu_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Menu Order (Leave blank if it's not a menu)</label>
                                <input type="number" name="menu_order" id="edit-menu-order" class="form-control" min="0" placeholder="Urutan">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Publish Date*</label>
                            <input type="date" name="publish_date" id="edit-pubdate" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status*</label>
                            <select name="status" id="edit-status" class="form-select">
                                <option value="Draft">Draft</option>
                                <option value="Publish">Publish</option>
                                <option value="Un Publish">Un Publish</option>
                            </select>
                        </div>
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

        // --- PENTING: ATUR CSS MANUAL AGAR FONT NORMAL & GAMBAR TIDAK BABLAS ---
        // Ini menjaga font tetap standar CKEditor (kecil/rapi) tapi gambar responsif.
        CKEDITOR.addCss('img { max-width: 100%; height: auto !important; }');
        CKEDITOR.addCss('.cke_widget_wrapper { max-width: 100% !important; }');

        function createCustomEditor(elementId) {
            if (CKEDITOR.instances[elementId]) {
                CKEDITOR.instances[elementId].destroy(true);
            }

            CKEDITOR.replace(elementId, {
                height: 400,
                
                // Plugin Lengkap + Image2 (Resize)
                extraPlugins: 'uploadimage,image2,widget,lineutils,widgetselection,notification,filetools', 
                // Hapus plugin image standar
                removePlugins: 'image,easyimage,cloudservices', 

                // Konfigurasi Resize
                image2_alignClasses: [ 'image-left', 'image-center', 'image-right' ],
                image2_disableResizer: false, 
                allowedContent: true, 

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
            // Init DataTable
            if (document.getElementById("pageTable")) {
                new simpleDatatables.DataTable("#pageTable", { perPage: 10 });
            }

            // Init Editors
            createCustomEditor('editor_add');
            createCustomEditor('editor_edit');

            // --- LOGIC TOMBOL EDIT ---
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Ambil data
                    var id = this.getAttribute('data-id');
                    var content = this.getAttribute('data-content'); // Ambil konten page
                    
                    // Isi Form
                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-old-cover').value = this.getAttribute('data-cover');
                    document.getElementById('edit-title').value = this.getAttribute('data-title');
                    document.getElementById('edit-pubdate').value = this.getAttribute('data-pubdate');
                    document.getElementById('edit-status').value = this.getAttribute('data-status');

                    // Logic Menu Options
                    const asMenu = this.getAttribute('data-asmenu');
                    document.getElementById('edit-as-menu').value = asMenu;
                    if (asMenu === 'Yes') {
                        document.getElementById('edit-menu-options').style.display = 'block';
                        document.getElementById('edit-parent-menu').value = this.getAttribute('data-parentid');
                        document.getElementById('edit-menu-order').value = this.getAttribute('data-menuorder');
                    } else {
                        document.getElementById('edit-menu-options').style.display = 'none';
                    }

                    // Preview Cover
                    let coverFile = this.getAttribute('data-cover');
                    if (coverFile && coverFile != '') {
                        document.getElementById('edit-preview').src = '../frontend/images/pages/' + coverFile;
                    } else {
                        document.getElementById('edit-preview').src = 'images/no-image-found.png';
                    }

                    // Isi CKEditor
                    if (CKEDITOR.instances['editor_edit']) {
                        CKEDITOR.instances['editor_edit'].setData(content);
                    } else {
                        setTimeout(function() {
                            CKEDITOR.instances['editor_edit'].setData(content);
                        }, 500);
                    }
                });
            });

            // Toggle Menu Options (Listener)
            const addMenuSelect = document.getElementById('add-as-menu');
            if(addMenuSelect){
                addMenuSelect.addEventListener('change', function() {
                    document.getElementById('add-menu-options').style.display = (this.value === 'Yes') ? 'block' : 'none';
                });
            }
            
            const editMenuSelect = document.getElementById('edit-as-menu');
            if(editMenuSelect){
                editMenuSelect.addEventListener('change', function() {
                    document.getElementById('edit-menu-options').style.display = (this.value === 'Yes') ? 'block' : 'none';
                });
            }
        });

        // 3. LOGIC UPLOAD KE PHP
        document.getElementById('hidden-image-upload').addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            var targetEditorId = this.getAttribute('data-target');
            var formData = new FormData();
            formData.append('upload', file);

            document.body.style.cursor = 'wait';

            // Pastikan ckeditor_upload.php ada di folder yang sama
            fetch('ckeditor_upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.uploaded === 1) {
                    var imgHtml = '<img src="' + data.url + '" width="500" alt="image" />';
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