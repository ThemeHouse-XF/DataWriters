<?php

class ThemeHouse_DataWriters_Reflection_Method_DataWriter_GetFields extends ThemeHouse_Reflection_Method
{

    public function addField($table, $name, $dataType = 'int', $default = '', $autoIncrement = false)
    {
        $newRow = "\t\t'$name' => array(\n\t\t\t'type' => self::TYPE_" . strtoupper($dataType);
        
        if ($default != "") {
            $newRow .= ",\n\t\t\t'default' => $default";
        } elseif ($autoIncrement) {
            $newRow .= ",\n\t\t\t'autoIncrement' => true";
        } else {
            $newRow .= ",\n\t\t\t'required' => true";
        }
        
        $newRow .= "\n\t\t)";
        
        $this->addRowToArrayKeyInMethod($table, $newRow);
    }
}