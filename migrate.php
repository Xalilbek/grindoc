<?php

require_once '../class/class.functions.php';

$user = new User();
createDirIfNotExist(MIGRATIONS_FILES_SAVE_PATH,true);
$path =  DIRNAME_INDEX . 'prodoc/migrations';

$migration_files = array_diff(scandir($path), array('.', '..'));
$migrations_from_db = DB::fetchColumnArray('SELECT * FROM migrations');
$migrations = [];

foreach ($migrations_from_db as $key=>$mig){
    $migrations[$mig] = $key;
}

foreach ($migration_files as $file){
    if (!array_key_exists($file, $migrations)){

        DB::insert('migrations',[
            'migration' => $file
        ]);
        require DIRNAME_INDEX . "prodoc/migrations/{$file}";
    }
}



