<?php

class ThemeHouse_DataWriters_ControllerAdmin_DataWriter extends ThemeHouse_Reflection_ControllerAdmin_Abstract
{

    public function actionIndex()
    {
        $class = $this->_input->filterSingle('class', XenForo_Input::STRING);
        
        if ($class) {
            return $this->responseReroute(__CLASS__, 'view');
        }
        
        $addOns = $this->_getAddOnModel()->getAllAddOns();
        
        $xenOptions = XenForo_Application::get('options');
        
        $addOnSelected = '';
        
        if ($xenOptions->th_dataWriters_enableAddOnChooser) {
            $addOnId = $this->_input->filterSingle('addon_id', XenForo_Input::STRING);
            
            if (!empty($GLOBALS['ThemeHouse_DataWriters_Route_PrefixAdmin_DataWriters']) && !$addOnId) {
                $addOnId = XenForo_Helper_Cookie::getCookie('edit_addon_id');
            }
            
            if ($addOnId && !empty($addOns[$addOnId])) {
                XenForo_Helper_Cookie::setCookie('edit_addon_id', $addOnId);
                
                $addOn = $addOns[$addOnId];
                
                $addOnSelected = $addOnId;
                
                $this->canonicalizeRequestUrl(XenForo_Link::buildAdminLink('add-ons/data-writers', $addOn));
            } else {
                $this->canonicalizeRequestUrl(XenForo_Link::buildAdminLink('add-ons/data-writers'));
                
                XenForo_Helper_Cookie::deleteCookie('edit_addon_id');
            }
        }
        
        $addOns['XenForo'] = array(
            'addon_id' => 'XenForo',
            'active' => true,
            'title' => 'XenForo'
        );
        
        $rootPath = XenForo_Autoloader::getInstance()->getRootDir();
        
        $dataWriters = array();
        $dataWriterCount = 0;
        $totalDataWriters = 0;
        
        foreach ($addOns as $addOnId => $addOn) {
            $dataWriterPath = $rootPath . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $addOnId) .
                 DIRECTORY_SEPARATOR . 'DataWriter';
            
            if (!file_exists($dataWriterPath)) {
                continue;
            }
            
            $directory = new RecursiveDirectoryIterator($dataWriterPath);
            $iterator = new RecursiveIteratorIterator($directory);
            $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach ($regex as $fileinfo) {
                $classPath = str_replace($rootPath, '', $fileinfo[0]);
                $classPath = pathinfo($classPath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR .
                     pathinfo($classPath, PATHINFO_FILENAME);
                $dirs = explode(DIRECTORY_SEPARATOR, $classPath);
                $dirs = array_filter($dirs);
                $className = implode('_', $dirs);
                if (!$xenOptions->th_dataWriters_enableAddOnChooser || !$addOnSelected ||
                     $addOnId == $addOnSelected) {
                    $dataWriters[$addOnId][$className] = array(
                        'class' => $className,
                        'filename' => pathinfo($classPath, PATHINFO_FILENAME)
                    );
                    $dataWriterCount++;
                }
                $totalDataWriters++;
            }
        }
        
        unset($addOns['XenForo']);
        
        $viewParams = array(
            'addOns' => $addOns,
            'addOnSelected' => $addOnSelected,
            
            'dataWriters' => $dataWriters,
            'dataWriterCount' => $dataWriterCount,
            'totalDataWriters' => $totalDataWriters
        );
        
        return $this->responseView('ThemeHouse_DataWriters_ViewAdmin_DataWriter_List',
            'th_datawriter_list_datawriters', $viewParams);
    }

