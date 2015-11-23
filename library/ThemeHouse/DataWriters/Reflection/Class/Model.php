<?php

class ThemeHouse_DataWriters_Reflection_Class_Model extends ThemeHouse_Reflection_Class
{

    public function addGetByIdMethod($methodName, $table, $primaryKey)
    {
        $primaryKeyVar = ThemeHouse_DataWriters_Helper_DataWriter::snakeCaseToCamelCase($primaryKey, true);
        
        $bodyArray = array(
            'return $this->_getDb()->fetchRow(',
            "\t" . '\'',
            "\t" . 'SELECT *',
            "\t" . 'FROM ' . $table,
            "\t" . 'WHERE ' . $primaryKey . ' = ?',
            '\', $' . $primaryKeyVar . ');'
        );
        
        $body = implode("\n", $bodyArray);
        
        $this->addMethod($methodName, $body, '$' . $primaryKeyVar);
    }
}