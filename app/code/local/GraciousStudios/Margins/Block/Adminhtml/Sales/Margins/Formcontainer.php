<?php

class GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Formcontainer extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'product_id';
        $this->_blockGroup = 'margins';
        $this->_controller = 'adminhtml_sales_margins_form';

        $this->_updateButton('save', 'label', Mage::helper('core')->__('Show Report'));
    }

    public function getHeaderText()
    {
        return Mage::helper('core')->__('Product Margins');
    }

}