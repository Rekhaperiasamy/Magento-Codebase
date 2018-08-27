<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Model\Rule;

use Magento\Framework\DataObject;

/**
 * Class AssociatedEntityMap
 */
class AssociatedEntityMap extends DataObject
{

    public function __construct(array $data = [])
    {
        $data = [
            'website' => [
                'associations_table' => 'colissimo_rule_website',
                'rule_id_field'      => 'rule_id',
                'entity_id_field'    => 'website_id',
            ],
            'customer_group' => [
                'associations_table' => 'colissimo_rule_customer_group',
                'rule_id_field'      => 'rule_id',
                'entity_id_field'    => 'customer_group_id',
            ]
        ];
        parent::__construct($data);
    }
}