<div class="container mt-50">
    <div class="row justify-center">
        <div class="w-6/12 p-4 rounded-lg shadow-xl/20 mx-auto <?= $formBgColor ?? '' ?> ">
            <h2 class="mb-4 text-center">Connexion Admin</h2>
            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; 
            echo $formLogin;
            ?>
            
        </div>
    </div>
</div>