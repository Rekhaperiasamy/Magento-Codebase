<?php
/**
 * \Netresearch\OPS\Helper\Api
 *
 * @package
 * @copyright 2013 Netresearch
 * @author    Michael Lühr <michael.luehr@netresearch.de>
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Helper;

class Api extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $configModel = null;

    /**
     * @var \Netresearch\OPS\Model\ConfigFactory
     */
    protected $oPSConfigFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Netresearch\OPS\Model\ConfigFactory $oPSConfigFactory
    ) {
        parent::__construct($context);
        $this->oPSConfigFactory = $oPSConfigFactory;
    }

    /**
     * @param $status - one of the fedd back status
     *
     * @throws \Magento\Framework\Exception\LocalizedException - in case the status is not known
     * @return string - the route for redirect
     */
    public function getRedirectRouteFromStatus($status)
    {
        $route = null;
        $configModel = $this->getConfigModel();
        if ($this->isAcceptStatus($status)) {
            $route = $configModel->getAcceptRedirectRoute();
        }
        if ($this->isCancelStatus($status)) {
            $route = $configModel->getCancelRedirectRoute();
        }
        if ($this->isDeclineStatus($status)) {
            $route = $configModel->getDeclineRedirectRoute();
        }
        if ($this->isExceptionStatus($status)) {
            $route = $configModel->getExceptionRedirectRoute();
        }

        // in case none of the cases above match then the status is not known
        if (null === $route) {
            throw new \Magento\Framework\Exception\LocalizedException(__('invalid status provided'));
        }

        return $route;
    }

    /**
     * config getter
     *
     * @return \Netresearch\OPS\Model\Config
     */
    private function getConfigModel()
    {
        if (null === $this->configModel) {
            $this->configModel = $this->oPSConfigFactory->create();
        }
        return $this->configModel;
    }

    /**
     * determine if the status is known as accepted status
     *
     * @param $status - the status
     *
     * @return bool - true if the status is known as accept status, false otherwise
     */
    private function isAcceptStatus($status)
    {
        return in_array($status, [
                \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_ACCEPT
            ]);
    }

    /**
     * determine if the status is known as canceled status
     *
     * @param $status - the status
     *
     * @return bool - true if the status is known as canceled status, false otherwise
     */
    private function isCancelStatus($status)
    {
        return in_array($status, [
                \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_CANCEL
            ]);
    }

    /**
     * determine if the status is known as declined status
     *
     * @param $status - the status
     *
     * @return bool - true if the status is known as declined status, false otherwise
     */
    private function isDeclineStatus($status)
    {
        return in_array($status, [
                \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_DECLINE
            ]);
    }

    /**
     * determine if the status is known as exception status
     *
     * @param $status - the status
     *
     * @return bool - true if the status is known as exception status, false otherwise
     */
    private function isExceptionStatus($status)
    {
        return in_array($status, [
                \Netresearch\OPS\Model\Status\Feedback::OPS_ORDER_FEEDBACK_STATUS_EXCEPTION
            ]);
    }
}
