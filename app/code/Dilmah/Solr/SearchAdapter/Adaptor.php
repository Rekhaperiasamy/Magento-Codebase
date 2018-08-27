<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Solr
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */
namespace Dilmah\Solr\SearchAdapter;

use \Magento\Solr\SearchAdapter\ConnectionManager;
use \Magento\Solr\SearchAdapter\Mapper;
use \Magento\Solr\SearchAdapter\ResponseFactory;
use \Magento\Solr\SearchAdapter\Aggregation\StatisticsBuilder;
use \Dilmah\Solr\SearchAdapter\Aggregation\Builder;
use \Magento\Framework\ObjectManagerInterface;

class Adaptor extends \Magento\Solr\SearchAdapter\Adapter
{

    /**
     * @var StatisticsBuilder
     */
    private $statisticsBuilder;

    /**
     * @var ConnectionManager
     */
    private $connectionManager;

    /**
     * @var Builder
     */
    private $aggregationBuilder;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Adaptor constructor.
     * @param ConnectionManager $connectionManager
     * @param Mapper $mapper
     * @param ResponseFactory $responseFactory
     * @param StatisticsBuilder $statisticsBuilder
     * @param Builder $aggregationBuilder
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ConnectionManager $connectionManager,
        Mapper $mapper,
        ResponseFactory $responseFactory,
        StatisticsBuilder $statisticsBuilder,
        Builder $aggregationBuilder,
        ObjectManagerInterface $objectManager
    ) {
        $this->connectionManager = $connectionManager;
        $this->statisticsBuilder = $statisticsBuilder;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Magento\Framework\Search\RequestInterface $request
     * @return \Magento\Framework\Search\Response\QueryResponse
     */
    public function query(\Magento\Framework\Search\RequestInterface $request)
    {
        $requestQuery = $request->getQuery();
        $queryShould = $requestQuery->getShould();
        $queryMust = $requestQuery->getMust();
        $client = $this->connectionManager->getConnection();

        //Remove price filter to display all price ranges.
        // Otherwise it will show only the filtered product price range
        if (count($queryMust) >= 2 && array_key_exists('price', $queryMust)) {
            $queryShould['price'] = $queryMust['price'];
            unset($queryMust['price']);
        }

        if (isset($queryShould)) {
            $rawResponse =[];
            foreach ($queryShould as $filterShould) {
                $filterName = str_replace("_query", "", $filterShould->getName());
                $multiSelectQueryShould = $queryShould;
                unset($multiSelectQueryShould[$filterShould->getName()]);

                $filteredRequestQuery = $this->objectManager->create(
                    'Magento\Framework\Search\Request\Query\BoolExpression',
                    [
                        'name'=>$request->getName(),
                        'boost'=>"1",
                        'must'=>$queryMust,
                        'should'=>$multiSelectQueryShould,
                        []
                    ]
                );
                $filteredRequest=$this->objectManager->create(
                    'Magento\Framework\Search\Request',
                    [
                        'name'=>$request->getName(),
                        'indexName'=>$request->getIndex(),
                        'query'=>$filteredRequestQuery,
                        'from'=>$request->getFrom(),
                        'size'=>$request->getSize(),
                        'dimensions'=>$request->getDimensions(),
                        'buckets'=>$request->getAggregation()
                    ]
                );
                $multiSelectRequestQuery = $this->mapper->buildQuery($filteredRequest);
                $this->statisticsBuilder->build($filteredRequest, $multiSelectRequestQuery);
                $rawResponse[$filterName] = $client->query($multiSelectRequestQuery);
            }
        }
        $query = $this->mapper->buildQuery($request);
        $this->statisticsBuilder->build($request, $query);
        $rawResponse['rawResponse'] = $client->query($query);

        return $this->responseFactory->create([
            'documents' => $rawResponse['rawResponse'],
            'aggregations' => $this->aggregationBuilder->build($request, $rawResponse)
        ]);
    }
}
