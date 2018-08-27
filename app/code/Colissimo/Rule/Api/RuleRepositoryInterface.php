<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Api;

use Colissimo\Rule\Api\Data\RuleInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Sales rule CRUD interface
 */
interface RuleRepositoryInterface
{
    /**
     * Save colissimo rule.
     *
     * @param RuleInterface $rule
     * @return \Colissimo\Rule\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a rule ID is sent but the rule does not exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(RuleInterface $rule);

    /**
     * Get rule by ID.
     *
     * @param int $ruleId
     * @return \Colissimo\Rule\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $id is not found
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($ruleId);

    /**
     * Retrieve sales rules that match te specified criteria.
     *
     * This call returns an array of objects, but detailed information about each object’s attributes might not be
     * included. See http://devdocs.magento.com/codelinks/attributes.html#RuleRepositoryInterface to
     * determine which call to use to get detailed information about all attributes for an object.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Colissimo\Rule\Api\Data\RuleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete rule by ID.
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($ruleId);
}
