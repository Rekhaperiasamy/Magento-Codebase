<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Customer
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Customer\Block\Form;

/**
 * Class Login
 */
class Login extends \Magento\Customer\Block\Form\Login
{

    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
        $url = $this->getData('create_account_url');
        if ($url === null) {
            $url = $this->_customerUrl->getRegisterUrl();
        }
        return $url;
    }
}
