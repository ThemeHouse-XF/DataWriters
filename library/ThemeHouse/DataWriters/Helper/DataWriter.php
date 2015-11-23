<?php

class ThemeHouse_DataWriters_Helper_DataWriter
{

    public static function getAddOnIdFromDataWriterClass($class)
    {
        preg_match('#^([A-z_]*)_DataWriter_[A-z_]*$#', $class, $matches);
        
        $class = $matches[1];
        
        preg_match('#^([A-z_]*)_Extend_[A-z_]*$#', $class, $matches);
        
        if ($matches) {
            $class = $matches[1];
        }
        
        return $class;
    }

    public static function snakeCaseToCamelCase($snakeCase, $lcFirst = false)
    {
        $snakeCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $snakeCase)));
        
        if ($lcFirst) {
            if (PHP_VERSION_ID < 50300) {
                $snakeCase = strtolower(substr($snakeCase, 0, 1)) . substr($snakeCase, 1);
            } else {
                $snakeCase = lcfirst($snakeCase);
            }
        }
        
        return $snakeCase;
    }
}