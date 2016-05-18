<?php
$installer = $this;
$installer->startSetup();

//-------- margins_purchase_price
//-------- margins_revenue_excl_tax
//-------- margins_revenue_incl_tax
//-------- margins_margin_eur
//-------- margins_total_qty
//-------- margins_avg_sale_price
//-------- margins_tax_amount
//-------- margins_percentage_excl_tax
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

$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('gs_margins')};
    CREATE TABLE {$installer->getTable('gs_margins')} (
        `entity_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `product_id` INT(10) UNSIGNED NOT NULL,
        `sku` VARCHAR(255) NOT NULL,
        `name` TEXT NOT NULL,
        `total_qty` INT(11) NOT NULL DEFAULT 0,
        `purchase_price` FLOAT NOT NULL,
        `revenue_excl_tax` FLOAT NOT NULL,
        `revenue_incl_tax` FLOAT NOT NULL,
        `avg_sale_price` FLOAT NOT NULL,
        `tax_amount` FLOAT NOT NULL,
        `percentage_excl_tax` FLOAT NOT NULL,
        `percentage_incl_tax` FLOAT NOT NULL,
        `margin_incl_tax` FLOAT NOT NULL,
        `margin_excl_tax` FLOAT NOT NULL,
        `created_at` DATETIME NULL,
        `updated_at` DATETIME NULL,
        PRIMARY KEY (`entity_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ALTER TABLE {$installer->getTable('gs_margins')} ADD INDEX `gs_margins_product_id` (`product_id`);
");
$installer->setConfigData('gracefuldeals/configuration/enabled', 1);

$installer->endSetup();
			 