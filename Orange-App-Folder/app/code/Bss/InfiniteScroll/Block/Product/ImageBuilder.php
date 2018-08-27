<?php
/**
* BSS Commerce Co.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://bsscommerce.com/Bss-Commerce-License.txt
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento COMMUNITY edition
* BSS Commerce does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* BSS Commerce does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   BSS
* @package    Bss_InfiniteScroll
* @author     Extension Team
* @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/
namespace Bss\InfiniteScroll\Block\Product;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    public function create()
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_helper = $objectManager->create('Bss\InfiniteScroll\Helper\Data');
        if($_helper->checkActive() && !$_helper->checkBssLazy() && $_helper->enabledLazy()){
                $template = $helper->getFrame()
                    ? 'Magento_Catalog::product/image.phtml'
                    : 'Bss_InfiniteScroll::product/image_with_borders.phtml';
        }else{
            $template = $helper->getFrame()
                    ? 'Magento_Catalog::product/image.phtml'
                    : 'Magento_Catalog::product/image_with_borders.phtml';
        }
        $imagesize = $helper->getResizedImageInfo();

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' =>  $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
            ],
        ];

        return $this->imageFactory->create($data);
    }
}
