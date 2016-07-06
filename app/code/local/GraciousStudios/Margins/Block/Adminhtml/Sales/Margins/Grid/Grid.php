<?php

class GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Grid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('margins_view_grid');
        $this->setDefaultSort('product_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
//        $this->setFilterVisibility(false);
//        $this->setPagerVisibility(false);

    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {

        Mage::log(__METHOD__, null, 'gracious.log');
        Mage::log('array = ' . print_r($this->getRequest()->getParams(), true), null, 'gracious.log');
        $productId = $this->getRequest()->getParam('product_id');

        $from = $this->getLocale()->date($this->getRequest()->getParam('from'), Zend_Date::DATE_SHORT, null, false)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        /* @var $toDate Zend_Date */
        $toDate = $this->getLocale()->date($this->getRequest()->getParam('to'), Zend_Date::DATE_SHORT, null, false);
        $toDate->setHour(23)->setMinute(59)->setSecond(59);
        Mage::log('$toDate = ' . get_class($toDate), null, 'gracious.log');
        $to = $toDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $period_type = $this->getRequest()->getParam('period_type');

        // Set the correct groupBy according to which period_type as chosen
        switch ($period_type)   {
            case 'day':
                $groupBy = "DATE_FORMAT(created_at, '%d-%m-%Y')";
                break;
            case 'week':
                $groupBy = "YEAR(created_at), WEEKOFYEAR(created_at)";
                break;
            case 'month':
                $groupBy = "DATE_FORMAT(created_at, '%m-%Y')";
                break;
            case 'year':
                $groupBy = "DATE_FORMAT(created_at, '%Y')";
                break;
        }

        if(!empty($productId) && !empty($from) && !empty($to) && !empty($period_type))    {
            /* @var $_collection Mage_Sales_Model_Resource_Order_Item_Collection */
            $_collection = Mage::getModel('sales/order_item')
                ->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('created_at', array(
                    'from' => $from,
                    'to'   => $to,
                    'date' => true,
                ))
                ->addFieldToFilter('cost', array(
                    'notnull' => true
                ))
            ;

            $_collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns($groupBy . ' AS date')
                ->columns('name')
                ->columns('AVG(margin_incl_tax) AS avg_margin_incl_tax')
                ->columns('AVG(margin_excl_tax) AS avg_margin_excl_tax')
                ->columns('AVG(margin_percentage_incl_tax) AS avg_margin_percentage_incl_tax')
                ->columns('AVG(margin_percentage_excl_tax) AS avg_margin_percentage_excl_tax')
                ->columns('AVG(cost) AS avg_cost')
                ->columns('SUM(qty_ordered) AS qty')
                ->group('product_id')
                ->group($groupBy)
//                ->group('name')
                ->order('created_at DESC')
            ;
            Mage::log('sql = ' . $_collection->getSelect()->__toString(), null, 'gracious.log');
            $this->setCollection($_collection);
        }


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
        $this->addColumn('date', [
            'header' => $helper->__('Date'),
            'index'  => 'date',
            'width'  => '50px',
            'filter' => false
        ]);
        $this->addColumn('name', [
            'header' => $helper->__('Product Name'),
            'index'  => 'name',
            'width'  => '200px',
            'filter' => false,
        ]);
        $this->addColumn('avg_cost', [
            'header'        => $helper->__('Avg Purchase Price'),
            'index'         => 'avg_cost',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
            'filter'        => false,
        ]);
        $this->addColumn('avg_margin_incl_tax', [
            'header'        => $helper->__('Avg Margin Incl Tax'),
            'index'         => 'avg_margin_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
            'filter'        => false,
        ]);
        $this->addColumn('avg_margin_excl_tax', [
            'header'        => $helper->__('Avg Margin Excl Tax'),
            'index'         => 'avg_margin_incl_tax',
            'type'          => 'currency',
            'currency_code' => $currency,
            'width'         => '50px',
            'filter'        => false,
        ]);

        $this->addColumn('avg_margin_percentage_incl_tax', [
            'header'        => $helper->__('Avg Margin % Incl Tax'),
            'index'         => 'avg_margin_percentage_incl_tax',
            'type'          => 'number',
            'width'         => '50px',
            'filter'        => false,
        ]);

        $this->addColumn('avg_margin_percentage_excl_tax', [
            'header'        => $helper->__('Avg Margin % Excl Tax'),
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

        $this->addExportType('*/*/exportProductMarginsCsv', $this->__('CSV'));
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

    /**
     * Retrieve locale
     *
     * @return Mage_Core_Model_Locale
     */
    protected function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = Mage::app()->getLocale();
        }
        return $this->_locale;
    }

}