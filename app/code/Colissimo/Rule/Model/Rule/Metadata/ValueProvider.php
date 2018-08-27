<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Rule\Metadata;

use Colissimo\Rule\Model\Rule;
use Colissimo\Rule\Model\RuleFactory;
use Colissimo\Rule\Model\Source\Actions;
use Magento\Store\Model\System\Store;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;

/**
 * Class ValueProvider
 */
class ValueProvider
{
    /**
     * @var Store $store
     */
    protected $store;

    /**
     * @var GroupRepositoryInterface $groupRepository
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder searchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DataObject $objectConverter
     */
    protected $objectConverter;

    /**
     * @var RuleFactory $colissimoRuleFactory
     */
    protected $colissimoRuleFactory;

    /**
     * @var Actions $actions
     */
    protected $actions;

    /**
     * Initialize dependencies.
     *
     * @param Store $store
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     * @param RuleFactory $colissimoRuleFactory
     * @param Actions $actions
     */
    public function __construct(
        Store $store,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        RuleFactory $colissimoRuleFactory,
        Actions $actions
    ) {
        $this->store = $store;
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->objectConverter = $objectConverter;
        $this->actions = $actions;
    }

    /**
     * Get metadata for sales rule form. It will be merged with form UI component declaration.
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getMetadataValues()
    {
        $customerGroups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return [
            'rule_information' => [
                'children' => [
                    'website_ids' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->store->getWebsiteValuesForForm(),
                                ],
                            ],
                        ],
                    ],
                    'is_active' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => [
                                        ['label' => __('Active'), 'value' => '1'],
                                        ['label' => __('Inactive'), 'value' => '0']
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'customer_group_ids' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->objectConverter->toOptionArray($customerGroups, 'id', 'code'),
                                ],
                            ],
                        ],
                    ],
                ]
            ],
            'actions' => [
                'children' => [
                    'shipping_action' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->actions->toOptionArray(),
                                ],
                            ],
                        ],
                    ],
                    'shipping_amount' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'value' => '0',
                                ],
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }
}
