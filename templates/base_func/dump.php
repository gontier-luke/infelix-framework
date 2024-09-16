<div style="border: 5px solid transparent; padding: 5px; margin: 0 auto; width: 90%; text-align: left; background-color: #040404; color: #fff; font-family: monospace;">
    <ul style="color: orange; text-decoration: none; padding-left: 5px;">
        <?php foreach($vars as $var) {?>
            <li style="list-style-type: none; padding-left: 5px">
                <?php
                switch($var['type']) {
                    case 'array':
                        echo 'Array';
                        displayArray($var['value']);
                        break;
                    case 'object':
                        echo $var['value']::class;
                        displayObject($var['value']);
                        break;
                    default:
                        echo '('. $var['type'] .') '. $var['value'];
                } 
                ?>
            </li>
        <?php }?>

</div>