<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Yume Ramen'; ?></title>
    <link rel="icon" type="image/png" href="public/img/Yume_icon.png">
    <link rel="shortcut icon" type="image/png" href="public/img/Yume_icon.png">
    <link rel="stylesheet" href="public/css/style.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="public/css/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&family=Noto+Sans+JP:wght@700&display=swap" rel="stylesheet">
</head>
<body>
