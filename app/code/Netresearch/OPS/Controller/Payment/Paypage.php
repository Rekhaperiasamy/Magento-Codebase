<?php

namespace Netresearch\OPS\Controller\Payment;

class Paypage extends \Netresearch\OPS\Controller\Payment
{
    /**
     * Display our pay page, need to ops payment with external pay page mode
     */
    public function execute()
    {
        $resultPage = $this->pageFactory->create();
        return $resultPage;
    }
}
