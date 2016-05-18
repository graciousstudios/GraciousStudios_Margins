<?php

class GraciousStudios_Margins_Block_Adminhtml_Marginsbackend extends Mage_Adminhtml_Block_Template
{

    public function __construct() {

        parent::__construct();
        $this->setId('margins_order_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

}