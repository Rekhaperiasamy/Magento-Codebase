<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 *
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Seo\Model\Source\Catalog;

/**
 * Class Robottags.
 */
class Robottags extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * noindex follow.
     */
    const NOINDEX_FOLLOW = 1;

    /**
     * index nofollow.
     */
    const INDEX_NOFOLLOW = 2;

    /**
     * noindex nofollow.
     */
    const NOINDEX_NOFOLLOW = 3;

    /**
     * index follow.
     */
    const INDEX_FOLLOW = 4;

    /**
     * Get all options.
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                [
                    'value' => self::NOINDEX_FOLLOW,
                    'label' => __('NOINDEX, FOLLOW'),
                ],
                [
                    'value' => self::INDEX_NOFOLLOW,
                    'label' => __('INDEX, NOFOLLOW'),
                ],
                [
                    'value' => self::NOINDEX_NOFOLLOW,
                    'label' => __('NOINDEX, NOFOLLOW'),
                ],
                [
                    'value' => self::INDEX_FOLLOW,
                    'label' => __('INDEX, FOLLOW'),
                ],
            ];
        }

        return $this->_options;
    }
}
