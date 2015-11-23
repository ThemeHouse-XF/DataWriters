<?php
if (false) {

    class XFCP_ThemeHouse_DataWriters_Extend_XenForo_Route_PrefixAdmin_AddOns extends XenForo_Route_PrefixAdmin_AddOns
    {
    }
}

class ThemeHouse_DataWriters_Extend_XenForo_Route_PrefixAdmin_AddOns extends XFCP_ThemeHouse_DataWriters_Extend_XenForo_Route_PrefixAdmin_AddOns
{

    /**
     * Match a specific route for an already matched prefix.
     *
     * @see XenForo_Route_Interface::match()
     */
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        $xenOptions = XenForo_Application::get('options');

        if ($xenOptions->th_models_enableAddOnChooser) {
            $action = $router->resolveActionWithStringParam($routePath, $request, 'addon_id');

            if ($request->getParam('addon_id') == 'data-writers') {
                $action = 'data-writers' . $action;
                $request->setParam('addon_id', '');
            }

            if (strlen($action) >= strlen('data-writers') && substr($action, 0, strlen('data-writers')) == 'data-writers') {
                return $router->getRouteMatch('ThemeHouse_DataWriters_ControllerAdmin_DataWriter',
                    substr($action, strlen('data-writers')), 'dataWriters');
            }
        }

        return parent::match($routePath, $request, $router);
    }
}