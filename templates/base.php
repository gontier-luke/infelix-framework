<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <?php foreach ($stylesheets as $stylesheet){ ?>
        <link rel="stylesheet" type="text/css" href="<?= $stylesheet ?>">
    <?php } ?>
</head>
<body>
    <header>
        <?php require 'header.php'; ?>
    </header>
    
    <main>
        <?php echo $content; ?>
    </main>
    
    <footer>
        <?php require 'footer.php'; ?>
    </footer>
</body>
</html>