<!DOCTYPE html>
<html>

<?php include "bagiankode/head.php"; ?>
    <body class="sb-nav-fixed">
        <?php
            include "bagiankode/menunav.php";
        ?>
        <div id="layoutSidenav">
            
        <?php
            include "bagiankode/menu.php";
        ?>

        <div id="layoutSidenav_content" style="background-color: #EFF6FF;">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Speaker</h1>
                        <div style="text-align: right;">
                            <a href="dashboard.php" style="font-weight: bold; color: silver; text-decoration: none;"> DashBoard</a>
                            <a style="font-weight: bold; color: silver;"> > Speaker</a>
                        </div>
                        <br>
                        <div style="background-color: white; padding: 20px; display: flex; justify-content: space-between; align-items: center;">
                            <a href="input_speaker.php" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Speaker
                            </a>
                            <form method="GET">
                                <div style="display: flex; gap: 10px;">
                                    <input type="text" name="keyword" placeholder="Keyword" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; width: 300px;">
                                    <button type="submit" class="btn btn-success">Search</button>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">

            <table class="table table-hover align-middle">
                <thead>
                    <tr style="background-color: white;">
                        <th>No</th>
                        <th>Photo</th>
                        <th>Speaker Name</th>
                        <th>Information</th>
                        <th>Event</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody style="border-top: 0;">
                    <?php
                    include "includes/config.php";

                    if (isset($_GET['keyword'])) {
                        $keyword = $_GET['keyword'];
                        $query = "SELECT photo,speaker_name, information, event FROM speaker WHERE speaker_name LIKE '%$keyword%' OR information LIKE '%$keyword%'";
                    } else {
                        $query = "SELECT photo, speaker_name, information, event FROM speaker";
                    }

                    $tampil = mysqli_query($conn, $query); 
                    $total_data = mysqli_num_rows($tampil);
                    $no = 1;

                    if ($total_data > 0) {
                        while ($data = mysqli_fetch_array($tampil)) {
                    ?>
                        <tr style="border-bottom: 1px solid #f4f4f4; background-color: white;">
                            <td class="py-3" style="font-weight: 600;"><?php echo $no++; ?></td>
                            
                            <td class="py-3">
                                <?php if($data['photo'] != ""): ?>
                                    <img src="gambar/<?php echo $data['photo']; ?>" width="40" height="40" style="object-fit:cover; border-radius: 4px;">
                                <?php else: ?>
                                    <span class="text-muted small">No Img</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $data['speaker_name']; ?> </td>
                            <td><?php echo $data['information']; ?></td>
                            <td><?php echo $data['event']; ?></td>
                            
                            
                            <td class="py-3">
                                <a href="edit_speaker.php?speaker_name=<?php echo urlencode($data['speaker_name']); ?>" 
                                   style="color: #3c8dbc; text-decoration: none; font-weight: 500; margin-right: 15px; font-size: 14px;">
                                   Edit
                                </a>
                                <a href="hapus_speaker.php?hapus_speaker=<?php echo urlencode($data['speaker_name']); ?>" 
                                   onclick="return confirm('Yakin hapus?');"
                                   style="color: #3c8dbc; text-decoration: none; font-weight: 500; font-size: 14px;">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr style="background-color: #f9f9f9;">
                            <td colspan="6" class="text-center py-4 text-muted">
                                No data available in table
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div style="font-size: 14px; color: #333; font-weight: 500;">
                <?php 
                if($total_data > 0){
                    echo "Showing 1 to $total_data of $total_data entries";
                } else {
                    echo "Showing 0 to 0 of 0 entries";
                }
                ?>
            </div>

            <div>
                <ul class="pagination mb-0">
                    <li class="page-item active">
                        <a class="page-link" href="#" style="background-color: #337ab7; border-color: #337ab7;">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" style="color: #337ab7;">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#" style="color: #337ab7; border-radius: 0 4px 4px 0;">Next</a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>




