<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Netstarter
 * @package     Netstarter_OrderReview
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2016 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\OrderReview\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * UpgradeData constructor.
     *
     * @param BlockFactory $modelBlockFactory
     */
    public function __construct(
        BlockFactory $modelBlockFactory
    ) {
        $this->blockFactory = $modelBlockFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $dbVersion = $context->getVersion();

        // @codingStandardsIgnoreStart
        if (version_compare($dbVersion, '1.0.1', '<')) {
            $cmsBlocks = [
                [
                    'title' => 'Feedback Email Top Text',
                    'identifier' => 'feedback_email_top_block',
                    'content' => '
                <p>
                    {{trans \'We trust you enjoyed your tea and would appreciate if you would take the time to send us
                    feedback on your overall experience. Your feedback will help us to improve your overall experience
                    with us.\'}}
                </p>
                <p>
                    {{trans \'For every approved review we will be rewarding you with 50 points, for you to use on your
                    next purchase. \'}}
                </p>',
                    'is_active' => 1,
                    'stores' => 0
                ],
                [
                    'title' => 'Feedback Email Extra Text',
                    'identifier' => 'feedback_email_block',
                    'content' => '
                <p style="margin-top: 0; margin-bottom: 40px;">&nbsp;</p>
                <p><strong>Learn how to brew the perfect cup of tea in 5 Steps:</strong></p>
                <ol>
                    <li>
                        <p lang="en-GB">
                            <span style="color: #000000;">Please use good quality spring water, this will ensure that 
                            calcium, chlorine and other elements that could negatively influence the taste of tea are 
                            removed.&nbsp; </span>
                        </p>
                    </li>
                    <li>
                        <p>Boil water to 95˚C &ndash;100˚C for black, Oolong, infusion &amp; flavoured black tea and 
                        70˚C &ndash; 80˚C for green tea, flavoured green tea and white tea.</p>
                    </li>
                    <li>
                        <p>Put 1 tea bag or a tea spoon (2.5g) of leaf tea into the water and stir</p>
                    </li>
                    <li>
                        <p lang="en-GB">
                        <span style="color: #000000;">Use approximately 200ml &ndash; 220ml water per Tea Cup</span>
                        </p>
                    </li>
                    <li>
                        <p>Brew black, infusion, Oolong &amp; flavoured black tea for 3 &ndash; 5 minutes, and brew 
                        green tea, flavoured green tea and white tea for 2 &ndash; 3 minutes.</p>
                    </li>
                </ol>
                <p><span style="color: #000000;">&nbsp;</span>
                <a href="http://dilmah.com/serve-the-perfect-cup-of-black-tea"><span lang="en-GB">Click</span></a>
                <span style="color: #000000;"><span lang="en-GB"> here to find </span></span>
                        more information on serving the perfect cup of tea.<strong> </strong></p>
                <p>Thank you,</p>
                <p>Merrill, Malik, Dilhan and the Dilmah Online Tea Team</p>',
                    'is_active' => 1,
                    'stores' => 0
                ]
            ];

            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlocks as $data) {
                $block->setData($data)->save();
            }
        }

        if (version_compare($dbVersion, '1.0.2', '<')) {
            $cmsBlocks = [
                [
                    'title' => 'Feedback Email Top Text - FR',
                    'identifier' => 'feedback_email_top_block',
                    'content' => '
                <p>
                    {{trans \'Vous avez récemment passé commande sur notre boutique en ligne et nous espérons que vous 
                    avez pris plaisir à déguster vos thés Dilmah.\'}}
                </p>
                <p>
                    {{trans \'Soucieux de répondre à vos attentes, nous vous invitons à donner votre avis afin de nous 
                    aider à améliorer votre expérience d’achat.\'}}
                </p>',
                    'is_active' => 1,
                    'stores' => 9
                ],
                [
                    'title' => 'Feedback Email Extra Text - FR',
                    'identifier' => 'feedback_email_block',
                    'content' => '
                <p style="margin-top: 0; margin-bottom: 40px;">&nbsp;</p>
                <p><strong>Nos conseils pour obtenir une infusion parfaite en 5 étapes :</strong></p>
                <ol>
                    <li>
                        <p lang="en-GB">
                            <span style="color: #000000;">
                            Utiliser une eau minérale très faiblement minéralisée 
                            (+/-25mg par litre de résidu sec), pauvre en sodium et dont le PH se situe autour de 7&nbsp; 
                            </span>
                        </p>
                    </li>
                    <li>
                        <p>Utiliser une eau minérale très faiblement minéralisée (+/-25mg par litre de résidu sec), 
                        pauvre en sodium et dont le PH se situe autour de 7</p>
                    </li>
                    <li>
                        <p>Prévoir 2g de thé (ou 1 sachet) pour 200-220ml d’eau</p>
                    </li>
                    <li>
                        <p lang="en-GB">
                        <span style="color: #000000;">Remuer pendant l’infusion</span>
                        </p>
                    </li>
                    <li>
                        <p>Laisser infuser 3 à 5 minutes pour le thé noir et 2 à 3 minutes pour le thé vert, le thé 
                        oolonget le thé blanc</p>
                    </li>
                </ol>
                <p><span style="color: #000000;">&nbsp;</span>
                <a href="http://dilmah.com/serve-the-perfect-cup-of-black-tea"><span lang="en-GB">Cliquez ici</span></a>
                <span style="color: #000000;"><span lang="en-GB"> pour en savoir plus </span></span></p>
                <p>Nous vous remercions par avance pour votre contribution,</p>
                <p>L’équipe Dilmah France</p>',
                    'is_active' => 1,
                    'stores' => 9
                ]
            ];

            /** @var \Magento\Cms\Model\Block $block */
            $block = $this->blockFactory->create();
            foreach ($cmsBlocks as $data) {
                $block->setData($data)->save();
            }
        }
        // @codingStandardsIgnoreEnd
    }
}
