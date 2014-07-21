<?php

interface WidgetsInterface
{

    /**
     * Registra o widget no controller
     * @param Controller $scope
     * @param array $options
     */
    public function registerWidget($scope, $options = null);

    /**
     * Gera o template para insejar na view
     */
    public function getWidget();
    
    /**
      * Registra metadado do widget.
      * 
      * @param type $ketaKey
      * @param type $metaType
      * @param type $defaultValue
      */
     public function registerMeta($metaKey = '', $metaType = '', $defaultValue = '');
}