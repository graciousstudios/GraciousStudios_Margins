<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$installer->getTable('sales/order_item')} ADD COLUMN `cost` FLOAT(12,4) NULL DEFAULT NULL;
    ALTER TABLE {$installer->getTable('sales/order_item')} ADD COLUMN `margin_incl_tax` FLOAT(12,4) NULL DEFAULT NULL;
    ALTER TABLE {$installer->getTable('sales/order_item')} ADD COLUMN `margin_excl_tax` FLOAT(12,4) NULL DEFAULT NULL;
    ALTER TABLE {$installer->getTable('sales/order_item')} ADD COLUMN `margin_percentage_incl_tax` FLOAT(12,4) NULL DEFAULT NULL;
    ALTER TABLE {$installer->getTable('sales/order_item')} ADD COLUMN `margin_percentage_excl_tax` FLOAT(12,4) NULL DEFAULT NULL;
");

$installer->endSetup();