<div class="container mt-5">
    <div class="row justify-center">
        <div class="w-12/12 p-4 rounded-lg shadow-xl/20 <?= $formBgColor ?? '' ?> ">
            <div class="mb-4">
                <h1 class="text-3xl font-bold mb-0">Liste des utilisateurs administrateurs</h1>
                <div class="text-base font-bold w-3/12"><?= $createAdminUserButton ?></div>
            </div>
            <table class="min-w-full w-12/12 bg-gray-800">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-700 text-left">Identifiant</th>
                        <th class="py-2 px-4 border-b border-gray-700 text-left">Adresse E-mail</th>
                        <th class="py-2 px-4 border-b border-gray-700 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($adminUsers as $key => $adminUser): ?>
                        <tr style=" <?= $key % 2 === 0 ? 'background-color: #293343;' : '' ?>">
                            <td class="py-2 px-4 border-b border-gray-700"><?= htmlspecialchars($adminUser['username']) ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?= htmlspecialchars($adminUser['email']) ?></td>
                            <td class="py-2 px-4 border-b border-gray-700 flex justify-center gap-2 items-center">
                                <?php foreach ($adminUser['actions'] as $action): ?>
                                    <?= $action ?>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>