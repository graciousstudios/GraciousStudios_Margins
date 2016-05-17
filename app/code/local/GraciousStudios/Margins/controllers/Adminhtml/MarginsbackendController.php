<?php

class GraciousStudios_Margins_Adminhtml_MarginsbackendController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Check is allowed access to action
     * @return bool
     */
    protected function _isAllowed() {

        return true;
    }


    public function indexAction() {

        $this->_title($this->__('Sales'))->_title($this->__('Margins'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/sales');
        $this->_addContent($this->getLayout()->createBlock('margins/adminhtml_sales_margins'));
        $this->renderLayout();
    }

    public function gridAction() {

        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('margins/adminhtml_sales_margins_grid')->toHtml());
    }

}