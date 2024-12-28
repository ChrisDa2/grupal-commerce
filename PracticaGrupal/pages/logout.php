<?php

unset($_SESSION['user_id']);
unset($_SESSION['email']);
session_destroy();
header("Location: index.php");

?>
