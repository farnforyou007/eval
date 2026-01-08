<?php
    session_start();
    session_destroy();
    header("Location: https://www.ttmed.psu.ac.th/th/evaluate");
    exit();
?>