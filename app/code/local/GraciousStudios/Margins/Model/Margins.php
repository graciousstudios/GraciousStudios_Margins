<?php

class GraciousStudios_Margins_Model_Margins extends Mage_Core_Model_Abstract
{

    protected $startDate;
    protected $endDate;

    protected $avgSalePrice = 'AVG(main_table.price)';
    protected $totalQty = 'SUM(main_table.qty_ordered)';
    protected $purchasePrice = 'SUM(main_table.qty_ordered) * `at_margins_purchase_price`.`value`';
    protected $salePriceExclTax = 'SUM(main_table.row_total)';
    protected $salePriceInclTax = 'SUM(main_table.row_total_incl_tax)';

    protected $attributesToSet = [
        'margins_revenue_excl_tax',
        'margins_revenue_incl_tax',
        '',
    ];

    protected function _construct()
    {
        $this->_init('margins/margins');
    }

    /**
     * Generate reports and send email
     */
    public function generate()
    {
        Mage::log('-----------------------------------', null, 'gracious.log');
        Mage::log(__METHOD__, null, 'gracious.log');
        $startTimestamp = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        $endTimestamp = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $this->startDate = date('Y-m-d H:i:s');
        $this->endDate = date('Y-m-d H:i:s');
        $this->refresh();
    }

    protected function refresh()
    {
        $_collection = Mage::getModel('catalog/product')
                           ->getCollection()
                           ->addAttributeToSelect(['name', 'margins_purchase_price'], 'inner')
        ;
        foreach ($_collection as $_item) {
            $_product = Mage::getModel('catalog/product')
                            ->load($_item->getId())
            ;
            $_margin = Mage::getModel('margins/margins')
                           ->load($_product->getId(), 'product_id')
            ;
            $aMargins = $this->calculateMargins($_product);
            if (!$_margin->getId()) {
                $_margin->setData($aMargins);
                $_margin->setSku($_product->getSku());
                $_margin->setName($_product->getName());
                $_margin->setPurchasePrice($_product->getMarginsPurchasePrice());
                $_margin->setCreatedAt($this->startDate);
                $_margin->setUpdatedAt($this->startDate);
            }
            else {
                $_id = $_margin->getId();
                $_margin->setData($aMargins);
                $_margin->setSku($_product->getSku());
                $_margin->setName($_product->getName());
                $_margin->setPurchasePrice($_product->getMarginsPurchasePrice());
                $_margin->setUpdatedAt($this->startDate);
                $_margin->setId($_id);
            }
            $_margin->save();
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $_product
     *
     * @return mixed
     */
    protected function calculateMargins(Mage_Catalog_Model_Product $_product)
    {
        $aReturn = [];
        $_collection = Mage::getResourceModel('sales/order_item_collection')
                           ->addAttributeToFilter('product_id', ['eq' => $_product->getId()])
        ;
        $_collection->addFieldToSelect('product_id');
        $aCustomColumns = [
            $this->salePriceExclTax . ' as revenue_excl_tax',
            $this->salePriceInclTax . ' as revenue_incl_tax',
            $this->totalQty . ' as total_qty',
            $this->avgSalePrice . ' as avg_sale_price',
            $this->salePriceInclTax . ' - (' . $this->totalQty . ' * ' . $_product->getMarginsPurchasePrice() . ') as margin_eur',
            '100 - ((' . $_product->getMarginsPurchasePrice() . ' / ' . $this->salePriceExclTax . ') * 100) as percentage_excl_tax',
            '100 - ((SUM(main_table.qty_ordered) * ' . $_product->getMarginsPurchasePrice() . ' / ' . $this->salePriceInclTax . ') * 100) as percentage_incl_tax',
            $this->salePriceInclTax . ' - ' . $_product->getMarginsPurchasePrice() . ' as margin_incl_tax',
            $this->salePriceExclTax . ' - ' . $_product->getMarginsPurchasePrice() . ' as margin_excl_tax',
        ];
        $sCustomColumns = implode(',', $aCustomColumns);
        $_collection->getSelect()->columns($sCustomColumns);
        $_collection->getSelect()->group('product_id');
        $_item = $_collection->getFirstItem();
        if($_item)  {
            $aReturn = $_item->getData();
        }
        return $aReturn;

    }

    /**
     * Convert strings with underscores into CamelCase
     *
     * @param    string $string          The string to convert
     * @param    bool   $first_char_caps camelCase or CamelCase
     *
     * @return    string    The converted string
     *
     */
    protected function underscoreToCamelCase($string, $first_char_caps = false)
    {
        if ($first_char_caps == true) {
            $string[0] = strtoupper($string[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $string);
    }

}
