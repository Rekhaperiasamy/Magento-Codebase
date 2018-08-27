<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Simple product data view
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Orange\Catalog\Block\Product\View;



class Gallery extends \Magento\Catalog\Block\Product\View\Gallery
{
      public function getGalleryImages()
    {
	
        $product = $this->getProduct();
        $images = $product->getMediaGalleryImages();
	   //$images = $product->aroundGetMediaGalleryImages();
        if ($images instanceof \Magento\Framework\Data\Collection) {
           // foreach ($images as $image) {
			foreach ($images as $key => $image) {
			if ($image->getMediaType() == 'image') {
                /* @var \Magento\Framework\DataObject $image */
                $image->setData(
                    'small_image_url',
                    $this->_imageHelper->init($product, 'product_page_image_small')
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'medium_image_url',
                    $this->_imageHelper->init($product, 'product_page_image_medium')
                        ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
                $image->setData(
                    'large_image_url',
                    $this->_imageHelper->init($product, 'product_page_image_large')
                        ->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
                        ->setImageFile($image->getFile())
                        ->getUrl()
                );
				}
				// if ($image->getMediaType() != 'image') {
				// $images->removeItemByKey($key);
				// }
            }
        }

        return $images;
    }
	public function getProductName()
    {
	
        $product     = $this->getProduct();
    
	   //$images = $product->aroundGetMediaGalleryImages();
      
        return $product;
    }
		public function getColorOption($colorcode)
    {
	
      $product = $this->getProduct();
      $attr = $product->getResource()->getAttribute('color');
         if ($attr->usesSource()) {
        $optionText = $attr->getSource()->getOptionText($colorcode);
        }
     
      
        return $optionText;
    }

}
