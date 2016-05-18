<?php
class GraciousStudios_Margins_Block_Adminhtml_Sales_Margins_Renderer_ProductTax extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Returns value of the row
     *
     * @param Varien_Object $row
     * @return mixed|string
     */
    protected function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $value = round($data * 1, 2) . '%';
            return $value ? $value : '0'; // fixed for showing zero in grid
        }
        return $this->getColumn()->getDefault();
    }

    /**
     * Renders CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return parent::renderCss() . ' a-right';
    }

}