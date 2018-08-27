<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Scommerce\Gdpr\Api\Data\ConsentInterface;

/**
 * Interface ConsentRepositoryInterface
 * @package Scommerce\Gdpr\Api
 */
interface ConsentRepositoryInterface
{
    /**
     * @param ConsentInterface|\Magento\Framework\Model\AbstractModel $model
     * @return ConsentInterface|\Magento\Framework\Model\AbstractModel
     */
    public function save(ConsentInterface $model);

    /**
     * @param int $id
     * @return ConsentInterface|\Magento\Framework\Model\AbstractModel
     */
    public function get($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\ConsentSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param ConsentInterface|\Magento\Framework\Model\AbstractModel $model
     */
    public function delete(ConsentInterface $model);

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById($id);
}
