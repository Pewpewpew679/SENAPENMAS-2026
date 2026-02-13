<?php
    ob_start();
    session_start();

    if (!isset($_SESSION['useremail'])) {
        header("Location: login.php");
        exit;
    }

    include "includes/config.php";

    // Cari menu_id dari "Past Conferences" untuk dijadikan parent
    $past_conf_parent = mysqli_query($conn, "SELECT menu_id FROM menu WHERE menu_name = 'Past Conferences' LIMIT 1");
    $parent_row = mysqli_fetch_assoc($past_conf_parent);
    $parent_menu_id = $parent_row ? $parent_row['menu_id'] : null;

    // ==========================================
    // A. LOGIC: TAMBAH DATA (ADD PAST CONFERENCE)
    // ==========================================
    if (isset($_POST['Simpan'])) {
        $event_id   = (int) $_POST['event_id'];
        $menu_order = isset($_POST['menu_order']) && $_POST['menu_order'] != '' ? (int) $_POST['menu_order'] : NULL;
        $pub_date   = $_POST['publish_date'];
        $status     = $_POST['status'];

        // Cek apakah event sudah ada di past conferences
        $check = mysqli_query($conn, "SELECT * FROM past_conferences WHERE event_id = $event_id");
        if (mysqli_num_rows($check) > 0) {
            header("Location: inputpastconference.php?msg=duplicate");
            exit;
        }

        $order_sql = $menu_order ? $menu_order : "NULL";
        
        $query = "INSERT INTO past_conferences 
                  (event_id, menu_order, publish_date, status) 
                  VALUES 
                  ($event_id, $order_sql, '$pub_date', '$status')";
        
        if (mysqli_query($conn, $query)) {
            // Ambil data event untuk membuat menu
            $event_data = mysqli_query($conn, "SELECT event_name FROM events WHERE event_id = $event_id");
            $event = mysqli_fetch_assoc($event_data);
            
            if ($event && $status == 'Publish') {
                // Generate SEO-friendly URL: event/{id}/{event-name}
                $event_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $event['event_name'])));
                $menu_link = "event/" . $event_id . "/" . $event_slug;
                
                $parent_sql = $parent_menu_id ? $parent_menu_id : "NULL";
                
                // Insert menu sebagai submenu dari Past Conferences
                $menu_query = "INSERT INTO menu (menu_name, parent_id, menu_type, menu_link, menu_order) 
                               VALUES ('" . mysqli_real_escape_string($conn, $event['event_name']) . "', $parent_sql, 'internal-link', '$menu_link', $order_sql)";
                mysqli_query($conn, $menu_query);
            }
            
            header("Location: inputpastconference.php?msg=success");
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        exit;
    }

    // ==========================================
    // B. LOGIC: EDIT DATA (UPDATE PAST CONFERENCE)
    // ==========================================
    if (isset($_POST['Update'])) {
        $past_conf_id = (int) $_POST['past_conf_id'];
        $event_id     = (int) $_POST['event_id'];
        $menu_order   = isset($_POST['menu_order']) && $_POST['menu_order'] != '' ? (int) $_POST['menu_order'] : NULL;
        $pub_date     = $_POST['publish_date'];
        $status       = $_POST['status'];

        $order_sql = $menu_order ? $menu_order : "NULL";

        $query = "UPDATE past_conferences SET 
                  menu_order=$order_sql, publish_date='$pub_date', status='$status'
                  WHERE past_conf_id=$past_conf_id";

        if(mysqli_query($conn, $query)){
            // Update menu
            $event_data = mysqli_query($conn, "SELECT event_name FROM events WHERE event_id = $event_id");
            $event = mysqli_fetch_assoc($event_data);
            
            if ($event) {
                $event_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $event['event_name'])));
                $menu_link = "event/" . $event_id . "/" . $event_slug;
                
                $parent_sql = $parent_menu_id ? $parent_menu_id : "NULL";
                
                // Cek apakah menu sudah ada
                $check_menu = mysqli_query($conn, "SELECT menu_id FROM menu WHERE menu_link = '$menu_link'");
                
                if ($status == 'Publish') {
                    if (mysqli_num_rows($check_menu) > 0) {
                        // Update existing menu
                        $menu_query = "UPDATE menu SET 
                                      menu_name='" . mysqli_real_escape_string($conn, $event['event_name']) . "', 
                                      parent_id=$parent_sql, 
                                      menu_order=$order_sql 
                                      WHERE menu_link='$menu_link'";
                    } else {
                        // Insert new menu
                        $menu_query = "INSERT INTO menu (menu_name, parent_id, menu_type, menu_link, menu_order) 
                                       VALUES ('" . mysqli_real_escape_string($conn, $event['event_name']) . "', $parent_sql, 'internal-link', '$menu_link', $order_sql)";
                    }
                    mysqli_query($conn, $menu_query);
                } else {
                    // Delete menu if status is not Publish
                    mysqli_query($conn, "DELETE FROM menu WHERE menu_link = '$menu_link'");
                }
            }
            
            header("Location: inputpastconference.php?msg=updated");
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
                    <h1 class="mt-4">Past Conferences</h1>
                    
                    <?php if (isset($_GET['msg'])): ?>
                        <div class="alert alert-<?= $_GET['msg'] == 'success' ? 'success' : ($_GET['msg'] == 'updated' ? 'info' : ($_GET['msg'] == 'deleted' ? 'success' : 'warning')) ?> alert-dismissible fade show" role="alert">
                            <?php 
                            if ($_GET['msg'] == 'success') echo '<i class="bi bi-check-circle"></i> Past conference added successfully!';
                            elseif ($_GET['msg'] == 'updated') echo '<i class="bi bi-check-circle"></i> Past conference updated successfully!';
                            elseif ($_GET['msg'] == 'deleted') echo '<i class="bi bi-check-circle"></i> Past conference deleted successfully!';
                            elseif ($_GET['msg'] == 'duplicate') echo '<i class="bi bi-exclamation-triangle"></i> This event is already in past conferences!';
                            elseif ($_GET['msg'] == 'notfound') echo '<i class="bi bi-exclamation-triangle"></i> Past conference not found!';
                            elseif ($_GET['msg'] == 'invalid') echo '<i class="bi bi-exclamation-triangle"></i> Invalid parameters!';
                            elseif ($_GET['msg'] == 'error') echo '<i class="bi bi-x-circle"></i> An error occurred. Please try again!';
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#addPastConfModal">
                                <i class="bi bi-plus-lg"></i> Add Past Conference
                            </button>

                            <div class="table-responsive">
                                <table id="pastConfTable" class="table table-sm table-bordered table-hover align-middle" style="font-size: 0.9rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="30">No</th>
                                            <th width="160">Poster</th>
                                            <th width="200">Event Name</th>
                                            <th width="60">Year</th>
                                            <th width="180">Date</th>
                                            <th width="100">Menu Order</th>
                                            <th width="100">Status</th>
                                            <th width="150">Link</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $query = mysqli_query($conn, "
                                            SELECT pc.*, e.event_name, e.event_year, e.poster, e.start_date, e.end_date
                                            FROM past_conferences pc
                                            JOIN events e ON pc.event_id = e.event_id
                                            ORDER BY pc.past_conf_id DESC
                                        ");
                                        while ($row = mysqli_fetch_assoc($query)) {
                                            $startDate = date("M j, Y", strtotime($row['start_date'])); 
                                            $endDate   = date("M j, Y", strtotime($row['end_date']));
                                            $event_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['event_name'])));
                                            $event_link = "event/" . $row['event_id'] . "/" . $event_slug;
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><img src="images/events/<?= $row['poster'] ?>" width="150" class="img-thumbnail" loading="lazy"></td>
                                            <td><?= htmlspecialchars($row['event_name']) ?></td>
                                            <td><?= $row['event_year'] ?></td>
                                            <td><small>Start: <?= $startDate ?><br>End: <?= $endDate ?></small></td>
                                            <td><?= $row['menu_order'] ?? '-' ?></td>
                                            <td>
                                                <span class="<?= $row['status'] == 'Publish' ? 'text-success' : 'text-secondary' ?>">
                                                    <?= $row['status'] ?>
                                                </span>
                                            </td>
                                            <td><a href="<?= $event_link ?>" target="_blank"><?= $event_link ?></a></td>
                                            <td>
                                                <a href="#" class="text-primary me-2 btn-edit" style="text-decoration:none;"
                                                   data-bs-toggle="modal" data-bs-target="#editPastConfModal"
                                                   data-id="<?= $row['past_conf_id'] ?>"
                                                   data-eventid="<?= $row['event_id'] ?>"
                                                   data-menuorder="<?= $row['menu_order'] ?>"
                                                   data-pubdate="<?= $row['publish_date'] ?>"
                                                   data-status="<?= $row['status'] ?>">
                                                   Edit
                                                </a>

                                                <a href="hapuspastconference.php?id=<?= $row['past_conf_id'] ?>&event_id=<?= $row['event_id'] ?>" 
                                                   class="text-primary" style="text-decoration:none;"
                                                   onclick="return confirm('Are you sure you want to delete this past conference?\n\nEvent: <?= htmlspecialchars($row['event_name']) ?>\nYear: <?= $row['event_year'] ?>\n\nThis will also remove the menu entry.')">Delete</a>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addPastConfModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Past Conference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Event*</label>
                            <select name="event_id" class="form-select" required>
                                <option value="">-- Select Event --</option>
                                <?php
                                // Ambil events yang belum ada di past_conferences
                                $events_query = mysqli_query($conn, "
                                    SELECT e.event_id, e.event_name, e.event_year 
                                    FROM events e
                                    WHERE e.event_id NOT IN (SELECT event_id FROM past_conferences)
                                    ORDER BY e.event_year DESC, e.event_name
                                ");
                                while ($event = mysqli_fetch_assoc($events_query)) {
                                    echo "<option value='{$event['event_id']}'>{$event['event_name']} ({$event['event_year']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Menu Order</label>
                            <input type="number" name="menu_order" class="form-control" min="0" placeholder="Leave blank for default">
                            <small class="text-muted">Order in Past Conferences submenu</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Publish Date*</label>
                            <input type="date" name="publish_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status*</label>
                            <select name="status" class="form-select">
                                <option value="Draft">Draft</option>
                                <option value="Publish" selected>Publish</option>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editPastConfModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Past Conference</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="past_conf_id" id="edit-id">
                        <input type="hidden" name="event_id" id="edit-event-id">
                        
                        <div class="mb-3">
                            <label class="form-label">Event (Cannot be changed)</label>
                            <input type="text" id="edit-event-name" class="form-control" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Menu Order</label>
                            <input type="number" name="menu_order" id="edit-menuorder" class="form-control" min="0" placeholder="Leave blank for default">
                            <small class="text-muted">Order in Past Conferences submenu</small>
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
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (document.getElementById("pastConfTable")) {
                new simpleDatatables.DataTable("#pastConfTable", { perPage: 10 });
            }

            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('edit-id').value = this.getAttribute('data-id');
                    document.getElementById('edit-event-id').value = this.getAttribute('data-eventid');
                    document.getElementById('edit-menuorder').value = this.getAttribute('data-menuorder');
                    document.getElementById('edit-pubdate').value = this.getAttribute('data-pubdate');
                    document.getElementById('edit-status').value = this.getAttribute('data-status');
                    
                    // Get event name for display
                    const eventId = this.getAttribute('data-eventid');
                    const row = this.closest('tr');
                    const eventName = row.querySelector('td:nth-child(3)').textContent;
                    document.getElementById('edit-event-name').value = eventName;
                });
            });
        });
    </script>
</body>
</html>