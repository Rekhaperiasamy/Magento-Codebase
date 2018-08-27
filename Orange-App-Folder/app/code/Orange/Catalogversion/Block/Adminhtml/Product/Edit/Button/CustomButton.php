<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Orange\Catalogversion\Block\Adminhtml\Product\Edit\Button;
use Magento\Ui\Component\Control\Container;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

/**
 * Class Save
 */
class CustomButton extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
	   if($this->getProduct()->getId()){
	      return [];
	   }
       if ($this->getProduct()->isReadonly()) {
            return [];
        }
       //if (!$this->getProduct()) {
        return [
            'label' => __('Draft'),
            'class' => 'save secondary',
			'id'=>'save_draft',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'product_form.product_form',
                                'actionName' => 'save',
                                'params' => [
                                    false,
									[
									'save_type'=>'draft',
									'back' => 'draft',
									]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
			'sort_order'=>200,
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
        ];
		
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options[] = [
            'id_hard' => 'save_and_new',
            'label' => __('Draft and schedulle'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
							   
                                'targetName' => 'product_form.product_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'draft',
										'save_type'=>'draft'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }
}
?>