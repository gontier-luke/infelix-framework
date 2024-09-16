<li>
    <i class="orbit-icon titre-rubrique">
        Études / Diplômes
    </i>
</li>

<?php

foreach ($formations as $key => $formation) { ?>
    <li class="hide">
        <i id="formations__item_<?php echo $key; ?>" class="bubble formations__item">
            <img class="formations__image" src="<?php echo $formation['image']; ?>">
            <div class="formations__content">
                <h3 class="formations__titre"><?php echo $formation['nom']; ?></h3>
                <h4 class="formations__type"><?php echo $formation['type']; ?></h4>
                <span class="formations__niveau"><?php echo $formation['niveau']; ?></span>
                <span class="formations__stade <?php echo ($formation['stade'] == 'Terminé') ?  'complete' : 'incomplete'; ?>"><?php echo $formation['stade']; ?></span>
                <a target="_blank" class="btn formations__url" href="<?php echo $formation['url']; ?>">Plus d'info ></a>
        </i>
    </li>
<?php } ?>
</div>