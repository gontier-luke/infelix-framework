<?php 
foreach($menuLinks as $linkId => $linkData) :
    if($linkId === 'logout') {
        continue; // Skip the logout link, it will be displayed separately
    }
    $hasChildren = false;
    if(key_exists('children', $linkData) && !empty($linkData['children'])) $hasChildren = true;
    ?>
    <div class="admin-menu-item ">
        <div class="admin-main-link-container">
            <a href="<?php echo $linkData['url']; ?>" class="admin-menu-link w-full <?= $hasChildren ? 'has-children ' : '' ?>"><?= $linkData['label'] ?></a>
            <?php if($hasChildren): ?>
                <div class="admin-menu-deploy-arrow w-1/12">V</div>
            <?php endif; ?>
        </div>
        <?php if(isset($linkData['children']) && !empty($linkData['children'])): ?>
            <div class="admin-submenu-container">
                <?php foreach($linkData['children'] as $sublinkId => $sublinkData): ?>
                    <a href="<?php echo $sublinkData['url']; ?>" class="admin-submenu-link w-full block"><?php echo $sublinkData['label']; ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if(isset($menuLinks['logout'])): ?>
    <div class="admin-menu-item logout">
        <div class="admin-main-link-container">
            <a href="<?= $menuLinks['logout']['url'] ?>" class="admin-menu-link w-full"><?= $menuLinks['logout']['label'] ?></a>
        </div>
    </div>
<?php endif; ?>