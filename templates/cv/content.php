<body>
<div class="orbit">
    
<?php require 'templates/header.php';?>

  <ul class="orbit-wrap">
    
    <li class="orbit-center">
      <i class="orbit-center__icon"></i>
    </li>

    <li>
      <ul class="ring-0">
        <?php require 'formations.php'; ?>
      </ul>
    </li>

    <li>
      <ul class="ring-1">
        <?php require 'professionnels.php'; ?>
      </ul>
    </li>
    <li>
      <!-- <ul class="ring-2">
        <?php require 'projets.php'; ?>
      </ul> -->
    </li>
  </ul>
</div>
<div class="statistiques" style="height: 500px;">
    <p>Java => 5%</p>
    <p>CSS => 65%</p>
    <p>PHP => 85%</p>
    <p>Algo => 85%</p>

</div>
</body>