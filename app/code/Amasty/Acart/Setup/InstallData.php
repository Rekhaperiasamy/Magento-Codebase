<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */

/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Amasty\Acart\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup->createMigrationSetup();
        $setup->startSetup();
        

        $templateCode = 'amasty_acart_template';


        $template = \Magento\Framework\App\ObjectManager::getInstance()
                        ->create('Magento\Email\Model\Template');

        $template->setForcedArea($templateCode);

        $template->loadDefault($templateCode);

        $template->setData('orig_template_code', $templateCode);

        $template->setData('template_variables', \Zend_Json::encode($template->getVariablesOptionArray(true)));

        $template->setData('template_code', 'Amasty: Abandoned Cart Reminder');

        $template->setTemplateType(\Magento\Email\Model\Template::TYPE_HTML);

        $template->setId(NULL);
        
        $template->save();

        $installer->doUpdateClassAliases();
        
        $setup->endSetup();
        
    }
}
