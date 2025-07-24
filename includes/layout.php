<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Absensi QR' ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }
        .sidebar {
            width: 200px;
            background: #2c3e50;
            height: 100vh;
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 30px;
        }
        .sidebar a {
            color: white;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        .content {
            flex-grow: 1;
            padding: 40px;
            background: #f4f4f4;
        }
        .box {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #2196F3;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .msg {
            padding: 15px;
            border: 1px solid #ccc;
            background: #e7ffe7;
            font-size: 18px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <?php if (isset($content)) echo $content; ?>
</div>

</body>
</html>
