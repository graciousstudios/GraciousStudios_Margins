<?php


class GraciousStudios_Margins_Block_Adminhtml_Sales_Margins extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_blockGroup = 'margins';
        $this->_controller = 'adminhtml_sales_margins';
        $this->_headerText = Mage::helper('core')->__('Margins');

        parent::__construct();
        $this->_removeButton('add');
    }
}