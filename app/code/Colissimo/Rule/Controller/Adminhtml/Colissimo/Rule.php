<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Controller\Adminhtml\Colissimo;

use Colissimo\Rule\Model\RuleFactory;
use Colissimo\Rule\Model\RuleRepository;
use Colissimo\Rule\Model\ResourceModel\Rule as RuleResourceModel;
use Colissimo\Rule\Model\RegistryConstants;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

/**
 * Class Rule
 */
abstract class Rule extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Colissimo_Rule::rule';

    /**
     * Core registry
     *
     * @var Registry $_coreRegistry
     */
    protected $_coreRegistry = null;

    /**
     * @var FileFactory $_fileFactory
     */
    protected $_fileFactory;

    /**
     * @var Date $_dateFilter
     */
    protected $_dateFilter;

    /**
     * @var RuleFactory $ruleFactory
     */
    protected $ruleFactory;

    /**
     * @var RuleResourceModel $ruleResource
     */
    protected $ruleResource;

    /**
     * @var RuleRepository $ruleRepository
     */
    protected $ruleRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param RuleFactory $ruleFactory
     * @param RuleResourceModel $ruleResource
     * @param RuleRepository $ruleRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        RuleFactory $ruleFactory,
        RuleResourceModel $ruleResource,
        RuleRepository $ruleRepository
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_dateFilter = $dateFilter;
        $this->ruleFactory = $ruleFactory;
        $this->ruleResource = $ruleResource;
        $this->ruleRepository = $ruleRepository;
        parent::__construct($context);
    }

    /**
     * Initiate rule
     *
     * @return void
     */
    protected function _initRule()
    {
        $this->_coreRegistry->register(
            RegistryConstants::CURRENT_COLISSIMO_RULE,
            $this->_objectManager->create('Colissimo\Rule\Model\Rule')
        );
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('rule_id')) {
            $id = (int)$this->getRequest()->getParam('rule_id');
        }

        if ($id) {
            $this->_coreRegistry->registry(RegistryConstants::CURRENT_COLISSIMO_RULE)->load($id);
        }
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Colissimo_Rule::colissimo_rule')->_addBreadcrumb(__('Colissimo'), __('Rules'));
        return $this;
    }
}
