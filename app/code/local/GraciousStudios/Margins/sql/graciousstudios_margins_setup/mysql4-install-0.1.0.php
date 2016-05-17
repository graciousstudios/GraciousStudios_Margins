<?php
$installer = $this;
$installer->startSetup();

//margins_revenue_excl_tax
//margins_revenue_incl_tax
//margins_total_qty
//margins_avg_sale_price
//margins_tax_amount
//margins_margin_eur
//margins_percentage_excl_tax
//margins_percentage_incl_tax
//margins_incl_eur
//margins_excl_eur


$installer->addAttribute('catalog_product', 'margins_purchase_price', [
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

$installer->addAttribute('catalog_product', 'margins_revenue_excl_tax', [
    'default'          => '',
    'frontend'         => '',
    'class'            => '',
    'type'             => 'decimal',
    'label'            => 'Revenue Excl Tax',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => false,
    'required'         => false,
    'user_defined'     => false,
    'searchable'       => false,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false,
]);

$installer->addAttribute('catalog_product', 'margins_revenue_incl_tax', [
    'default'          => '',
    'frontend'         => '',
    'class'            => '',
    'type'             => 'decimal',
    'label'            => 'Revenue Incl Tax',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => false,
    'required'         => false,
    'user_defined'     => false,
    'searchable'       => false,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false,
]);

$installer->addAttribute('catalog_product', 'margin_eur', [
    'default'          => '',
    'frontend'         => '',
    'class'            => '',
    'type'             => 'decimal',
    'label'            => 'Margin EUR',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => false,
    'required'         => false,
    'user_defined'     => false,
    'searchable'       => false,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false,
]);


$installer->endSetup();
			 