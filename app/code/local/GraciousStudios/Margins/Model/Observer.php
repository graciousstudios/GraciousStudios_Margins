<?php

class GraciousStudios_Margins_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function addCostToQuote(Varien_Event_Observer $observer)    {
//        Mage::log(str_repeat('-', 20), null, 'gracious.log');
//        Mage::log(__METHOD__, null, 'gracious.log');
//        Mage::log('$observer = ' . get_class($observer), null, 'gracious.log');
        /* @var $_quoteItem Mage_Sales_Model_Quote_Item */
        $_quoteItem = $observer->getQuoteItem();
        if(!empty($_quoteItem)) {
//            Mage::log('$_quoteItem = ' . $_quoteItem->getId(), null, 'gracious.log');
            $_product = $_quoteItem->getProduct();
            if(!empty($_product))   {
//                Mage::log('$_product = ' . $_product->getId(), null, 'gracious.log');
                $_cost = $_product->getCost();
                if(!empty($_cost))  {
//                    Mage::log('$_cost = ' . $_cost, null, 'gracious.log');
                    $_quoteItem->setCost($_cost);
                }else{

//                    Mage::log('Cost not found, checking for parent product', null, 'gracious.log');
                    // Check if it has a parent and grab cost there
                    if ($_product->getTypeId() == "simple") {
                        $parentIds = Mage::getModel('catalog/product_type_grouped')
                            ->getParentIdsByChild($_product->getId())
                        ;
                        if (!$parentIds) {
                            $parentIds = Mage::getModel('catalog/product_type_configurable')
                                ->getParentIdsByChild($_product->getId())
                            ;
                        }
                        if (isset($parentIds[0])) {
//                            Mage::log('Found parent id, grabbing cost = ' . $parentIds[0], null, 'gracious.log');
                            $parent = Mage::getModel('catalog/product')->load($parentIds[0]);
                            $_cost = $parent->getCost();
//                            Mage::log('$_cost = ' . $_cost, null, 'gracious.log');
                            $_quoteItem->setCost($_cost);
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function addMarginsToOrder(Varien_Event_Observer $observer) {

        Mage::log(str_repeat('-', 20), null, 'gracious.log');
        Mage::log(__METHOD__, null, 'gracious.log');
//        Mage::log('$observer = ' . get_class($observer), null, 'gracious.log');

        /* @var $_helper GraciousStudios_Margins_Helper_Data */
        $_helper = Mage::helper('margins');

        /* @var $_order Mage_Sales_Model_Order */
        $_order = $observer->getEvent()->getOrder();

        Mage::log('$_order = ' . $_order->getId(), null, 'gracious.log');

        $_items = $_order->getAllVisibleItems();
//        Mage::log('$_items = ' . get_class($_items), null, 'gracious.log');
        /* @var $_item Mage_Sales_Model_Order_Item */
        foreach($_items as $_item)  {
            Mage::log(str_repeat('-', 20), null, 'gracious.log');
            Mage::log('class = ' . get_class($_item), null, 'gracious.log');
            Mage::log('product_id = ' . $_item->getProductId(), null, 'gracious.log');
            Mage::log('$_item = ' . $_item->getName(), null, 'gracious.log');
            /* @var $_items Mage_Sales_Model_Order_Item */
//            Mage::log('$_item = ' . get_class($_item), null, 'gracious.log');
            Mage::log('qty_ordered = ' . $_item->getQtyOrdered(), null, 'gracious.log');
//            Mage::log('price = ' . $_item->getPrice(), null, 'gracious.log');
//            Mage::log('row_total = ' . $_item->getRowTotal(), null, 'gracious.log');
//            Mage::log('price_incl_tax = ' . $_item->getPriceInclTax(), null, 'gracious.log');

            // Grab the variables we need to do all the calculations
            $_qty = $_item->getQtyOrdered();
            $_cost = $_item->getCost();

            if(!empty($_cost))  {
                $_priceExclTax = $_item->getPrice();
                $_priceInclTax = $_item->getPriceInclTax();
                $_rowTotalExclTax = $_item->getRowTotal();
                $_rowTotalInclTax = $_item->getRowTotalInclTax();

                if(empty($_rowTotalInclTax))    {
                    Mage::log('EMPTY $_rowTotalInclTax', null, 'gracious.log');
                    if($_parent = $_item->getParentItem())  {
                        $_rowTotalExclTax = $_parent->getRowTotal();
                        $_rowTotalInclTax = $_parent->getRowTotalInclTax();
                    }
                }


                Mage::log('$_rowTotalExclTax = ' . $_rowTotalExclTax, null, 'gracious.log');
                Mage::log('$_rowTotalInclTax = ' . $_rowTotalInclTax, null, 'gracious.log');

                // Calculate everything
                $_marginInclTax = $_helper->calculateMargin($_qty, $_rowTotalInclTax, $_cost);
                $_marginExclTax = $_helper->calculateMargin($_qty, $_rowTotalExclTax, $_cost);
                $_marginPercentageInclTax = $_helper->calculatePercentage($_qty, $_rowTotalInclTax, $_cost);
                $_marginPercentageExclTax = $_helper->calculatePercentage($_qty, $_rowTotalExclTax, $_cost);

                // Set calculated variables on order item
                $_item->setMarginInclTax($_marginInclTax);
                $_item->setMarginExclTax($_marginExclTax);
                $_item->setMarginPercentageInclTax($_marginPercentageInclTax);
                $_item->setMarginPercentageExclTax($_marginPercentageExclTax);
            }
        }
        return $this;
    }
}
