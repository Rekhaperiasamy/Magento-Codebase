<?php
namespace Orange\Coupon\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{
    protected $_coupon;
	protected $_importCoupon;

    /**
     * @param Action\Context $context
     * @param Magento\SalesRule\Model\Coupon $coupon
     */    
    public function __construct(
		Action\Context $context,
		\Magento\SalesRule\Model\Coupon $coupon,
		\Orange\Coupon\Model\CouponImport $importCoupon
	)
    {
        $this->_coupon = $coupon;
		$this->_importCoupon = $importCoupon;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Orange_Coupon::import');
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
        if ($this->getRequest()->isPost() && !empty($_FILES['import_coupons_file']['tmp_name'])) {
            try {				
				$this->_importCoupon->importFromCsvFile($this->getRequest()->getFiles('import_coupons_file'));				
				$this->messageManager->addSuccess(__('Coupons imported Successfully.'));
            }
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
				$this->messageManager->addException($e,$e->getMessage());
                //$this->messageManager->addException($e, __('Something went wrong while saving the coupon.'));
            }
        }
		else {
            $this->messageManager->addError(__('Invalid file upload attempt'));
        }
        return $resultRedirect->setPath('*/*/new');
    }
}