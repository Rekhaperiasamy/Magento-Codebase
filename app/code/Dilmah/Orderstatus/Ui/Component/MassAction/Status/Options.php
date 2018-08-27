<?php

namespace Dilmah\Orderstatus\Ui\Component\MassAction\Status;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;
use Dilmah\Orderstatus\Model\Config\Source\Order\Statuses;

class Options implements JsonSerializable
{
    /**
     * @var Statuses
     */
    private $statuses;

    /**
     * @var array
     */
    private $options;

    /**
     * Additional options params
     *
     * @var array
     */
    private $data;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    private $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    private $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    private $additionalData = [];

    /**
     * @param UrlInterface $urlBuilder
     * @param Statuses $statuses
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Statuses $statuses,
        array $data = []
    ) {
        $this->data = $data;
        $this->statuses = $statuses;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $options = $this->statuses->toOptionArray();
            $this->prepareData();

            foreach ($options as $optionCode) {
                $value = $optionCode['value'];

                $this->options[$value] = [
                    'type' => $value,
                    'label' => $optionCode['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$value]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $value]
                    );
                }

                $this->options[$value] = array_merge_recursive(
                    $this->options[$value],
                    $this->additionalData
                );
            }

            $this->options = array_values($this->options);
        }

        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    private function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
