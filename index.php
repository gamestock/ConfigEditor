<?php

    error_reporting(-1);
    
    if (PHP_SAPI != 'cli')
    {
        echo '<pre>';
    }

    $dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    require_once  $dir . 'ConfigEditor.php';
    
    $editor = ConfigEditor::factory('Cod4');
    
    echo "Loading...\n";
    $editor->load($dir . 'server.cfg');
    
    echo "Changing entries...\n";
    $editor->set('sv_hostname', 'test hier :D', 'number', 'seta');
    
    echo "Getting all...\n";
    $cfg = $editor->getAll();
    
    /*echo "Dumping...\n";
    var_dump($cfg);*/
    
    /*echo "Replacing all...\n";
    $editor->replace(array(
            'test',
            'test2',
            'sv_blubber'
        ), array(
            array(
                'type' => 'number',
                'set' => 'seta'
            ),
            array(
                'set' => 'sets'
            ),
            array(
                'value' => 'teeeeest',
                'type' => 'string'
            )
        )
    );*/
    
    echo "Saving...\n";
    $editor->save($dir . 'new.cfg');
    
    echo "Done! :)";
    
    
    if (PHP_SAPI != 'cli')
    {
        echo '</pre>';
    }

?>
