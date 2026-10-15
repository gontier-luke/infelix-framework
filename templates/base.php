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
    <header class="closed">
        <?php 
        require 'header.php';
        ?>
    </header>
    <main>
        <?php  eval('?>' . $content); ?>
    </main>
    <?php if($nofooter !== true){ ?>
        <footer class="footer container-fluid">
            <?php
            require 'footer.php';
            ?>
        </footer>
    <?php } ?>
</body>
</html>