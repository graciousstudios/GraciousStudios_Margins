<?php

class GraciousStudios_Margins_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @param integer $qty
     * @param float $price
     * @param float $cost
     *
     * @return float
     */
    public static function calculateMargin($qty, $rowTotal, $cost) {
        $margin = $rowTotal - ($qty * $cost);
        return $margin;
    }

    /**
     * @param float $price
     * @param float $cost
     *
     * @return int
     */
    public static function calculatePercentage($qty, $rowTotal, $cost) {
        $percentage = 100 - (($qty * $cost) / $rowTotal * 100);
        return $percentage;
    }
}
	 