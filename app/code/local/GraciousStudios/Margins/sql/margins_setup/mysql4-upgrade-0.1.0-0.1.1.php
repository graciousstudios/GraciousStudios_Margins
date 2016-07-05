<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$installer->getTable('sales/quote_item')} ADD COLUMN `cost` FLOAT(12,4) NULL DEFAULT NULL;
");

$installer->endSetup();
			 