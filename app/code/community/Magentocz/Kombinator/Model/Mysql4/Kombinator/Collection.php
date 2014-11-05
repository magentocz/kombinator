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
/** 
 * 
 * @category Magentocz 
 * @package Magentocz_Kombinator
 * @author Jaromir Muller, jaromir.muller@getready.cz
 */
class Magentocz_Kombinator_Model_Mysql4_Kombinator_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magentocz_kombinator/kombinator');
    }
}