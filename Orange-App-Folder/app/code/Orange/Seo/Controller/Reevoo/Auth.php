<?php
/*
Defect: 5695
Issue: REEVO Product Export is NOT protected by Basic Authentication at Magento
Solution: Created new separate URL, redirect to Reevoo Controller.
Name: Madana Gopal K
Date: 29/11/2017
*/

namespace Orange\Seo\Controller\Reevoo;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Action\Context;

class Auth extends \Magento\Framework\App\Action\Action {

    protected $scopeConfig;

    public function __construct(Context $context, \Magento\Framework\App\Helper\Context $scope_context) {
        parent::__construct($context);
        $this->scopeConfig = $scope_context->getScopeConfig();
    }

    public function execute() {
        $this->require_auth();
        $objectManager = ObjectManager::getInstance();    
        echo $objectManager->get('Orange\Catalog\Helper\ReevooFeedCsv')->generatesFeeds();    
        $baseDir = $objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl();
        $baseDir =substr($baseDir,0,-4);
        $url = $baseDir.'/nl/export/reevoo/downloadnl';
        $urlFr=  $baseDir.'/fr/export/reevoo/downloadfr';
        echo '<h3>Reevoo Feed Files Download</h3></br>';
        echo '<div style="float:left;background-color: #e7e7e7; /* Green */border: none;color: white;padding: 10px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;-webkit-transition-duration: 0.4s; /* Safari */transition-duration: 0.4s;
        ">
        <a target="_blank" href = "'.$url.'">Click to Download Reevoo Feed File for Nl</a></div>'; 
        echo '<div style="float:left;background-color: #e7e7e7; /* Green */border: none;color: white;padding: 10px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;cursor: pointer;-webkit-transition-duration: 0.4s; /* Safari */transition-duration: 0.4s;
        "><a target="_blank"  href = "'.$urlFr.'">Click to Download Reevoo Feed File for Fr</a></div>'; 
    }

    public function require_auth() {
        $AUTH_USER = $this->scopeConfig->getValue('reevoo_product_export/reevooauth__configuration/reevooauth_user_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $AUTH_PASS = $this->scopeConfig->getValue('reevoo_product_export/reevooauth__configuration/reevooauth_user_pass', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (
            !$has_supplied_credentials ||
            $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        );
        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Reevoo Product Export"');
            echo "<h2>Access denied</h2>";
            exit;
        }
    }

}
