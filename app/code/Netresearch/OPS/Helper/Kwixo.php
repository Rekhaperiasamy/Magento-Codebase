<?php
namespace Netresearch\OPS\Helper;

/**
 * \Netresearch\OPS\Helper\Kwixo
 *
 * @package
 * @copyright 2013 Netresearch
 * @author    Michael Lühr <michael.luehr@netresearch.de>
 * @license   OSL 3.0
 */
class Kwixo extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $helper = null;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    protected $oPSHelper;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Category\MappingFactory
     */
    protected $oPSKwixoCategoryMappingFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $catalogCategoryFactory;

    /**
     * @var \Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory
     */
    protected $oPSSourceKwixoProductCategoriesFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Netresearch\OPS\Helper\Data $oPSHelper,
        \Netresearch\OPS\Model\Kwixo\Category\MappingFactory $oPSKwixoCategoryMappingFactory,
        \Magento\Catalog\Model\CategoryFactory $catalogCategoryFactory,
        \Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory $oPSSourceKwixoProductCategoriesFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->oPSHelper = $oPSHelper;
        $this->oPSKwixoCategoryMappingFactory = $oPSKwixoCategoryMappingFactory;
        $this->catalogCategoryFactory = $catalogCategoryFactory;
        $this->oPSSourceKwixoProductCategoriesFactory = $oPSSourceKwixoProductCategoriesFactory;
        $this->messageManager = $messageManager;
    }

    protected function getHelper()
    {
        if (null === $this->helper) {
            $this->helper = $this->oPSHelper;
        }

        return $this->helper;
    }

    /**
     * validates the kwixoConfiguration data
     *
     * @param array $postData the data to validate
     *
     */
    public function validateKwixoconfigurationMapping(array $postData)
    {
        $this->validateKwixoConfigurationData($postData);
        $this->validateKwixoMappingExist($postData);
        $this->validateCategoryExist($postData);
    }

    /**
     * saves the KwixoConfigurationMapping
     *
     * @param array $postData
     */
    public function saveKwixoconfigurationMapping(array $postData)
    {
        $this->validateKwixoconfigurationMapping($postData);
        $kwixoCatMapModel = $this->oPSKwixoCategoryMappingFactory->create()->load($postData['id']);
        $kwixoCatMapModel->setCategoryId($postData['category_id']);
        $kwixoCatMapModel->setKwixoCategoryId(
            $postData['kwixoCategory_id']
        );
        $kwixoCatMapModel->save();
        if (array_key_exists('applysubcat', $postData)) {
            $category = $this->catalogCategoryFactory->create()->load(
                $postData['category_id']
            );
            $subcategories = $category->getAllChildren(true);
            foreach ($subcategories as $subcategory) {
                $kwixoCatMapModel = $this->oPSKwixoCategoryMappingFactory->create()->loadByCategoryId($subcategory);
                $kwixoCatMapModel->setCategoryId($subcategory);
                $kwixoCatMapModel->setKwixoCategoryId(
                    $postData['kwixoCategory_id']
                );
                $kwixoCatMapModel->save();
            }
        }
        $this->messageManager->addSuccess(__('Successfully added Kwixo category mapping'));
    }

    /**
     * validates if the structure of a given array does match the expected kwixo
     * setting configuration
     *
     * @param array $postData - the array to inspect
     *
     * @throws \Magento\Framework\Exception\LocalizedException - if the structure does not match
     */
    private function validateKwixoConfigurationData(array $postData)
    {
        $helper = $this->getHelper();
        $isValid = true;
        $message = '';
        if (0 === count($postData)) {
            $message = __('Invalid form data provided');
            $isValid = false;
        }

        if ($isValid && !array_key_exists('id', $postData)) {
            $message = __('Invalid form data provided');
            $isValid = false;
        }

        if ($isValid && 0 < strlen($postData['id'])
            && (!is_numeric($postData['id'])
                || $postData['id'] < 0)
        ) {
            $message = __('Invalid id provided');
            $isValid = false;
        }
        if (false === $isValid) {
            throw new \Magento\Framework\Exception\LocalizedException($message);
        }
    }

    /**
     * validates if the given array contains the neccessary information for
     * a proper kwixo category setting
     *
     * @param array $postData - the array to inspect
     *
     * @throws \Magento\Framework\Exception\LocalizedException - if the array does not contain the needed
     *                             information
     *
     */
    private function validateKwixoMappingExist(array $postData)
    {
        $helper = $this->getHelper();
        $kwixoCategories = $this->oPSSourceKwixoProductCategoriesFactory->create()
            ->getValidKwixoCategoryIds();
        if (!array_key_exists('kwixoCategory_id', $postData)
            || !in_array(
                $postData['kwixoCategory_id'],
                $kwixoCategories
            )
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Invalid kwixo category provided')
            );
        }
    }

    /**
     * validates if the given array contains a proper category setting
     *
     * @param array $postData - the array to inspect
     *
     * @throws \Magento\Framework\Exception\LocalizedException - if an invalid setting is given
     */
    private function validateCategoryExist(array $postData)
    {
        $helper = $this->getHelper();
        $isValid = true;
        $message = '';
        if (!array_key_exists('category_id', $postData)) {
            $isValid = false;
            $message = __('Invalid category provided');
        }
        if ($isValid
            && (!is_numeric($postData['category_id'])
                || null === $this->catalogCategoryFactory->create()->load($postData['category_id'])->getId())
        ) {
            $isValid = false;
            $message = __('Invalid category provided');
        }
        if (false === $isValid) {
            throw new \Magento\Framework\Exception\LocalizedException($message);
        }
    }
}
