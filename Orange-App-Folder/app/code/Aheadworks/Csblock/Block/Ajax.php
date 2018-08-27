<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Csblock\Block;

/**
 * Class Ajax
 * @package Aheadworks\Csblock\Block
 */
class Ajax extends \Magento\Framework\View\Element\Template
{
    /**
     * Retrieve script options encoded to json
     *
     * @return string
     */
    public function getScriptOptions()
    {
       $params = [
            'url' => $this->getUrl(
                'csblock/block/render/',
                [
                    '_current' => true,
                    '_secure' => $this->templateContext->getRequest()->isSecure()
                ]
            )
        ];
		return json_encode($params);
    }
}
