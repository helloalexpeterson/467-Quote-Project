<?php
    session_destroy();
    header("Location: login.php", 303);
?>