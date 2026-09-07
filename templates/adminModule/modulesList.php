<div class="flash-message-container *:fixed top-0 right-0 z-50 m-4">
    <?php if (isset($flashMessage)): ?>
        <div class="flash-message <?= $flashMessage['color'] ?>">
            <?= htmlspecialchars($flashMessage['message']) ?>
        </div>
    <?php endif; ?>
</div>

<div class="container mt-5">
    <div class="row justify-center">
        <div class="w-12/12 p-4 rounded-lg shadow-xl/20 <?= $formBgColor ?? '' ?> ">
            <div class="mb-4">
                <h1 class="text-3xl font-bold mb-0">Liste des modules</h1>
            </div>
            <table class="min-w-full w-12/12 bg-gray-800">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-700 text-left">Label</th>
                        <th class="py-2 px-4 border-b border-gray-700 text-left">Description</th>
                        <th class="py-2 px-4 border-b border-gray-700 text-left">Version</th>
                        <th class="py-2 px-4 border-b border-gray-700 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $key => $module): ?>
                        <tr style=" <?= $key % 2 === 0 ? 'background-color: #293343;' : '' ?>">
                            <td class="py-2 px-4 border-b border-gray-700"><?= htmlspecialchars($module['label']) ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?= htmlspecialchars($module['description']) ?></td>
                            <td class="py-2 px-4 border-b border-gray-700"><?= htmlspecialchars($module['version']) ?></td>
                            <td class="py-2 px-4 border-b border-gray-700 flex justify-center gap-2 items-center">
                                <?php foreach ($module['actions'] as $actionKey => $action): ?>
                                    <a href="<?= $action['url'] ?>" id="<?= $actionKey .'_' . $module['name'] ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        <?= $action['label'] ?>
                                </a>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>