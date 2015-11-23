<?php

class ThemeHouse_DataWriters_ControllerHelper_DataWriter extends XenForo_ControllerHelper_Abstract
{

    public function getDataTypes()
    {
        return array(
            'int' => new XenForo_Phrase('integer'),
            'uint' => new XenForo_Phrase('unsigned_integer'),
            'float' => new XenForo_Phrase('numeric'),
            'string' => new XenForo_Phrase('string')
        );
    }
}