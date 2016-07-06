<?php
$installer = $this;
$installer->startSetup();

//      ALTER TABLE `sales_flat_quote_item` DROP COLUMN `cost`;
//      ALTER TABLE `sales_flat_quote_item` DROP COLUMN `revenue_excl_tax`;
//      ALTER TABLE `sales_flat_quote_item` DROP COLUMN `revenue_incl_tax`;
//      ALTER TABLE `sales_flat_quote_item` DROP COLUMN `margin_incl_tax`;
//      ALTER TABLE `sales_flat_quote_item` DROP COLUMN `margin_excl_tax`;
//      ALTER TABLE `sales_flat_order_item` DROP COLUMN `cost`;
//      ALTER TABLE `sales_flat_order_item` DROP COLUMN `revenue_excl_tax`;
//      ALTER TABLE `sales_flat_order_item` DROP COLUMN `revenue_incl_tax`;
//      ALTER TABLE `sales_flat_order_item` DROP COLUMN `margin_incl_tax`;
//      ALTER TABLE `sales_flat_order_item` DROP COLUMN `margin_excl_tax`;
//      ALTER TABLE `sales_flat_order_item` DROP COLUMN `margin_percentage_excl_tax`;
//      ALTER TABLE `sales_flat_order_item` DROP COLUMN `margin_percentage_incl_tax`;

$installer->addAttribute('catalog_product', 'cost', [
    'default'          => '',
    'frontend'         => '',
    'class'            => '',
    'type'             => 'decimal',
    'backend'          => 'catalog/product_attribute_backend_price',
    'label'            => 'Purchase Price',
    'input'            => 'price',
    'group'            => 'Prices',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => true,
    'required'         => false,
    'user_defined'     => false,
    'searchable'       => false,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false,
]);

$installer->endSetup();