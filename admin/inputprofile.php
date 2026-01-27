<!DOCTYPE html>
<html>
<?php
ob_start();
session_start();
if (!isset($_SESSION['useremail']))
    header("Location: login.php");

include "includes/config.php";

// --- LOGIKA SIMPAN DATA BARU (INSERT) ---
if (isset($_POST['Update'])) {
    
    // Ambil data gambar lama
    $qOld = mysqli_query($conn, "SELECT logo_web, logo_profile FROM profile ORDER BY id DESC LIMIT 1");
    $oldData = mysqli_fetch_array($qOld);
    
    // Default gambar pake yang lama
    $finalLogoWeb = $oldData['logo_web'] ?? ''; 
    $finalLogoProfile = $oldData['logo_profile'] ?? '';

    $webName = mysqli_real_escape_string($conn, $_POST['webName']);
    $webUrl = mysqli_real_escape_string($conn, $_POST['webUrl']);
    $profileName = mysqli_real_escape_string($conn, $_POST['profileName']);
    $secretariatOffice = mysqli_real_escape_string($conn, $_POST['secretariatOffice']);
    $phone1 = mysqli_real_escape_string($conn, $_POST['phone1']);
    $phone2 = mysqli_real_escape_string($conn, $_POST['phone2']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    //  LOGIC GAMBAR WEB 
    if (!empty($_FILES['logoWeb']['name'])) {
        $namaFileWeb = $_FILES['logoWeb']['name'];
        $fileTmpWeb = $_FILES['logoWeb']['tmp_name'];
        $ext = pathinfo($namaFileWeb, PATHINFO_EXTENSION);
        $newLogoWebName = "web_" . time() . "." . $ext; 
        if(move_uploaded_file($fileTmpWeb, "images/" . $newLogoWebName)){
            $finalLogoWeb = $newLogoWebName;
        }
    }

    //  LOGIC GAMBAR PROFILE 
    if (!empty($_FILES['logoProfile']['name'])) {
        $namaFileProf = $_FILES['logoProfile']['name'];
        $fileTmpProf = $_FILES['logoProfile']['tmp_name'];
        $ext = pathinfo($namaFileProf, PATHINFO_EXTENSION);
        $newLogoProfName = "prof_" . time() . "." . $ext;
        if(move_uploaded_file($fileTmpProf, "images/" . $newLogoProfName)){
            $finalLogoProfile = $newLogoProfName;
        }
    }

    // INSERT DATA BARU 
    $query = "INSERT INTO profile (web_name, web_url, logo_web, profile_name, logo_profile, secretariat_office, phone1, phone2, email) 
              VALUES ('$webName', '$webUrl', '$finalLogoWeb', '$profileName', '$finalLogoProfile', '$secretariatOffice', '$phone1', '$phone2', '$email')";
    
    if(mysqli_query($conn, $query)){
        header("Location: profile.php?success=1"); 
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// AMBIL DATA TERAKHIR (SELECT)
$queryLastData = mysqli_query($conn, "SELECT * FROM profile ORDER BY id DESC LIMIT 1");
$row = mysqli_fetch_array($queryLastData);

// Jika database kosong
if (!$row) {
    $row = [
        'web_name' => '', 'web_url' => '', 'logo_web' => '', 
        'profile_name' => '', 'logo_profile' => '', 
        'secretariat_office' => '', 'phone1' => '', 'phone2' => '', 'email' => ''
    ];
}
?>

<?php include "bagiankode/head.php"; ?>

<body class="sb-nav-fixed">
    <?php include "bagiankode/menunav.php"; ?>
    
    <div id="layoutSidenav">
        <?php include "bagiankode/menu.php"; ?>
        
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <h1>Profile</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
                    </div>

                    <?php if(isset($_GET['success'])) { ?>
                        <div class="alert alert-success mt-3">Data berhasil diperbarui!</div>
                    <?php } ?>

                    <div class="card mb-4 mt-4 shadow-sm">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                
                                <div class="row mb-3">
                                    <label for="webName" class="col-sm-3 col-form-label fw-bold">Web Name*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="webName" name="webName" value="<?php echo $row['web_name']; ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="webUrl" class="col-sm-3 col-form-label fw-bold">Web URL*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="webUrl" name="webUrl" value="<?php echo $row['web_url']; ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label fw-bold">
                                        Logo Web(PNG)* <br>
                                        <small class="text-muted">Recomend : 512x100 px</small>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="d-flex align-items-end gap-4 mb-2">
                                            
                                            <?php if(!empty($row['logo_web'])) { ?>
                                                <div>
                                                    <small class="text-muted d-block mb-1">Gambar Saat Ini:</small>
                                                    <img src="images/<?php echo $row['logo_web']; ?>" style="max-height: 50px; border: 1px solid #ddd; padding: 2px;">
                                                </div>
                                            <?php } ?>

                                            <div id="previewWebContainer" style="display: none;">
                                                <small class="text-muted d-block mb-1">Gambar Baru:</small>
                                                <img id="logoWebPreview" src="" style="max-height: 50px; border: 1px solid #ddd; padding: 2px;">
                                            </div>

                                        </div>

                                        <input type="file" class="form-control" id="logoWeb" name="logoWeb" accept=".png, .jpg, .jpeg">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="profileName" class="col-sm-3 col-form-label fw-bold">Profile Name*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="profileName" name="profileName" value="<?php echo $row['profile_name']; ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label fw-bold">
                                        Logo Profile(PNG)* <br>
                                        <small class="text-muted">Recomend : 512x100 px</small>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="d-flex align-items-end gap-4 mb-2">
                                            
                                            <?php if(!empty($row['logo_profile'])) { ?>
                                                <div>
                                                    <small class="text-muted d-block mb-1">Gambar Saat Ini:</small>
                                                    <img src="images/<?php echo $row['logo_profile']; ?>" style="max-height: 50px; border: 1px solid #ddd; padding: 2px;">
                                                </div>
                                            <?php } ?>

                                            <div id="previewProfileContainer" style="display: none;">
                                                <small class="text-muted d-block mb-1">Gambar Baru:</small>
                                                <img id="logoProfilePreview" src="" style="max-height: 50px; border: 1px solid #ddd; padding: 2px;">
                                            </div>

                                        </div>

                                        <input type="file" class="form-control" id="logoProfile" name="logoProfile" accept=".png, .jpg, .jpeg">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="summernote" class="col-sm-3 col-form-label fw-bold">Secretariat Office*</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="summernote" name="secretariatOffice" rows="6"><?php echo $row['secretariat_office']; ?></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="phone1" class="col-sm-3 col-form-label fw-bold">Phone 1*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="phone1" name="phone1" value="<?php echo $row['phone1']; ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="phone2" class="col-sm-3 col-form-label fw-bold">Phone 2*</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="phone2" name="phone2" value="<?php echo $row['phone2']; ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="email" class="col-sm-3 col-form-label fw-bold">Email*</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>">
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <a href="profile.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary" name="Update">Update</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </main>
            <?php include "bagiankode/footer.php"; ?>
        </div>
    </div>
    
    <?php include "bagiankode/jsscript.php"; ?>

    <script>
    // Preview Logo Web
    document.getElementById('logoWeb').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewImg = document.getElementById('logoWebPreview');
        const previewContainer = document.getElementById('previewWebContainer');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block'; 
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '';
            previewContainer.style.display = 'none';
        }
    });

    // Preview Logo Profile
    document.getElementById('logoProfile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewImg = document.getElementById('logoProfilePreview');
        const previewContainer = document.getElementById('previewProfileContainer');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '';
            previewContainer.style.display = 'none';
        }
    });
    </script>

</body>
</html>