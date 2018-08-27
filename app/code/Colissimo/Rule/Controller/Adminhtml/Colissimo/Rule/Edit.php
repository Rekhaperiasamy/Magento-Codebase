<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Controller\Adminhtml\Colissimo\Rule;

use Colissimo\Rule\Model\RuleFactory;
use Colissimo\Rule\Model\RuleRepository;
use Colissimo\Rule\Model\RegistryConstants;
use Colissimo\Rule\Controller\Adminhtml\Colissimo\Rule as AbstractRule;
use Colissimo\Rule\Model\ResourceModel\Rule as RuleResourceModel;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

/**
 * Class Edit
 */
class Edit extends AbstractRule
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param PageFactory $resultPageFactory
     * @param RuleFactory $ruleFactory
     * @param RuleResourceModel $ruleResource
     * @param RuleRepository $ruleRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        PageFactory $resultPageFactory,
        RuleFactory $ruleFactory,
        RuleResourceModel $ruleResource,
        RuleRepository $ruleRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct(
            $context, $coreRegistry, $fileFactory, $dateFilter, $ruleFactory, $ruleResource, $ruleRepository
        );
    }

    /**
     * Colissimo rule edit action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var \Colissimo\Rule\Model\Rule $model */
        $model = $this->ruleFactory->create();

        $this->_coreRegistry->register(RegistryConstants::CURRENT_COLISSIMO_RULE, $model);

        if ($id) {
            $this->ruleResource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists'));
                $this->_redirect('colissimo_rule/*');
                return;
            }
            $model->getConditions()->setFormName('colissimo_rule_form');
            $model->getConditions()->setJsFormObject(
                $model->getConditionsFieldSetId($model->getConditions()->getFormName())
            );
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();

        $this->_addBreadcrumb($id ? __('Edit Rule') : __('New Rule'), $id ? __('Edit Rule') : __('New Rule'));

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? $model->getName() : __('New Colissimo Rule')
        );

        $this->_view->renderLayout();
    }
}
