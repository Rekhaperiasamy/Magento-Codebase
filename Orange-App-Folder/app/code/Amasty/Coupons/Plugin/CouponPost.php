<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Coupons
 */


namespace Amasty\Coupons\Plugin;

class CouponPost
{

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Amasty\Coupons\Helper\Data
     */
    protected $amHelper;
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository Quote repository.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Escaper $escaper
     * @param \Amasty\Coupons\Helper\Data $helper
     */
    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Escaper $escaper,
        \Amasty\Coupons\Helper\Data $helper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_request = $request;
        $this->messageManager = $messageManager;
        $this->_objectManager = $objectManager;
        $this->amHelper = $helper;
        $this->escaper = $escaper;
    }
    
    public function afterExecute($subject, $back)
    {
        $messages = $this->messageManager->getMessages();
        $appliedCodes = $this->amHelper->getRealAppliedCodes();
        if (is_array($appliedCodes)) {
            foreach ($messages->getItems() as $type => $message) {
                $message->setIdentifier('amCoupons');
                $lastCode =  trim($this->_request->getParam('last_code'));
                $fullCode =  trim($this->_request->getParam('coupon_code'));
                $isRemoved =  $this->_request->getParam('remove_coupon');
                $messageText = $message->getText();
                $messageText = str_replace($fullCode, $lastCode, $messageText);
                $message->setText($messageText);
				//$this->_objectManager->get('Psr\Log\LoggerInterface')->addDebug($lastCode.','.$fullCode.',removed:'.$isRemoved);
                if (!in_array($lastCode, $appliedCodes)) {
                    $messages->deleteMessageByIdentifier('amCoupons');
                    if ($isRemoved) {
                        // $this->messageManager->addSuccessMessage(
                        //     __(
                        //         'You canceled the coupon code "%1".',
                        //         $this->escaper->escapeHtml($lastCode)
                        //     )
                        // );
                        $this->messageManager->addSuccessMessage(
                            __(
                                'You canceled the coupon code.'
                            )
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon code "%1" is not valid.',
                                $this->escaper->escapeHtml($lastCode)
                            )
                        );
                    }

                }
            }
        }
        else {
            $this->messageManager->addSuccessMessage(
                __(
                    'You canceled the coupon code.'
                )
            );
        }
        return $back;
    }
}
