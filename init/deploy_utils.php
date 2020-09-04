<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 10.09.2018
 * Time: 16:46
 */

function setTableData(
    $tableName,
    $criteriaForCreate,
    $data,
    $columnsToBeUpdated = [],
    $idColumnName = 'id'
)
{
    $module = DB::fetchOneBy($tableName, $criteriaForCreate);

    if (FALSE === $module) {
        $id = DB::insertAndReturnId($tableName, $data);
    } else {
        $id = $module['id'];
        if (count($columnsToBeUpdated) > 0) {
            $dataToBeUpdated = [];

            foreach ($columnsToBeUpdated as $columnName) {
                if (!array_key_exists($columnName, $data)) {
                    throw new Exception("Column name '$columnName' doesn't exist");
                }

                $dataToBeUpdated[$columnName] = $data[$columnName];
            }

            DB::update($tableName, $dataToBeUpdated, $module['id']);
        }
    }

    return $id;
}

function defineView($viewName, $viewDefinition)
{
    $sql = "
        IF OBJECT_ID('$viewName', 'V') IS NOT NULL
            DROP VIEW $viewName
    ";
    DB::exec($sql);

    $sql = "CREATE VIEW $viewName AS $viewDefinition";
    DB::exec($sql);
}

function refreshViews()
{
    $q = DB::fetchAll("select table_name from information_schema.tables WHERE table_type='VIEW'");

    foreach($q AS $qq)
    {
        DB::exec("sp_refreshview '".$qq['table_name']."'");
    }
}

function createDirIfNotExist($dirname, $permission=false)
{

    if (file_exists($dirname)) {
        return;
    }

    mkdir($dirname);


    if($permission){
        chmod($dirname,0777);
    }

}

function createFileIfNotExist($filename, $permission=false)
{
    if (file_exists($filename)) {
        return;
    }

    touch($filename);

    if($permission){
        chmod($filename,0777);
    }

}