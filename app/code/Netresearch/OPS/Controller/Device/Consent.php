<?php

namespace Netresearch\OPS\Controller\Device;

class Consent extends \Netresearch\OPS\Controller\Device
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->consent();
    }
}
