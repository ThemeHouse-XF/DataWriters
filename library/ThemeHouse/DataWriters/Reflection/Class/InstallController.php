<?php

class ThemeHouse_DataWriters_Reflection_Class_InstallController extends ThemeHouse_Reflection_Class
{

    public function addGetTablesMethod($table, $name, $dataType = 'int', $default = '', $autoIncrement = false, $primaryKey = false)
    {
        $newRow = ThemeHouse_DataWriters_Reflection_Method_InstallController_GetTables::buildColumnNewRow($name,
            $dataType, $default, $autoIncrement, $primaryKey);
        
        $body = "return array(\n\t'$table' => array(\n$newRow\n\t)\n);";
        
        $this->addMethod('_getTables', $body);
    }
}