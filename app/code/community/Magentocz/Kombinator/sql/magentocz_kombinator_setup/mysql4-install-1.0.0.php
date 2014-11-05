<?php
/**
 * Magento CZ Module 
 * 
 * NOTICE OF LICENSE 
 * 
 * This source file is subject to the Open Software License (OSL 3.0) 
 * that is bundled with this package in the file LICENSE.txt. 
 * It is also available through the world-wide-web at this URL: 
 * http://opensource.org/licenses/osl-3.0.php 
 * If you did of the license and are unable to 
 * obtain it through the world-wide-web, please send an email 
 * to magentocz@gmail.com so we can send you a copy immediately. 
 * 
 * @copyright Copyright (c) 2014 Magento CZ (http://www.magento.cz)
 * 
 */ 

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('magentocz_kombinator')};
CREATE TABLE {$this->getTable('magentocz_kombinator')} (
  `kombinator_id` int(11) unsigned NOT NULL auto_increment,
  `shipping_code` varchar(255) NOT NULL,
  `payment_code` varchar(255) NOT NULL,
  PRIMARY KEY (`kombinator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 
