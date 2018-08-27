<?php
namespace Dilmah\CountryPopup\Block;
class CountryCheck extends \Magento\Framework\View\Element\Template
{
    private $popupHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Dilmah\CountryPopup\Helper\Data $popupHelper
    ) {
        parent::__construct($context);
        $this->popupHelper = $popupHelper;
    }

    public function getVisitorGeoData()
    {
        return $this->popupHelper->getVisitorGeoData();
    }
}