<?php

class GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Form_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        Mage::log(__METHOD__, null, 'gracious.log');
        Mage::log('params = ' . print_r($this->getRequest()->getParams(), true), null, 'gracious.log');
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $form = new Varien_Data_Form([
            'id'     => 'edit_form',
            'action' => $this->getUrl('*/*/view', ['product_id' => $this->getRequest()->getParam('product_id')]),
            'method' => 'get',
        ]);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => Mage::helper('reports')->__('Filter')]);
        $htmlIdPrefix = 'margins_view_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $fieldset->addField('period_type', 'select', [
            'name'    => 'period_type',
            'options' => [
                'day'   => Mage::helper('reports')->__('Day'),
                'month' => Mage::helper('reports')->__('Month'),
                'year'  => Mage::helper('reports')->__('Year'),
            ],
            'label'   => Mage::helper('reports')->__('Period'),
            'title'   => Mage::helper('reports')->__('Period'),
            'value'   => $this->getRequest()->getParam('period_type'),
        ]);

        $fieldset->addField('from', 'date', [
            'name'     => 'from',
            'format'   => $dateFormatIso,
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'label'    => Mage::helper('reports')->__('From'),
            'title'    => Mage::helper('reports')->__('From'),
            'value'    => $this->getRequest()->getParam('from'),
            'required' => true,
        ]);

        $fieldset->addField('to', 'date', [
            'name'     => 'to',
            'format'   => $dateFormatIso,
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'label'    => Mage::helper('reports')->__('To'),
            'title'    => Mage::helper('reports')->__('To'),
            'value'    => $this->getRequest()->getParam('to'),
            'required' => true,
        ]);
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}