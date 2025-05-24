<?php
session_start();
date_default_timezone_set('America/Lima');
session_destroy();
header("Location: login.php");
exit;