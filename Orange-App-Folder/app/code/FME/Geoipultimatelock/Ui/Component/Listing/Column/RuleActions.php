<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Geoipultimatelock\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use FME\Geoipultimatelock\Block\Adminhtml\Rule\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

/**
 * Class IndexActions
 */
class RuleActions extends Column
{

    /** Url path */
    const GEOIPULTIMATELOCK_URL_PATH_EDIT = 'geoipultimatelock/rule/edit';
    const GEOIPULTIMATELOCK_URL_PATH_DELETE = 'geoipultimatelock/rule/delete';

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
        array $data = array(),
        $editUrl = self::GEOIPULTIMATELOCK_URL_PATH_EDIT
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
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
                if (isset($item['geoipultimatelock_id'])) {
                    $item[$name]['edit'] = array(
                        'href' => $this->urlBuilder->getUrl($this->editUrl, array('geoipultimatelock_id' => $item['geoipultimatelock_id'])),
                        'label' => __('Edit')
                    );
                    $item[$name]['delete'] = array(
                        'href' => $this->urlBuilder->getUrl(self::GEOIPULTIMATELOCK_URL_PATH_DELETE, array('geoipultimatelock_id' => $item['geoipultimatelock_id'])),
                        'label' => __('Delete'),
                        'confirm' => array(
                            'title' => __('Delete ${ $.$data.title }'),
                            'message' => __('Are you sure you wan\'t to delete a ${ $.$data.title } record?')
                        )
                    );
                }
            }
        }

        return $dataSource;
    }
}
