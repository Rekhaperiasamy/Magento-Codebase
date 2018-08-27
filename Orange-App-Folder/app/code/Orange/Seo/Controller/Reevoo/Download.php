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

class Download extends \Magento\Framework\App\Action\Action {

    const MEDIAPATH = BP.DIRECTORY_SEPARATOR.'pub'.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'reevoo'.DIRECTORY_SEPARATOR;

    public function __construct(Context $context) {
        parent::__construct($context);
    }

    public function execute() {
        $media_folder = self::MEDIAPATH;
        $filetype = $this->getRequest()->getParam('filetype', false);
        $objectManager = ObjectManager::getInstance();
        $objectManager->get('Orange\Seo\Controller\Reevoo\Auth')->require_auth();

        if ($filetype == 'fr') {
            $file = 'MBF_product.csv';
        }
        else {
            $file = 'MBD_product.csv';
        }

		$url = $media_folder.$file;

        if (file_exists($url)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $file);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            ob_clean();
            flush();

            readfile($url);
            exit;
        }
    }
}
