<?php
session_start();
session_destroy();
header("Location: pharma.php");
exit();
?>