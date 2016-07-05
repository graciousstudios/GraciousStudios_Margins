<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$installer->getTable('sales/order_item')} ADD INDEX `sales_order_item_cost` (`cost`);
");

$installer->endSetup();