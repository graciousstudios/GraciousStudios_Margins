<?php

class GraciousStudios_Margins_Model_Margins extends Mage_Core_Model_Abstract
{

    protected $startDate;
    protected $endDate;

    protected $avgSalePrice = 'AVG(order_item.price)';
    protected $totalQty = 'SUM(order_item.qty_ordered)';
    protected $purchasePrice = 'SUM(order_item.qty_ordered) * `at_margins_purchase_price`.`value`';
    protected $salePriceExclTax = 'SUM(order_item.row_total)';
    protected $salePriceInclTax = 'SUM(order_item.row_total_incl_tax)';
    protected $margin_percentage_excl_tax;

    protected $attributesToSet = [
        'margins_revenue_excl_tax',
        'margins_revenue_incl_tax',
        ''
    ];

    /**
     * Generate reports and send email
     */
    public function generate()
    {
        Mage::log(__METHOD__, null, 'gracious.log');
        $startTimestamp = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
        $endTimestamp = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $this->startDate = date('Y-m-d H:i:s', $startTimestamp);
        $this->endDate = date('Y-m-d H:i:s', $endTimestamp);
        $this->margin_percentage_excl_tax = '100 - ((' . $this->purchasePrice . ' / ' . $this->salePriceExclTax . ') * 100)';
        $this->test();
        die;
    }

    protected function test()
    {
        Mage::log(__METHOD__, null, 'gracious.log');
        $_collection = Mage::getModel('catalog/product')
               ->getCollection()
               ->addAttributeToSelect(['name', 'margins_purchase_price'], 'inner')
               ->joinTable(
                   ['order_item' => 'sales/order_item'],
                   'product_id = entity_id',
                   [
                       'margins_revenue_excl_tax' => $this->salePriceExclTax,
                       'margins_revenue_incl_tax' => $this->salePriceInclTax,
                       'margins_total_qty'        => $this->totalQty,
                       'margins_avg_sale_price'   => $this->avgSalePrice,
                       'margins_tax_amount'       => 'order_item.tax_amount',
                   ]
               )
               ->groupByAttribute('entity_id', 'order_item.tax_amount')
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
        Mage::log('sql = ' . $_collection->getSelect()
                                         ->__toString(), null, 'gracious.log');
        foreach ($_collection as $_item) {

            $_product = Mage::getModel('catalog/product')->load($_item->getId());

            foreach($this->attributesToSet as $_attribute)  {
                $functionaName = $this->underscoreToCamelCase($_attribute, true);
                $setFunctionName = 'set' . $functionaName;
                $getFunctionName = 'get' . $functionaName;
                $_product->$setFunctionName($_item->$getFunctionName());
                $_product->getResource()
                         ->saveAttribute($_product, $_attribute)
                ;

                Mage::log($getFunctionName . ' = ' . $_product->$getFunctionName(), null, 'gracious.log');


            }


        }
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
