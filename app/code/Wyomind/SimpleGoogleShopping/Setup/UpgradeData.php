<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Upgrade data for SImple Google Shopping
 */
class UpgradeData implements UpgradeDataInterface
{

    private $_feedsCollection = null;
    private $_state = null;

    public function __construct(
        \Wyomind\SimpleGoogleShopping\Model\ResourceModel\Feeds\Collection $feedsCollection,
        \Magento\Framework\App\State $state
    ) {
    
        $this->_feedsCollection = $feedsCollection;
        $this->_state = $state;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
    

        /**
         * upgrade to 11.0.0
         */
        if (version_compare($context->getVersion(), '11.0.0') < 0) {
            try {
                $this->_state->setAreaCode('admin');
            } catch (\Exception $e) {
            }
            foreach ($this->_feedsCollection as $feed) {
                $pattern = str_replace(['"{{', '}}"', "'{{", "}}'", "php="], ['{{', '}}', "{{", "}}", "output="], $feed->getSimplegoogleshoppingXmlitempattern());
                $feed->setSimplegoogleshoppingXmlitempattern($pattern);
                $feed->save();
            }
            //.categories index=
            $re = '/.categories([^|}]+)index="?\'?([0-9]+)"?\'?/';
            foreach ($this->_feedsCollection as $feed) {
                $pattern = $feed->getProductPattern();
                $pattern = preg_replace($re, '.categories${1}nth="${2}"', $pattern);
                $feed->setProductPattern($pattern);
                $feed->save();
            }
        }
        /**
         * upgrade to 11.0.2
         * $myPattern = null; becomes $this->skip = true;
         */
        if (version_compare($context->getVersion(), '11.0.1') < 0) {
            try {
                $this->_state->setAreaCode('admin');
            } catch (\Exception $e) {
            }
            foreach ($this->_feedsCollection as $feed) {
                $pattern = $feed->getSimplegoogleshoppingXmlitempattern();
                $re = '/\$myPattern\s*=\s*null;/';
                preg_match_all($re, $pattern, $matches);
                foreach ($matches[0] as $match) {
                    $pattern = str_replace($match, '$this->skip();', $pattern);
                }
                $feed->setSimplegoogleshoppingXmlitempattern($pattern);
                $feed->save();
            }
        }
    }
}
