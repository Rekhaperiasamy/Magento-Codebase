<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_LazyLoad
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\LazyLoad\Block;

use \Magento\Catalog\Helper\ImageFactory as HelperFactory;
use \Magento\Catalog\Block\Product\ImageFactory as ImageFactory;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{

    public function __construct(
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        \Lof\LazyLoad\Helper\Data $helper
    ) {
        $this->helperFactory = $helperFactory;
        $this->imageFactory  = $imageFactory;
        $this->_helper       = $helper;
    }

    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create()
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        if ($this->_helper->getConfig('general/enable')) {
			 $template = $helper->getFrame()
                ? 'Lof_LazyLoad::product/image.phtml'
                : 'Lof_LazyLoad::product/image_with_borders.phtml';
        } else {
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