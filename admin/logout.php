<?php
    session_start();
    session_unset();
    session_destroy();
    $url = $admin_base_url."login.php";
    header("Location: $url");
?>