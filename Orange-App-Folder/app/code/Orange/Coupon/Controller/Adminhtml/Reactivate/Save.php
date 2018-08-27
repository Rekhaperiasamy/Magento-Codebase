<?php
namespace Orange\Coupon\Controller\Adminhtml\Reactivate;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{
    protected $_coupon;

    /**
     * @param Action\Context $context
     * @param Magento\SalesRule\Model\Coupon $coupon
     */    
    public function __construct(Action\Context $context,\Magento\SalesRule\Model\Coupon $coupon)
    {
        $this->_coupon = $coupon;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Orange_Coupon::reactivate');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if($data) {
            try {
                $couponCode = $this->getRequest()->getParam('coupon_code');
                $coupon = $this->_coupon->loadByCode($couponCode);
                if($coupon->getCouponId()) {
                    $usageLimit = intval($coupon->getUsageLimit());                
                    $coupon->setUsageLimit($usageLimit + 1); //Increase the usage limit
                    $coupon->save();
                    $this->messageManager->addSuccess(__('Coupon Activated.'));
                }
                else {
                    $this->messageManager->addError(__('Invalid Coupon Code.'));                
                }
            }
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the coupon.'));
            }
        }
        return $resultRedirect->setPath('*/*/new');
    }
}