<div style="border: 5px solid transparent; margin: 0 auto; width: 90%; text-align: left; color: #fff; font-family: monospace;">
    <ul style="background-color: #040404; color: orange; text-decoration: none;  padding: 5px; padding-left: 10px; margin:0;">
        <?php foreach($aVars as $var) {?>
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