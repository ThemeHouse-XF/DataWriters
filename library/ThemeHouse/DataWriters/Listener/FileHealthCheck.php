<?php

class ThemeHouse_DataWriters_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/DataWriters/ControllerAdmin/DataWriter.php' => 'c299989bc1b284a50946a247b1384de2',
                'library/ThemeHouse/DataWriters/ControllerHelper/DataWriter.php' => 'b8214ad0cc2d443face7372719308448',
                'library/ThemeHouse/DataWriters/Extend/XenForo/Route/PrefixAdmin/AddOns.php' => 'ca7b9ed3c93e85f6e02fb3d4a409e19b',
                'library/ThemeHouse/DataWriters/Helper/DataWriter.php' => 'e502e87961df77435c9ef1ed6f747933',
                'library/ThemeHouse/DataWriters/Install/Controller.php' => '5a08c3cc672b00902728c602555ae3af',
                'library/ThemeHouse/DataWriters/Listener/LoadClass.php' => 'd38c3430d4323bd7c05999b81686cfe0',
                'library/ThemeHouse/DataWriters/PhpFile/DataWriter.php' => '78970f6745f48fd7290ba06321b43c8e',
                'library/ThemeHouse/DataWriters/Reflection/Class/DataWriter.php' => '0b4026c684c5eb7c4bae0d809af9c8bf',
                'library/ThemeHouse/DataWriters/Reflection/Class/InstallController.php' => '732a76e6bc2c6f9f55113d2653dba0f0',
                'library/ThemeHouse/DataWriters/Reflection/Class/Model.php' => '903628db1d8a77eda936a61e553ef9fc',
                'library/ThemeHouse/DataWriters/Reflection/Method/DataWriter/GetFields.php' => '24d8d62154254a602ec7a55b906876df',
                'library/ThemeHouse/DataWriters/Reflection/Method/InstallController/GetTables.php' => '323d42d49b2a7a38ba0f04c3bbf44897',
                'library/ThemeHouse/DataWriters/Route/PrefixAdmin/DataWriters.php' => '9dabdfa7e6a0fde989abc756c16b3976',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
            ));
    }
}