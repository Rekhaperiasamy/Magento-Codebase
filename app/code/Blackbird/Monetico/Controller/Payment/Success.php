<?php
/**
 * Blackbird Monetico Module
 *
 * NOTICE OF LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category    Blackbird
 * @package     Blackbird_Monetico
 * @copyright   Copyright (c) 2018 Blackbird (https://black.bird.eu)
 * @author      Blackbird Team
 * @license     https://store.bird.eu/license/
 * @support     help@bird.eu
 */
namespace Blackbird\Monetico\Controller\Payment;

class Success extends \Blackbird\Monetico\Controller\Payment
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_config->setMethodCode('monetico');
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->_checkoutSession->getLastRealOrder()) {
            $resultRedirect->setPath('checkout/onepage/success');
        } else {
            $resultRedirect->setPath('/');
        }

        return $resultRedirect;
    }
}