    public function actionView()
    {
        $class = $this->_input->filterSingle('class', XenForo_Input::STRING);
        
        try {
            $dataWriter = XenForo_DataWriter::create($class);
        } catch (Exception $e) {
        }
        
        if (empty($dataWriter) || !$dataWriter instanceof XenForo_DataWriter) {
            return $this->responseNoPermission();
        }
        
        $reflectionClass = new ThemeHouse_Reflection_Class(get_class($dataWriter));
        
        $reflectionMethods = $reflectionClass->getMethods();
        
        $methods = array();
        foreach ($reflectionMethods as $reflectionMethod) {
            /* @var $reflectionMethod ReflectionMethod */
            $methodName = $reflectionMethod->getName();
            $declaringClass = $reflectionMethod->getDeclaringClass();
            $methods[$methodName]['declaringClass'] = $declaringClass->getName();
            $methods[$methodName]['isAbstract'] = $reflectionMethod->isAbstract();
            $methods[$methodName]['isConstructor'] = $reflectionMethod->isConstructor();
            $methods[$methodName]['isDeprecated'] = $reflectionMethod->isDeprecated();
            $methods[$methodName]['isDestructor'] = $reflectionMethod->isDestructor();
            $methods[$methodName]['isFinal'] = $reflectionMethod->isFinal();
            $methods[$methodName]['isInternal'] = $reflectionMethod->isInternal();
            $methods[$methodName]['isPrivate'] = $reflectionMethod->isPrivate();
            $methods[$methodName]['isProtected'] = $reflectionMethod->isProtected();
            $methods[$methodName]['isPublic'] = $reflectionMethod->isPublic();
            $methods[$methodName]['isStatic'] = $reflectionMethod->isStatic();
            $methods[$methodName]['isUserDefined'] = $reflectionMethod->isUserDefined();
        }
        
        $fields = $dataWriter->getFields();
        
        $dataWriter = array(
            'class' => $class
        );
        
        $viewParams = array(
            'dataWriter' => $dataWriter,
            
            'fields' => $fields,
            'methods' => $methods
        );
        
        return $this->responseView('ThemeHouse_DataWriters_ViewAdmin_Model_View', 'th_datawriter_view_datawriters',
            $viewParams);
    }

