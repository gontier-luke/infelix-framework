<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <link rel="icon" type="image/png" href="<?= $logo ?>">
    <?php foreach ($stylesheets as $stylesheet){ ?>
        <link rel="stylesheet" type="text/css" href="<?= $stylesheet ?>">
    <?php } ?>
</head>
<body class="<?= $bodyClass ?>">
    <header>
        <?php 
        require 'header.php';
        ?>
    </header>
    <main>
        <?php  eval('?>' . $content); ?>
    </main>
    <footer class="footer container-fluid">
        <?php
         require 'footer.php';
        ?>
    </footer>
</body>
</html>