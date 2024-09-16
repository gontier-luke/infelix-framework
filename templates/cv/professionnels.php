
<h2 class="titre-rubrique">Milieu professionel</h2>

<div class="bubble professionnel">
<?php

foreach ($professionnels as $key => $professionnel){?>
    <div id="professionnel__item_<?php echo $key;?>" class="bubble hide professionnel__item">
        <div class="image_container">
            <img class="formations__image" src="<?php echo $professionnel['image']; ?>">
        </div>
        <div class="formations__content">
            <h3 class="formations__titre"><?php echo $professionnel['nom']; ?></h3>
            <h4 class="formations__type"><?php echo $professionnel['type']; ?></h4>
            <p class="bold underline">Technologies utilisées :</p>
            <p class="formations__type"><?php echo $professionnel['techno']; ?></p>
            <?php if (count($professionnel['projets'])>0){ ?>
            <p class="displayProjets" onclick="toggleProjets(this)">Projets</p>
            <div class="projets">
                <?php foreach ($professionnel['projets'] as $projet){ ?>
                <a href="<?= $projet['url'] ?>" target="_blank"><img src="/cv/assets/img/<?= $projet['img'] ?>" alt="<?= $projet['title'] ?>"></a>
                <?php } ?>
            </div>
            <?php } ?>
            <span class="formations__niveau"><?php echo $professionnel['periode']; ?></span>
            <span class="formations__stade <?php echo ($professionnel['stade'] == 'Terminé') ?  'complete' : 'incomplete'; ?>"><?php echo $professionnel['annees']; ?></span>
            <a target="_blank" class="btn formations__url" href="<?php echo $professionnel['url']; ?>">Plus d'info ></a>
        </div>
    </div>
<?php }?>
</div>
