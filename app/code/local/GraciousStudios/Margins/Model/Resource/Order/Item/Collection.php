<?php

class GraciousStudios_Margins_Model_Resource_Order_Item_Collection extends Mage_Sales_Model_Resource_Order_Item_Collection  {

    public function getSelectCountSql()
    {

        Mage::log(str_repeat('-', 20), null, 'gracious.log');
        Mage::log(str_repeat('-', 20), null, 'gracious.log');
        Mage::log(str_repeat('-', 20), null, 'gracious.log');
        Mage::log(__METHOD__, null, 'gracious.log');

        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        // Count doesn't work with group by columns keep the group by
        if (count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT " . implode(", ", $group) . ")");
        }
        else {
            $countSelect->columns('COUNT(*)');
        }
        return $countSelect;
    }

}