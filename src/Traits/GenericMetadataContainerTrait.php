<?php


namespace CaptainKant\Generics\Traits;


trait GenericMetadataContainerTrait
{

    private $_container_data;

    public function _getMetadataContainerData($id)
    {
        return $this->_container_data[$id] ?? null;
    }

    public function _setMetadataContainerData($id, $val)
    {
        $this->_container_data[$id] = $val;
    }

}