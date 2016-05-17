<?php

class GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected $avgSalePrice = 'AVG(order_item.price)';
    protected $totalQty = 'ROUND(SUM(order_item.qty_ordered))';
    protected $purchasePrice = 'ROUND(SUM(order_item.qty_ordered)) * `at_margins_purchase_price`.`value`';
    protected $salePriceExclTax = 'SUM(order_item.row_total)';
    protected $salePriceInclTax = 'SUM(order_item.row_total_incl_tax)';
    protected $margin_percentage_excl_tax;

    /**
     * GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('margins_order_grid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->margin_percentage_excl_tax = '100 - ((' . $this->purchasePrice . ' / ' . $this->salePriceExclTax . ') * 100)';
    }

    protected function _setCollectionOrder($column)
    {
        Mage::log(__METHOD__, null, 'gracious.log');
        if ($column->getOrderCallback()) {
            Mage::log('Found order callback', null, 'gracious.log');
            call_user_func($column->getOrderCallback(), $this->getCollection(), $column);
            return $this;
        }
        Mage::log('No order callback', null, 'gracious.log');
        return parent::_setCollectionOrder($column);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        Mage::log(__METHOD__, null, 'gracious.log');
        // 100 - ((purchase_price / sale_price) * 100)
        // bruto marge
        // netto marge
        // product tax
        // total order_item_tax



        $_collection = Mage::getModel('catalog/product')
                           ->getCollection()
                           ->addAttributeToSelect(['name', 'margins_purchase_price'], 'inner')
                           ->joinTable(
                               ['order_item' => 'sales/order_item'],
                               'product_id = entity_id',
                               [
                                   'revenue_excl_tax' => $this->salePriceExclTax,
                                   'revenue_incl_tax' => $this->salePriceInclTax,
                                   'total_qty'        => $this->totalQty,
                                   'avg_sale_price'   => $this->avgSalePrice,
                                   'tax_amount'       => 'order_item.tax_amount',
                               ]
                           )
                           ->groupByAttribute('entity_id','order_item.tax_amount')
        ;
        $aCustomColumns = [
            $this->salePriceInclTax . ' - (' . $this->totalQty . ' * ' . $this->purchasePrice . ') as margin_eur',
            $this->margin_percentage_excl_tax . ' as margin_percentage_excl_tax',
            '100 - ((' . $this->purchasePrice . ' / ' . $this->salePriceInclTax . ') * 100) as margin_percentage_incl_tax',
            $this->salePriceInclTax . ' - ' . $this->purchasePrice . ' as margin_incl_eur',
            $this->salePriceExclTax . ' - ' . $this->purchasePrice . ' as margin_excl_eur',
        ];
        $sCustomColumns = implode(',', $aCustomColumns);
        $_collection->getSelect()
                    ->columns($sCustomColumns)
        ;
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
        $helper = Mage::helper('core');
        $currency = (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
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
            'header'                    => $helper->__('Average Sale Price'),
            'index'                     => 'avg_sale_price',
            'type'                      => 'currency',
            'currency_code'             => $currency,
            'width'                     => '50px',
            'order_callback'            => [$this, '_sortOrderCallback'],
            'filter_condition_callback' => [$this, '_filterAvgSalePriceCallback'],
//            'filter' => false,
        ]);
        $this->addColumn('tax_amount', [
            'header'                    => $helper->__('Tax Amount'),
            'index'                     => 'tax_amount',
            'type'                      => 'currency',
            'currency_code'             => $currency,
            'width'                     => '50px',
            'order_callback'            => [$this, '_sortOrderCallback'],
            'filter_condition_callback' => [$this, '_filterTaxAmountCallback'],
        ]);
        $this->addColumn('total_qty', [
            'header'                    => $helper->__('Total Sold'),
            'index'                     => 'total_qty',
            'width'                     => '50px',
            'order_callback'            => [$this, '_sortOrderCallback'],
            'filter_condition_callback' => [$this, '_filterTotalQtyCallback'],
            'filter' => false,

        ]);
        $this->addColumn('revenue_excl_tax', [
            'header'                    => $helper->__('Revenue Excl Tax'),
            'index'                     => 'revenue_excl_tax',
            'type'                      => 'currency',
            'currency_code'             => $currency,
            'width'                     => '50px',
            'order_callback'            => [$this, '_sortOrderCallback'],
            'filter_condition_callback' => [$this, '_filterRevenueExclTaxCallback'],
        ]);
        $this->addColumn('revenue_incl_tax', [
            'header'                    => $helper->__('Revenue Incl Tax'),
            'index'                     => 'revenue_incl_tax',
            'type'                      => 'currency',
            'currency_code'             => $currency,
            'width'                     => '50px',
            'order_callback'            => [$this, '_sortOrderCallback'],
            'filter_condition_callback' => [$this, '_filterRevenueInclTaxCallback'],
        ]);
        $this->addColumn('margin_percentage_excl_tax', [
            'header'                    => $helper->__('Margin Excl Tax') . ' %',
            'index'                     => 'margin_percentage_excl_tax',
            'renderer'                  => 'margins/adminhtml_sales_margins_renderer_percentage',
            'width'                     => '50px',
            'type' => 'number',
            'order_callback'            => [$this, '_sortOrderCallback'],
            'filter_condition_callback' => [$this, '_filterMarginPercentageExclTaxCallback'],
            'filter' => false,

        ]);
        $this->addColumn('margin_percentage_incl_tax', [
            'header'                    => $helper->__('Margin Incl Tax') . ' %',
            'index'                     => 'margin_percentage_incl_tax',
            'renderer'                  => 'margins/adminhtml_sales_margins_renderer_percentage',
            'width'                     => '50px',
            'order_callback'            => [$this, '_sortOrderCallback'],
//            'filter_condition_callback' => [$this, '_filterCallback'],
            'filter' => false,

        ]);
        $this->addColumn('margin_incl_eur', [
            'header'                    => $helper->__('Margin Incl Tax') . ' EUR',
            'index'                     => 'margin_incl_eur',
            'type'                      => 'currency',
            'currency_code'             => $currency,
            'width'                     => '50px',
            'order_callback'            => [$this, '_sortOrderCallback'],
//            'filter_condition_callback' => [$this, '_filterCallback'],
            'filter' => false,
        ]);
        $this->addColumn('margin_excl_eur', [
            'header'                    => $helper->__('Margin Excl Tax') . ' ' . $currency,
            'index'                     => 'margin_excl_eur',
            'type'                      => 'currency',
            'currency_code'             => $currency,
            'width'                     => '50px',
            'order_callback'            => [$this, '_sortOrderCallback'],
//            'filter_condition_callback' => [$this, '_filterCallback'],
            'filter' => false,
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

    /**
     * @param $collection
     * @param $column
     *
     * @return $this
     */
    protected function _filterMarginPercentageExclTaxCallback($collection, $column) {
        Mage::log(__METHOD__, null, 'gracious.log');
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $range = $this->generateFromToSql($value);
        $after = $this->getCollection()
             ->getSelect()
             ->having($this->margin_percentage_excl_tax . $range)
        ;
        return $this;
    }

    /**
     * @param $collection
     * @param $column
     *
     * @return $this
     */
    protected function _filterAvgSalePriceCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $range = $this->generateFromToSql($value);
        $this->getCollection()
            ->getSelect()
            ->having($this->avgSalePrice . $range)
        ;
        return $this;
    }

    /**
     * @param $collection
     * @param $column
     *
     * @return $this
     */
    protected function _filterRevenueExclTaxCallback($collection, $column)  {
        Mage::log(__METHOD__, null, 'gracious.log');
        if (!$value = $column->getFilter()->getValue()) {
            Mage::log('no value', null, 'gracious.log');
            return $this;
        }
        $range = $this->generateFromToSql($value);
        $this->getCollection()
             ->getSelect()
             ->having($this->salePriceExclTax . $range)
        ;
        return $this;
    }

    /**
     * @param $collection
     * @param $column
     *
     * @return $this
     */
    protected function _filterRevenueInclTaxCallback($collection, $column)  {
        Mage::log(__METHOD__, null, 'gracious.log');
        if (!$value = $column->getFilter()->getValue()) {
            Mage::log('no value', null, 'gracious.log');
            return $this;
        }
        $range = $this->generateFromToSql($value);
        $this->getCollection()
             ->getSelect()
             ->having($this->salePriceInclTax . $range)
        ;

//        Mage::log('sql = ' . $this->getCollection()
//                                  ->getSelect()->__toString(), null, 'gracious.log');
        return $this;
    }

    /**
     * TODO: Broken!
     * @param $collection
     * @param $column
     *
     * @return $this
     */
    protected function _filterTotalQtyCallback($collection, $column)    {
        Mage::log(__METHOD__, null, 'gracious.log');
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $range = $this->generateFromToSql($value);
        $this->getCollection()
             ->getSelect()
             ->having($this->totalQty . $range)
        ;
        return $this;
    }

    /**
     * @param $collection
     * @param $column
     *
     * @return $this
     */
    protected function _filterTaxAmountCallback($collection, $column)   {
        Mage::log(__METHOD__, null, 'gracious.log');

        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $range = $this->generateFromToSql($value);
        $this->getCollection()
             ->getSelect()
             ->where('order_item.' . $column->getIndex() . $range)
        ;
        Mage::log('sql = ' . $this->getCollection()->getSelect()->__toString(), null, 'gracious.log');
        return $this;
    }

    /**
     * @param array $value
     *
     * @return string
     */
    protected function generateFromToSql($value) {

        $sql = '';
        if(is_array($value))    {
            if(isset($value['from']) && isset($value['to']))    {
                $sql = ' BETWEEN ' . $value['from'] . ' AND ' . $value['to'];
            }elseif(isset($value['from']) && !isset($value['to']))  {
                $sql = ' >= ' . $value['from'];
            }elseif(!isset($value['from']) && isset($value['to']))  {
                $sql = ' <= ' . $value['to'];
            }
        }elseif(is_string($value))  {
            $sql = ' = "' . $value . '"';
        }

        return $sql;



    }


}