    public function actionAdd()
    {
        $addOnId = $this->_input->filterSingle('addon_id', XenForo_Input::STRING);
        
        if (!$addOnId) {
            $addOnModel = $this->_getAddOnModel();
            
            $viewParams = array(
                'addOnOptions' => $addOnModel->getAddOnOptionsListIfAvailable()
            );
            
            return $this->responseView('ThemeHouse_DataWriters_ViewAdmin_DataWriter_Add_ChooseAddOn',
                'th_datawriter_choose_addon_datawriters', $viewParams);
        }
        
        $model = $this->_input->filterSingle('model', XenForo_Input::STRING);
        
        if (!$model) {
            $models = array();
            
            $rootPath = XenForo_Autoloader::getInstance()->getRootDir();
            
            $modelPath = $rootPath . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $addOnId) .
                 DIRECTORY_SEPARATOR . 'Model';
            
            if (!file_exists($modelPath)) {
                return $this->responseError(new XenForo_Phrase('th_no_models_in_this_addon_datawriter'));
            }
            
            $directory = new RecursiveDirectoryIterator($modelPath);
            $iterator = new RecursiveIteratorIterator($directory);
            $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
            
            foreach ($regex as $fileinfo) {
                $classPath = str_replace($rootPath, '', $fileinfo[0]);
                $classPath = pathinfo($classPath, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR .
                     pathinfo($classPath, PATHINFO_FILENAME);
                $dirs = explode(DIRECTORY_SEPARATOR, $classPath);
                $dirs = array_filter($dirs);
                $className = implode('_', $dirs);
                $models[$className] = $className;
            }
            
            $viewParams = array(
                'models' => $models,
                
                'addOnSelected' => $addOnId
            );
            
            return $this->responseView('ThemeHouse_DataWriters_ViewAdmin_DataWriter_Add_ChooseModel',
                'th_datawriter_choose_model_datawriters', $viewParams);
        }
        
        $modelClassName = $model;
        
        if (substr($modelClassName, 0, strlen($addOnId . '_Model_')) != $addOnId . '_Model_') {
            return $this->responseNoPermission();
        }
        
        $class = $addOnId . '_DataWriter_' . substr($modelClassName, strlen($addOnId . '_Model_'));
        $name = substr(strrchr($model, '_'), 1);
        
        $model = XenForo_Model::create($model);
        
        if (!$model || !$model instanceof XenForo_Model) {
            return $this->responseNoPermission();
        }
        
        $reflectionClass = new ThemeHouse_Reflection_Class(get_class($model));
        
        $method = 'get' . $name . 'ById';
        $table = 'xf_' . strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
        $primaryKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name)) . '_id';
        $primaryKeyType = '';
        $autoIncrement = true;
        
        $primaryKeyTypes = $this->_getDataWriterHelper()->getDataTypes();
        
        if ($reflectionClass->hasMethod('get' . $name . 'ById')) {
            /* @var $reflectionMethod ThemeHouse_Reflection_Method */
            $reflectionMethod = $reflectionClass->getMethod($method, 'ThemeHouse_Reflection_Method');
            $body = $reflectionMethod->getBody();
            $pattern = '#\$this->_getDb\(\)->fetchRow\(\'\s*SELECT.*FROM\s+([a-z_]+)\s+.*WHERE\s+(?:[a-z_]+.)?([a-z_]+)\s+#Us';
            preg_match($pattern, $body, $matches);
            if ($matches) {
                $table = $matches[1];
                $primaryKey = $matches[2];
            }
            $parameters = $reflectionMethod->getParameters();
            if (!empty($parameters[0])) {
                try {
                    $primaryKeyType = $parameters[0]->getType();
                } catch (Exception $e) {
                }
            }
        }
        
        $db = XenForo_Application::getDb();
        $tables = $db->listTables();
        if (in_array($table, $tables)) {
            $columns = $db->fetchAll('DESCRIBE ' . $db->quoteIdentifier($table, true));
            foreach ($columns as $column) {
                if ($column['Key'] == 'PRI') {
                    $primaryKey = $column['Field'];
                    $autoIncrement = $column['Extra'] == 'auto_increment';
                }
            }
            $columns = $db->describeTable($table);
            if ($primaryKey && !empty($columns[$primaryKey])) {
                $primaryKeyType = $columns[$primaryKey]['DATA_TYPE'];
                if (in_array($primaryKeyType, 
                    array(
                        'char',
                        'varchar',
                        'binary',
                        'varbinary',
                        'blob',
                        'tinyblob',
                        'mediumblob',
                        'longblob',
                        'text',
                        'tinytext',
                        'mediumtext',
                        'longtext'
                    ))) {
                    $primaryKeyType = 'string';
                } elseif (in_array($primaryKeyType, 
                    array(
                        'integer',
                        'int',
                        'smallint',
                        'tinyint',
                        'mediumint',
                        'bigint'
                    ))) {
                    if ($columns[$primaryKey]['UNSIGNED']) {
                        $primaryKeyType = 'uint';
                    } else {
                        $primaryKeyType = 'int';
                    }
                } elseif (in_array($primaryKeyType, 
                    array(
                        'float',
                        'double'
                    ))) {
                    $primaryKeyType = 'float';
                }
            }
        }
        
        if (empty($primaryKeyTypes[$primaryKeyType])) {
            $primaryKeyType = 'uint';
        }
        
        if ($primaryKeyType == 'string') {
            $autoIncrement = false;
        }
        
        $viewParams = array(
            'model' => $modelClassName,
            'addOnSelected' => $addOnId,
            
            'class' => $class,
            'method' => $method,
            'table' => $table,
            'primaryKey' => $primaryKey,
            'primaryKeyType' => $primaryKeyType,
            'primaryKeyTypes' => $primaryKeyTypes,
            'autoIncrement' => $autoIncrement
        );
        
        return $this->responseView('ThemeHouse_DataWriters_ViewAdmin_DataWriter_Add',
            'th_datawriter_add_datawriters', $viewParams);
    }

    public function actionSave()
    {
        $options = $this->_input->filter(
            array(
                'model' => XenForo_Input::STRING,
                'addon_id' => XenForo_Input::STRING,
                'class' => XenForo_Input::STRING,
                'method' => XenForo_Input::STRING,
                'table' => XenForo_Input::STRING,
                'primary_key' => XenForo_Input::STRING,
                'primary_key_type' => XenForo_Input::STRING,
                'auto_increment' => XenForo_Input::BOOLEAN
            ));
        
        try {
            $model = XenForo_Model::create($options['model']);
        } catch (Exception $e) {
        }
        
        if (empty($model) || !$model instanceof XenForo_Model) {
            return $this->responseNoPermission();
        }
        
        /* @var $reflectionClass ThemeHouse_DataWriters_Reflection_Class_Model */
        $reflectionClass = new ThemeHouse_DataWriters_Reflection_Class_Model(get_class($model));
        
        if (!$reflectionClass->hasMethod($options['method'])) {
            $reflectionClass->addGetByIdMethod($options['method'], $options['table'], $options['primary_key']);
        }
        
        $phpFile = new ThemeHouse_DataWriters_PhpFile_DataWriter($options['class'], $options);
        $phpFile->export();
        
        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CREATED, 
            XenForo_Link::buildAdminLink('data-writers'));
    }

    public function actionAddField()
    {
        $class = $this->_input->filterSingle('class', XenForo_Input::STRING);
        $table = $this->_input->filterSingle('table', XenForo_Input::STRING);
        
        try {
            $dataWriter = XenForo_DataWriter::create($class);
        } catch (Exception $e) {
        }
        
        if (empty($dataWriter) || !$dataWriter instanceof XenForo_DataWriter) {
            return $this->responseNoPermission();
        }
        
        $field = array(
            'table' => $table
        );
        
        $dataWriter = array(
            'class' => $class
        );
        
        $viewParams = array(
            'dataWriter' => $dataWriter,
            'field' => $field,
            'dataTypes' => $this->_getDataWriterHelper()->getDataTypes()
        );
        
        return $this->responseView('ThemeHouse_DataWriters_ViewAdmin_DataWriter_Field_Add',
            'th_field_add_datawriters', $viewParams);
    }

    public function actionSaveField()
    {
        $input = $this->_input->filter(
            array(
                'class' => XenForo_Input::STRING,
                'table' => XenForo_Input::STRING,
                'name' => XenForo_Input::STRING,
                'data_type' => XenForo_Input::STRING,
                'default' => XenForo_Input::STRING,
                'auto_increment' => XenForo_Input::BOOLEAN
            ));
        
        $dataWriter = XenForo_DataWriter::create($input['class']);
        
        if (empty($dataWriter) || !$dataWriter instanceof XenForo_DataWriter) {
            return $this->responseNoPermission();
        }
        
        $reflectionClass = new ThemeHouse_Reflection_Class($input['class']);
        
        if ($reflectionClass->hasMethod('_getFields')) {
            /* @var $reflectionMethod ThemeHouse_DataWriters_Reflection_Method_DataWriter_GetFields */
            $reflectionMethod = $reflectionClass->getMethod('_getFields', 
                'ThemeHouse_DataWriters_Reflection_Method_DataWriter_GetFields');
            
            $reflectionMethod->addField($input['table'], $input['name'], $input['data_type'], $input['default'], 
                $input['auto_increment']);
        } else {
            // TODO add _getFields method
        }
        
        $addOnId = ThemeHouse_DataWriters_Helper_DataWriter::getAddOnIdFromDataWriterClass($input['class']);
        $installControllerClass = $addOnId . '_Install_Controller';
        
        /* @var $reflectionClass ThemeHouse_DataWriters_Reflection_Class_InstallController */
        $reflectionClass = new ThemeHouse_DataWriters_Reflection_Class_InstallController($installControllerClass);
        
        if ($reflectionClass->hasOveridden('_getTables')) {
            /* @var $reflectionMethod ThemeHouse_DataWriters_Reflection_Method_InstallController_GetTables */
            $reflectionMethod = $reflectionClass->getMethod('_getTables', 
                'ThemeHouse_DataWriters_Reflection_Method_InstallController_GetTables');
            
            $reflectionMethod->addColumn($input['table'], $input['name'], $input['data_type'], $input['default'], 
                $input['auto_increment']);
        } else {
            $reflectionClass->addGetTablesMethod($input['table'], $input['name'], $input['data_type'], 
                $input['default'], $input['auto_increment']);
        }
        
        // TODO add column to database?
        
        $dataWriter = array(
            'class' => $input['class']
        );
        
        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::RESOURCE_CREATED, 
            XenForo_Link::buildAdminLink('data-writers/edit-method', $dataWriter, 
                array(
                    'method' => '_getFields'
                )));
    }

    public function actionAddMethod()
    {
        $class = $this->_input->filterSingle('class', XenForo_Input::STRING);
        
        $dataWriter = XenForo_DataWriter::create($class);
        
        if (empty($dataWriter) || !$dataWriter instanceof XenForo_DataWriter) {
            return $this->responseNoPermission();
        }
        
        return $this->_getMethodAddResponse($dataWriter, 'data-writers', 'datawriter');
    }

    public function actionEditMethod()
    {
        $className = $this->_input->filterSingle('class', XenForo_Input::STRING);
        
        $dataWriter = XenForo_DataWriter::create($className);
        
        if (empty($dataWriter) || !$dataWriter instanceof XenForo_DataWriter) {
            return $this->responseNoPermission();
        }
        
        return $this->_getMethodEditResponse($dataWriter, 'data-writers');
    }

    public function actionDeleteMethod()
    {
        $className = $this->_input->filterSingle('class', XenForo_Input::STRING);
        
        $dataWriter = XenForo_DataWriter::create($className);
        
        if (empty($dataWriter) || !$dataWriter instanceof XenForo_DataWriter) {
            return $this->responseNoPermission();
        }
        
        return $this->_getMethodDeleteResponse($dataWriter, 'data-writers');
    }

    protected function _getAddOnIdFromClassName($className)
    {
        return ThemeHouse_DataWriters_Helper_DataWriter::getAddOnIdFromDataWriterClass($className);
    }

    /**
     * Get the data-writer helper.
     *
     * @return ThemeHouse_DataWriters_ControllerHelper_DataWriter
     */
    protected function _getDataWriterHelper()
    {
        return $this->getHelper('ThemeHouse_DataWriters_ControllerHelper_DataWriter');
    }
}