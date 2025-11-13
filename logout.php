<?php
session_start();
session_unset();
session_destroy();

// Redirect sa tamang PHP file (hindi HTML)
header("Location: loginAs.php");
exit();
?>
