<?php

class GraciousStudios_Margins_Model_Mysql4_Margins extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('margins/margins', "entity_id");
    }
}