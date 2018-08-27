<?php
/**
 * Colissimo Shipping Module
 *
 * @author    Magentix
 * @copyright Copyright © 2017 Magentix. All rights reserved.
 * @license   https://www.magentix.fr/en/licence.html Magentix Software Licence
 * @link      https://colissimo.magentix.fr/
 */
namespace Colissimo\Shipping\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Directory\Model\ResourceModel\Country\Collection;
use Colissimo\Shipping\Helper\Data as ShippingHelper;

/**
 * Class Price
 */
class Price extends AbstractFieldArray
{
    /**
     * @var Factory $elementFactory
     */
    protected $elementFactory;

    /**
     * @var Collection $shippingHelper
     */
    protected $shippingHelper;

    /**
     * @var array $countries
     */
    protected $countries;

    /**
     * @var Collection $countryCollection
     */
    protected $countryCollection;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param ShippingHelper $shippingHelper
     * @param Collection $countryCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        ShippingHelper $shippingHelper,
        Collection $countryCollection,
        array $data = []
    ) {
        $this->shippingHelper    = $shippingHelper;
        $this->elementFactory    = $elementFactory;
        $this->countryCollection = $countryCollection;
        parent::__construct($context, $data);
    }

    /**
     * Initialise form fields
     *
     * @return void
     */
    protected function _construct()
    {
        $weightUnit = $this->shippingHelper->getWeightUnit();

        $this->setCountries();
        $this->addColumn('price', ['label' => __('Amount')]);
        $this->addColumn('country', ['label' => __('Country')]);
        $this->addColumn('weight_from', ['label' => __('Weight in %1 (From)', $weightUnit)]);
        $this->addColumn('weight_to', ['label' => __('Weight in %1 (To)', $weightUnit)]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }

    /**
     * Render array cell for prototypeJS template
     *
     * @param string $columnName
     * @return string
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'country' && isset($this->_columns[$columnName])) {

            if (is_array($this->countries)) {
                if (count($this->countries)) {
                    $this->countryCollection->addCountryIdFilter($this->countries);
                }
            }

            $options = $this->countryCollection->loadData()->toOptionArray(false);

            foreach ($options as $key => $option) {
                $options[$key] = array_map('addslashes', $option);
            }

            $element = $this->elementFactory->create('select');
            $element->setForm(
                $this->getForm()
            )->setName(
                $this->_getCellInputElementName($columnName)
            )->setHtmlId(
                $this->_getCellInputElementId('<%- _id %>', $columnName)
            )->setValues(
                $options
            );
            return str_replace("\n", '', $element->getElementHtml());
        }

        return parent::renderCellTemplate($columnName);
    }

    /**
     * Countries setter
     */
    public function setCountries()
    {
        $this->countries = [];
    }
}
