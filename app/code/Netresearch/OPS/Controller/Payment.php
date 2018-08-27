<?php
namespace Netresearch\OPS\Controller;

/**
 * \Netresearch\OPS\PaymentController
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
abstract class Payment extends \Netresearch\OPS\Controller\AbstractController
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $salesOrderFactory;

    /**
     * @var \Netresearch\OPS\Helper\Alias
     */
    protected $oPSAliasHelper;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteQuoteFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Session\Generic
     */
    protected $frameworkGeneric;

    /**
     * @var \Netresearch\OPS\Helper\DirectDebit
     */
    protected $oPSDirectDebitHelper;

    /**
     * @var \Netresearch\OPS\Helper\Payment\Request
     */
    protected $oPSPaymentRequestHelper;

    /**
     * @var \Netresearch\OPS\Model\Validator\Parameter\FactoryFactory
     */
    protected $oPSValidatorParameterFactoryFactory;

    /**
     * @var \Netresearch\OPS\Helper\Validation\Result
     */
    protected $oPSValidationResultHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteQuoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Session\Generic $frameworkGeneric,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory,
        \Netresearch\OPS\Helper\Order $oPSOrderHelper,
        \Netresearch\OPS\Helper\Payment $oPSPaymentHelper,
        \Netresearch\OPS\Helper\Directlink $oPSDirectlinkHelper,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Helper\Alias $oPSAliasHelper,
        \Netresearch\OPS\Helper\DirectDebit $oPSDirectDebitHelper,
        \Netresearch\OPS\Helper\Payment\Request $oPSPaymentRequestHelper,
        \Netresearch\OPS\Model\Validator\Parameter\FactoryFactory $oPSValidatorParameterFactoryFactory,
        \Netresearch\OPS\Helper\Validation\Result $oPSValidationResultHelper
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $oPSConfigFactory,
            $oPSOrderHelper,
            $oPSPaymentHelper,
            $oPSDirectlinkHelper,
            $oPSHelper
        );
        $this->customerSession = $customerSession;
        $this->salesOrderFactory = $salesOrderFactory;
        $this->quoteQuoteFactory = $quoteQuoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->frameworkGeneric = $frameworkGeneric;
        $this->pageFactory = $pageFactory;
        $this->oPSAliasHelper = $oPSAliasHelper;
        $this->oPSDirectDebitHelper = $oPSDirectDebitHelper;
        $this->oPSPaymentRequestHelper = $oPSPaymentRequestHelper;
        $this->oPSValidatorParameterFactoryFactory = $oPSValidatorParameterFactoryFactory;
        $this->oPSValidationResultHelper = $oPSValidationResultHelper;
    }

    protected function wasIframeRequest()
    {
        return $this->getConfig()->getConfigData('template', $this->_getOrder()->getStoreId())
        === \Netresearch\OPS\Model\Payment\PaymentAbstract::TEMPLATE_OPS_IFRAME;
    }

    /**
     * Generates the Javascript snippet that move the redirect to the parent frame in iframe mode.
     *
     * @param $redirect
     *
     * @return string javascript snippet
     */
    protected function generateJavaScript($redirect)
    {
        $javascript
            = "
        <script type=\"text/javascript\">
            window.top.location.href = '" . $this->_url->getUrl($redirect) . "'
        </script>";

        return $javascript;
    }

    /**
     * Redirects the customer to the given redirect path or inserts the js-snippet needed for iframe template mode into
     * the response instead
     *
     * @param $redirect
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function redirectOpsRequest($redirect)
    {
        if ($this->wasIframeRequest()) {
            return $this->getResponse()->setBody($this->generateJavaScript($redirect));
        } else {
            return $this->_redirect($redirect);
        }
    }
}
