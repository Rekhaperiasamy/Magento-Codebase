<?php

namespace Orange\Catalog\Setup;

use Magento\Cms\Model\Block;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface {

    private $blockFactory;

    public function __construct(BlockFactory $blockFactory) {
        $this->blockFactory = $blockFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

        $buyonline = [
            'title' => 'bundle virtual block',
            'identifier' => 'bundle_virtual_block',
            'stores' => [0],
            'content' => '<div class="row">
<div class="col-xs-12 black-bgd text-center margin-xs-b-s">
<div class="media">
<div class="media-left"></div>
<div class="media-body">
<h4>Promo web pour les nouveaux abonn&eacute;s Orange: -50% pendant 3 mois</h4>
</div>
</div>
</div>
</div>',
            'is_active' => true
        ];
        $this->blockFactory->create()->setData($buyonline)->save();
        $this->blockFactory->create()->setData($stepbystep)->save();
    }

}
