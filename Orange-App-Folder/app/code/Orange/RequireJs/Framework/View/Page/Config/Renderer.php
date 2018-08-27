<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\RequireJs\Framework\View\Page\Config;

use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Page\Config;

/**
 * Page config Renderer model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Renderer extends  \Magento\Framework\View\Page\Config\Renderer
{
   
    /**
     * @param string $contentType
     * @param string|null $attributes
     * @return string
     */
    protected function getAssetTemplate($contentType, $attributes)
    {
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$scope=$objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
	$versionNumber=$scope->getValue('jsversion/jsversion_configuration/jsversion_number');
	if($versionNumber==""){
	   $versionNumber=time();
	}
    
        switch ($contentType) {
            case 'js':
                $groupTemplate = '<script ' . $attributes . ' src="%s?v='.$versionNumber.'"></script>' . "\n";
                break;

            case 'css':
			     $groupTemplate = '<link ' . $attributes . ' href="%s?v='.$versionNumber.'" />' . "\n";
				 break;
            default:
                $groupTemplate = '<link ' . $attributes . ' href="%s" />' . "\n";
                break;
        }
        return $groupTemplate;
    }

   
}
