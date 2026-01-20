<?php
/*memanggil sesi yang sedang aktif agar sistem tahu sesi mana yang akan dihapus */
session_start();

/*menghapus semua variabel data yang tersimpan di memori*/
session_unset();
/*menghancurkan ID sesi dari server*/
session_destroy();

/*mengarahkan kembali ke halaman login. */
header("Location: login.php");
exit();
?>
