<?php

class ThemeHouse_DataWriters_PhpFile_DataWriter extends ThemeHouse_PhpFile
{

    public function __construct($className, array $options)
    {
        parent::__construct($className);
        
        if (empty($options['name'])) {
            $options['name'] = substr(strrchr($className, '_'), 1);
        }
        
        $this->setExtends('XenForo_DataWriter');
        
        $this->_createFunctionGetFields($options);
        $this->_createFunctionGetExistingData($options);
        $this->_createFunctionGetUpdateCondition($options);
        $this->_createFunctionGetModel($options);
    }

    protected function _createFunctionGetFields(array $options)
    {
        $function = $this->createFunction('_getFields');
        $function->setPhpDoc(
            array(
                'Gets the fields that are defined for the table.',
                'See parent for explanation.',
                '',
                '@return array'
            ));
        
        if ($options['auto_increment']) {
            $autoIncOrRequired = '\'autoIncrement\' => true';
        } else {
            $autoIncOrRequired = '\'required\' => true';
        }
        $body = array(
            'return array(',
            "\t" . '\'' . $options['table'] . '\' => array(',
            "\t\t" . '\'' . $options['primary_key'] . '\' => array(',
            "\t\t\t" . '\'type\' => self::TYPE_' . strtoupper($options['primary_key_type']) . ', ',
            "\t\t\t" . $autoIncOrRequired,
            "\t\t" . ')',
            "\t" . ')',
            ');'
        );
        $function->setBody($body);
    }

    protected function _createFunctionGetExistingData(array $options)
    {
        $function = $this->createFunction('_getExistingData');
        $function->setPhpDoc(
            array(
                'Gets the actual existing data out of data that was passed in.',
                'See parent for explanation.',
                '',
                '@param mixed',
                '',
                '@return array|false'
            ));
        $function->setSignature(array(
            '$data'
        ));
        $name = $options['name'];
        $method = $options['method'];
        $lcFirstName = lcfirst($options['name']);
        $primary = $options['primary_key'];
        $primaryKeyVar = ThemeHouse_DataWriters_Helper_DataWriter::snakeCaseToCamelCase($primary, true);
        
        $body = array(
            'if (!$' . $primaryKeyVar . ' = $this->_getExistingPrimaryKey($data, \'' . $primary . '\')) {',
            '    return false;',
            '}',
            '',
            '$' . $lcFirstName . ' = $this->_get' . $name . 'Model()->' . $method . '($' . $primaryKeyVar . ');',
            'if (!$' . lcfirst($options['name']) . ') {',
            '    return false;',
            '}',
            '',
            'return $this->getTablesDataFromArray($' . $lcFirstName . ');'
        );
        $function->setBody($body);
    }

    protected function _createFunctionGetUpdateCondition(array $options)
    {
        $function = $this->createFunction('_getUpdateCondition');
        $function->setPhpDoc(
            array(
                'Gets SQL condition to update the existing record.',
                '',
                '@return string'
            ));
        $function->setSignature(array(
            '$tableName'
        ));
        $function->addToBody(
            'return \'' . $options['primary_key'] . ' = \' . $this->_db->quote($this->getExisting(\'' .
                 $options['primary_key'] . '\'));');
    }

    protected function _createFunctionGetModel(array $options)
    {
        $name = $options['name'];
        $model = $options['model'];
        
        $function = $this->createFunction('_get' . $name . 'Model');
        $function->setPhpDoc(array(
            '',
            '@return ' . $model
        ));
        $function->addToBody('return $this->getModelFromCache(\'' . $model . '\');');
    }
}