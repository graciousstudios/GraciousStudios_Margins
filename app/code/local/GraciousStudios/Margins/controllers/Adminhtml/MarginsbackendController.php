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

    public function refreshAction() {
        try{
            $_margin = Mage::getModel('margins/margins')->generate();
            Mage::getSingleton('core/session')->addSuccess('Margins regenerated successfully');
        }catch(Exception $e)    {
            Mage::getSingleton('core/session')->addError($e->getMessage());

        }
        $this->_redirect('*/*/index');

    }

    public function viewAction()    {

        Mage::log(__METHOD__, null, 'gracious.log');
        Mage::log('session = ' . print_r(Mage::getSingleton('adminhtml/session')->getData(), true), null, 'gracious.log');

        $this->_title($this->__('Margins'))->_title($this->__('Product'))->_title($this->__('View'));

        $this->loadLayout();
        $this->_setActiveMenu('sales/margins');

        $_productId = $this->getRequest()->getParam('product_id');
        if(!empty($_productId) && is_numeric($_productId))  {
            //GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Formcontainer
            $filterFormBlock = $this->getLayout()->createBlock('margins/adminhtml_sales_margins_formcontainer');
            $this->_addContent($filterFormBlock);
        }

        $_from = $this->getRequest()->getParam('from');
        $_to = $this->getRequest()->getParam('to');
        $_periodType = $this->getRequest()->getParam('period_type');

//        Mage::getSingleton('adminhtml/session')->setData('margins', [
//            'product_id'  => $_productId,
//            'to'          => $_to,
//            'from'        => $_from,
//            'period_type' => $_periodType,
//        ]);
//
//        Mage::log('session = ' . print_r(Mage::getSingleton('adminhtml/session')->getData(), true), null, 'gracious.log');

//        if(empty($_from) && empty($_to) && empty($_periodType)) {
//            $_from =
//        }



        if(!empty($_from) && !empty($_to) && !empty($_periodType))  {
            $gridBlock = $this->getLayout()->createBlock('margins/adminhtml_sales_margins_viewgridcontainer');
            $this->_addContent($gridBlock);
        }

        $this->renderLayout();
    }

    /**
     * Report action init operations
     *
     * @param array|Varien_Object $blocks
     * @return Mage_Adminhtml_Controller_Report_Abstract
     */
    public function _initReportAction($blocks)
    {
        if (!is_array($blocks)) {
            $blocks = [$blocks];
        }
        $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        $requestData = $this->_filterDates($requestData, ['from', 'to']);
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new Varien_Object();
        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }
        foreach ($blocks as $block) {
            if ($block) {
                $block->setPeriodType($params->getData('period_type'));
                $block->setFilterData($params);
            }
        }
        return $this;
    }

    public function exportProductMarginsCsvAction() {
        Mage::log(__METHOD__, null, 'gracious.log');
        Mage::log('params = ' . print_r($this->getRequest()->getParams(), true), null, 'gracious.log');
        $prefix = 'margins_export_';
        $date = date('YmdHis');
        $extension = '.csv';
        $filename = $prefix . $date . $extension;
        $content = $this->getLayout()->createBlock('margins/adminhtml_sales_margins_grid_grid')->getCsv();
        $this->_sendUploadResponse($filename, strip_tags($content));
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}