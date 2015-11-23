<?php

class ThemeHouse_DataWriters_Reflection_Method_InstallController_GetTables extends ThemeHouse_Reflection_Method
{

    public function addColumn($table, $name, $dataType = 'int', $default = '', $autoIncrement = false, $primaryKey = false)
    {
        $newRow = self::buildColumnNewRow($name, $dataType, $default, $autoIncrement, $primaryKey);
        
        $this->addRowToArrayKeyInMethod($table, $newRow);
    }

    public static function buildColumnNewRow($name, $dataType = 'int', $default = '', $autoIncrement = false, $primaryKey = false)
    {
        if ($dataType == 'uint') {
            $dataType = 'int UNSIGNED';
        } elseif ($dataType == 'string') {
            $dataType = 'varchar(255)';
        } elseif ($dataType == 'numeric') {
            $dataType = 'float';
        }
        
        $newRow = "\t\t'$name' => '" . strtolower($dataType) . ' NOT NULL';
        
        if ($default != "") {
            $newRow .= " DEFAULT=" . addslashes($default);
        } elseif ($autoIncrement) {
            $newRow .= " AUTO_INCREMENT";
        }
        
        if ($primaryKey) {
            $newRow .= " PRIMARY KEY";
        }
        
        $newRow .= "'";
        
        return $newRow;
    }
}