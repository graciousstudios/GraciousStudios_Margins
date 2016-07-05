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
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        /* @var $_collection Mage_Sales_Model_Resource_Order_Item_Collection */
        $_collection = Mage::getModel('sales/order_item')
            ->getCollection()
            ->addFieldToFilter('cost', ['notnull' => true])
        ;
        $_collection->getSelect()
            ->columns('SUM(qty_ordered) AS qty')
            ->columns('AVG(margin_incl_tax) as avg_margin_incl_tax')
            ->columns('AVG(margin_excl_tax) as avg_margin_excl_tax')
            ->columns('AVG(margin_percentage_incl_tax) as avg_margin_percentage_incl_tax')
            ->columns('AVG(margin_percentage_excl_tax) as avg_margin_percentage_excl_tax')
            ->columns('AVG(cost) as avg_cost')
            ->group('product_id')
        ;

        Mage::log('sql = ' . $_collection->getSelect()->__toString(), null, 'gracious.log');

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
        $this->addColumn('product_id', [
            'header' => $helper->__('Product ID'),
            'index'  => 'product_id',
            'width'  => '50px',
        ]);
        
        $this->addColumn('name', [
            'header' => $helper->__('Product Name'),
            'index'  => 'name',
            'width'  => '200px',
        ]);

        $this->addColumn('avg_cost', [
            'header'        => $helper->__('Average Purchase Price'),
            'index'         => 'avg_cost',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
            'filter'        => false,
        ]);

        $this->addColumn('avg_margin_incl_tax', [
            'header'        => $helper->__('Average Margin Incl Tax'),
            'index'         => 'avg_margin_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
            'filter'        => false,
        ]);
        $this->addColumn('avg_margin_excl_tax', [
            'header'        => $helper->__('Average Margin Excl Tax'),
            'index'         => 'avg_margin_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
            'filter'        => false,
        ]);

        $this->addColumn('avg_margin_percentage_incl_tax', [
            'header'        => $helper->__('Average Margin Percentage Incl Tax'),
            'index'         => 'avg_margin_percentage_incl_tax',
            'type'          => 'number',
            'width'         => '50px',
            'filter'        => false,
        ]);

        $this->addColumn('avg_margin_percentage_excl_tax', [
            'header'        => $helper->__('Average Margin Percentage Excl Tax'),
            'index'         => 'avg_margin_percentage_excl_tax',
            'type'          => 'number',
            'width'         => '50px',
            'filter'        => false,
        ]);

        $this->addColumn('total_qty', [
            'header' => $helper->__('Total Sold'),
            'index'  => 'qty',
            'width'  => '50px',
            'type'   => 'number',
            'filter' => false,
        ]);

        $this->addColumn('action', [
            'header'    => Mage::helper('backup')->__('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'    => 'getProductId',
            'actions'   => [
                [
                    'caption' => Mage::helper('sales')->__('View'),
                    'url'     => ['base' => '*/*/view'],
                    'field'   => 'product_id',
                ],
            ],
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ]);
//        $this->addExportType('*/*/exportProductMarginsCsv', $this->__('CSV'));
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