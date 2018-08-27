<?php
/**
 * Mode.php
 * @author  paul.siedler@netresearch.de
 * @copyright Copyright (c) 2015 Netresearch GmbH & Co. KG
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License
 */

namespace Netresearch\OPS\Block\System\Config;

class Template extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Netresearch\OPS\Model\Config
     */
    protected $oPSConfig;

    /**
     * Mode constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Netresearch\OPS\Model\Config $oPSConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->oPSConfig = $oPSConfig;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html =  parent::_getElementHtml($element);

        $paypageUrl = $this->oPSConfig->getPayPageTemplate();
        $paypageInfo = __(
            'With this setting the customer will be redirected to the Ingenico ePayments paypage ' .
            'with the look and feel of your shop. ' .
            '</br> The template used can be seen here: </br>'
        );
        $paypageInfo .= "<a href=\"" . $paypageUrl . "\">" . $paypageUrl . "</a>";

        $javascript = "<p class=\"note\"><span id=\"ops_template_comment\"></span></p>";

        // @codingStandardsIgnoreStart
        $javascript .= "
            <script type=\"text/javascript\">
                require(['jquery', 'mage/translate'], function($, t) {
                        selectElement = $('#payment_services_ops_template');

                        function updateComment(value){
                            var comment = $('#ops_template_comment'),
                                commentStr;
                            switch (value) {
                                case '" . \Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_MAGENTO_INTERNAL . "':
                                    commentStr = '" . $paypageInfo .  "';
                                    break;
                                case '" . \Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_OPS_TEMPLATE . "':
                                    commentStr = t('With this setting the customer will be redirected to the Ingenico ePayments paypage. The look and feel of that page will be defined by a dynamically loaded template file whose origin you can define below.');
                                    break;
                                case '" . \Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_OPS_IFRAME . "':
                                    commentStr = t('With this setting the customer will enter the payment details on a page in your shop that hosts the Ingenico ePayments paypage in an iFrame. You can style the paypage through the parameters below.');
                                    break;
                                case '" . \Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_OPS_REDIRECT . "':
                                    commentStr = t('With this setting the customer will get redirected to Ingenico ePayments to enter his payment details. You can style the page through the parameters below.');
                                    break;
                                }
                            comment.html(commentStr);
                        }

                        updateComment('" . $element->getValue() . "');
                        selectElement.on('change', function() {
                            updateComment(selectElement.val());
                        });
                });
            </script>";
        // @codingStandardsIgnoreEnd

        return $html.$javascript;
    }
}
