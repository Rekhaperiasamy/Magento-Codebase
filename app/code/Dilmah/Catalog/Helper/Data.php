<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Catalog
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Catalog\Helper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * label manager sale attribute code.
     */
    const LABEL_MANAGER_SALE_ATTRIBUTE = 'is_sale';

    /**
     * label manager new attribute code.
     */
    const LABEL_MANAGER_NEW_ATTRIBUTE = 'is_new';

    /**
     * label manager promo attribute code.
     */
    const LABEL_MANAGER_PROMO_ATTRIBUTE = 'is_promotion';

    /**
     * is pack product attribute code.
     */
    const IS_PACK_PRODUCT_ATTRIBUTE = 'is_pack';

    /**
     * pack size attribute code.
     */
    const PACK_SIZE_ATTRIBUTE = 'pack_size';

    /**
     * pack price attribute code.
     */
    const PACK_PRICE_ATTRIBUTE = 'pack_price';

    /**
     * tea bag quantity attribute code.
     */
    const TEA_BAG_QTY_ATTRIBUTE = 'bag_qty';

    /**
     * number of servings per pack attribute code.
     */
    const NUMBER_OF_SERVINGS_ATTRIBUTE = 'servings';

    /**
     * tea format attribut code.
     */
    const TEA_FORMAT_ATTRIBUTE = 'tea_format';

    /**
     * time of the day attribute code.
     */
    const TIME_OF_THE_DAY_ATTRIBUTE = 'time_of_day';

    /**
     * tea recommendation attribute code.
     */
    const RECOMMENDATION_ATTRIBUTE = 'recommendation';

    /**
     * is combo (mix and match) product attribute
     */
    const IS_COMBO_PRODUCT_ATTRIBUTE = 'is_combo';

    /**
     * is combo (mix and match) product attribute
     */
    const GRAMMAGE_ATTRIBUTE = 'grammage';

    /**
     * shipping availability product attribute
     */
    const SHIPPING_AVAILABILITY_ATTRIBUTE = 'shipping_availability';

    /**
     * Brand Label for Dilmah
     */
    const BRAND_LABEL = 'Dilmah';

    /**
     * is Mix and Match promo product
     */
    const IS_MIX_AND_MATCH_PROMOTION = 'mix_and_match_promotion';

    /**
     * Global Store ID
     */
    const GLOBAL_STORE_ID = 1;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
