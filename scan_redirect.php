<?php
$data = $_GET['data'] ?? '';
header("Location: index.php?scan=$data");
exit;
