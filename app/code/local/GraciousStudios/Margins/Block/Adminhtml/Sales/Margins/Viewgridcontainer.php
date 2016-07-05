<?php

class GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Viewgridcontainer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'margins';
        $this->_controller = 'adminhtml_sales_margins_grid';
        $this->_headerText = Mage::helper('core')->__('Product Margins');
        parent::__construct();
        $this->_removeButton('add');
//
//        $this->_removeButton('search');
//        $this->_removeButton('delete');
//        $this->_removeButton('reset');

    }
}