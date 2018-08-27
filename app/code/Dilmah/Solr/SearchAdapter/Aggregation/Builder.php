<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Solr
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Solr\SearchAdapter\Aggregation;

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Solr\SearchAdapter\Aggregation\Builder\BucketBuilderInterface;
use Solarium\Core\Query\Result\ResultInterface;

class Builder
{

    /**
     * @var DataProviderInterface[]
     */
    private $dataProviderContainer;

    /**
     * @var BucketBuilderInterface[]
     */
    private $aggregationContainer;

    /**
     * @param  DataProviderInterface[] $dataProviderContainer
     * @param  BucketBuilderInterface[] $aggregationContainer
     */
    public function __construct(
        array $dataProviderContainer,
        array $aggregationContainer
    ) {
        $this->dataProviderContainer = $dataProviderContainer;
        $this->aggregationContainer = $aggregationContainer;
    }

    /**
     * @param RequestInterface $request
     * @param array $baseQueryResult
     * @return array
     */
    public function build(RequestInterface $request, array $baseQueryResult)
    {
        $aggregations = [];
        $buckets = $request->getAggregation();
        $dataProvider = $this->dataProviderContainer[$request->getIndex()];
        foreach ($buckets as $bucket) {
            $aggregationBuilder = $this->aggregationContainer[$bucket->getType()];
            if (array_key_exists($bucket->getField(), $baseQueryResult)) {
                $aggregations[$bucket->getName()] = $aggregationBuilder->build(
                    $bucket,
                    $request->getDimensions(),
                    $baseQueryResult[$bucket->getField()],
                    $dataProvider
                );
            } else {
                $aggregations[$bucket->getName()] = $aggregationBuilder->build(
                    $bucket,
                    $request->getDimensions(),
                    $baseQueryResult['rawResponse'],
                    $dataProvider
                );
            }
        }

        return $aggregations;
    }
}
