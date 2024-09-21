<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <?php foreach ($stylesheets as $stylesheet){ ?>
        <link rel="stylesheet" type="text/css" href="<?= $stylesheet ?>">
    <?php } ?>
</head>
<body class="d-grid">
    <header>
        <?php require 'header.php'; ?>
    </header>
    
    <main class="container-fluid">
        <?php echo $content; ?>
    </main>
    
    <footer class="footer container-fluid">
        <?php require 'footer.php'; ?>
    </footer>
</body>
</html>