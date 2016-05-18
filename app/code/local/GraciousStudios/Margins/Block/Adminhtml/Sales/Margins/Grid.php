<?php

class GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('margins_grid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $_collection = Mage::getModel('margins/margins')->getCollection();
        $this->setCollection($_collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        // Load the core helper for translations
        $helper = Mage::helper('margins');
        // Set currency to be used in price columns
        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        // Start setting all the columns
        $this->addColumn('entity_id', [
            'header' => $helper->__('Product ID'),
            'index'  => 'entity_id',
            'width'  => '50px',
        ]);
        $this->addColumn('name', [
            'header' => $helper->__('Product Name'),
            'index'  => 'name',
            'width'  => '200px',
        ]);
        $this->addColumn('purchase_price', [
            'header'        => $helper->__('Purchase Price'),
            'index'         => 'purchase_price',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
        ]);
        $this->addColumn('avg_sale_price', [
            'header'         => $helper->__('Average Sale Price'),
            'index'          => 'avg_sale_price',
            'type'           => 'currency',
            'currency_code'  => $currency,
            'width'          => '50px',
        ]);
        $this->addColumn('tax_amount', [
            'header'        => $helper->__('Tax Amount'),
            'index'         => 'tax_amount',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
        ]);
        $this->addColumn('total_qty', [
            'header' => $helper->__('Total Sold'),
            'index'  => 'total_qty',
            'width'  => '50px',
            'type'     => 'number',
        ]);
        $this->addColumn('revenue_excl_tax', [
            'header'        => $helper->__('Revenue Excl Tax'),
            'index'         => 'revenue_excl_tax',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
        ]);
        $this->addColumn('revenue_incl_tax', [
            'header'        => $helper->__('Revenue Incl Tax'),
            'index'         => 'revenue_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
        ]);
        $this->addColumn('percentage_excl_tax', [
            'header'   => $helper->__('Margin Excl Tax') . ' %',
            'index'    => 'percentage_excl_tax',
            'renderer' => 'margins/adminhtml_sales_margins_renderer_percentage',
            'width'    => '50px',
            'type'     => 'number',
        ]);
        $this->addColumn('percentage_incl_tax', [
            'header'   => $helper->__('Margin Incl Tax') . ' %',
            'index'    => 'percentage_incl_tax',
            'renderer' => 'margins/adminhtml_sales_margins_renderer_percentage',
            'width'    => '50px',
            'type'     => 'number',

        ]);
        $this->addColumn('margin_incl_tax', [
            'header'        => $helper->__('Margin Incl Tax') . ' EUR',
            'index'         => 'margin_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',

        ]);
        $this->addColumn('margin_excl_tax', [
            'header'        => $helper->__('Margin Excl Tax') . ' ' . $currency,
            'index'         => 'margin_excl_tax',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
        ]);
        return parent::_prepareColumns();
    }

    // Used for AJAX loading
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    protected function _sortOrderCallback($collection, $column)
    {
        $collection->getSelect()
                   ->order($column->getIndex() . ' ' . strtoupper($column->getDir()))
        ;
    }

}