<?php

namespace Netresearch\OPS\Test\Unit\Helper;

class KwixoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Netresearch\OPS\Helper\Kwixo
     */
    private $helper;

    /**
     * @var \Netresearch\OPS\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory
     */
    private $sourceKwixoProductCategoriesFactory;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    private $category;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $catalogCategoryFactory;

    /**
     * @var \Netresearch\OPS\Model\Kwixo\Category\MappingFactory
     */
    protected $categoryMappingFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->dataHelper    = $this->getMock('\Netresearch\OPS\Helper\Data', [], [], '', false, false);
        $productCategories = new \Netresearch\OPS\Model\Source\Kwixo\ProductCategories();
        $this->sourceKwixoProductCategoriesFactory
                             = $this->getMock(
                                 '\Netresearch\OPS\Model\Source\Kwixo\ProductCategoriesFactory',
                                 [],
                                 [],
                                 '',
                                 false,
                                 false
                             );
        $this->category = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false, false);
        $this->category->expects($this->any())->method('load')->will($this->returnSelf());
        $this->catalogCategoryFactory = $this->getMock('\Magento\Catalog\Model\CategoryFactory', [], [], '', false, false);
        $this->catalogCategoryFactory->expects($this->any())->method('create')->will($this->returnValue($this->category));
        $this->sourceKwixoProductCategoriesFactory->expects($this->any())->method('create')->will($this->returnValue($productCategories));
        $this->categoryMappingFactory = $this->getMock('\Netresearch\OPS\Model\Kwixo\Category\MappingFactory', [], [], '', false, false);
        $this->messageManager = $this->getMock('\Magento\Framework\Message\Manager', [], [], '', false, false);
        $this->helper        = $this->objectManager->getObject(
            '\Netresearch\OPS\Helper\Kwixo',
            [
                                                                   'oPSHelper' => $this->dataHelper,
                                                                   'oPSSourceKwixoProductCategoriesFactory' => $this->sourceKwixoProductCategoriesFactory,
                                                                   'catalogCategoryFactory' => $this->catalogCategoryFactory,
                                                                   'oPSKwixoCategoryMappingFactory' => $this->categoryMappingFactory,
                                                                   'messageManager' => $this->messageManager
                                                               ]
        );
    }

    public function testValidateKwixoconfigurationMappingContainsNoData()
    {
        $this->helperMustThrowException([]);
    }

    public function testValidateKwixoconfigurationMappingContainsInvalidId()
    {
        $this->helperMustThrowException(['od' => 1]);
    }

    public function testValidateKwixoconfigurationMappingContainsEmptyId()
    {
        $this->helperMustThrowException(['id' => '']);
    }

    public function testValidateKwixoconfigurationMappingContainsNonNumericId()
    {
        $this->helperMustThrowException(['id' => 'abc']);
    }

    public function testValidateKwixoconfigurationMappingContainsNegativeId()
    {
        $this->helperMustThrowException(['id' => -1]);
    }

    public function testValidateKwixoconfigurationMappingContainsNoKwixoCategory()
    {
        $this->helperMustThrowException(['id' => 1]);
    }

    public function testValidateKwixoconfigurationMappingContainsInvalidKwixoCategory()
    {
        $this->helperMustThrowException(
            ['id' => 1, 'kwixoCategory_id' => 666]
        );
    }

    public function testValidateKwixoconfigurationMappingContainsNoCategory()
    {
        $this->helperMustThrowException(
            ['id' => 1, 'kwixoCategory_id' => 1]
        );
    }

    public function testValidateKwixoconfigurationMappingContainsNonNumericCategory()
    {
        $this->helperMustThrowException(
            ['id' => 1, 'kwixoCategory_id' => 1, 'category_id' => 'abc']
        );
    }

    public function testValidateKwixoconfigurationMappingContainsNegativeCategory()
    {
        $this->category->expects($this->any())->method('getId')->will($this->returnValue(null));
        $this->helperMustThrowException(
            ['id' => 1, 'kwixoCategory_id' => 1, 'category_id' => -1]
        );
    }

    public function testValidateKwixoconfigurationMappingContainsNegativeCategory2()
    {
        $this->category->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->helper->validateKwixoconfigurationMapping(
            ['id' => 1, 'kwixoCategory_id' => 1, 'category_id' => 1]
        );
    }

    protected function helperMustThrowException($invalidData)
    {
        $this->setExpectedException('\Magento\Framework\Exception\LocalizedException');
        $this->helper->validateKwixoconfigurationMapping($invalidData);
    }

    public function testSaveKwixoConfigurationMapping()
    {
        $this->category->expects($this->any())->method('getId')->will($this->returnValue(1));
        $kwixoCatMap = $this->getMock('\Netresearch\OPS\Model\Kwixo\Category\Mapping', ['save', 'load'], [], '', false, false);
        $kwixoCatMap->expects($this->any())->method('load')->will($this->returnSelf());
        $this->categoryMappingFactory->expects($this->atLeastOnce())->method('create')->will($this->returnValue($kwixoCatMap));
        $this->helper->saveKwixoconfigurationMapping(
            ['id' => 1, 'category_id' => 1, 'kwixoCategory_id' => 1]
        );
        $this->assertEquals(1, $kwixoCatMap->getCategoryId());
        $this->assertEquals(1, $kwixoCatMap->getKwixoCategoryId());
    }

    public function testSaveKwixoConfigurationMappingForSubCategories()
    {
        $categoryId = 1;
        $subCategoryId = 11;
        $kwixoCategoryId = 1;

        $this->category->expects($this->any())->method('getId')->will($this->returnValue($categoryId));
        $this->category->expects($this->any())
            ->method('getAllChildren')
            ->will($this->returnValue([$subCategoryId]));
        $this->category->expects($this->any())
            ->method('load')
            ->will($this->returnSelf());
        $kwixoCatMapsubcategory = $this->getMock('\Netresearch\OPS\Model\Kwixo\Category\Mapping', ['save', 'load'], [], '', false, false);
        $kwixoCatMap = $this->getMock('\Netresearch\OPS\Model\Kwixo\Category\Mapping', ['save', 'load', 'loadByCategoryId'], [], '', false, false);
        $kwixoCatMap->expects($this->any())->method('load')->will($this->returnSelf());
        $kwixoCatMap->expects($this->any())->method('loadByCategoryId')->will($this->returnValue($kwixoCatMapsubcategory));
        $this->categoryMappingFactory->expects($this->atLeastOnce())->method('create')->will($this->returnValue($kwixoCatMap));

        $this->helper->saveKwixoconfigurationMapping(
            ['id'               => 1, 'category_id' => $categoryId,
                  'kwixoCategory_id' => $kwixoCategoryId, 'applysubcat' => true]
        );

        $this->assertEquals($categoryId, $kwixoCatMap->getCategoryId());
        $this->assertEquals($kwixoCategoryId, $kwixoCatMap->getKwixoCategoryId());
        $this->assertEquals($kwixoCategoryId, $kwixoCatMapsubcategory->getKwixoCategoryId());
    }
}
