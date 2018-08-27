<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use FME\Geoipultimatelock\Block\Adminhtml\Restrict\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

/**
 * Class IndexActions
 */
class RestrictActions extends Column
{

    /** Url path */
    const GEOIPULTIMATELOCK_RESTRICT_URL_PATH_DELETE = 'geoipultimatelock/restrict/delete';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = array(),
        array $data = array()
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['blocked_id'])) {
                    $item[$name]['delete'] = array(
                        'href' => $this->urlBuilder->getUrl(self::GEOIPULTIMATELOCK_RESTRICT_URL_PATH_DELETE, array('blocked_id' => $item['blocked_id'])),
                        'label' => __('Delete'),
                        'confirm' => array(
                            'title' => __('Delete ${ $.$data.blocked_ip }'),
                            'message' => __('Are you sure you wan\'t to delete a ${ $.$data.blocked_ip } record?')
                        )
                    );
                }
            }
        }

        return $dataSource;
    }
}
