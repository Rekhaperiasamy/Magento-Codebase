<?php
/**
 * Copyright © 2017 Magentix. All rights reserved.
 *
 * NOTICE OF LICENSE
 * This source file is subject to commercial licence, do not copy or distribute without authorization
 */
namespace Colissimo\Rule\Controller\Adminhtml\Colissimo\Rule;

use Colissimo\Rule\Controller\Adminhtml\Colissimo\Rule as AbstractRule;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Exception;

/**
 * Class Save
 */
class Save extends AbstractRule
{
    /**
     * Colissimo Rule save action
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var $model \Colissimo\Rule\Model\Rule */
                $this->_eventManager->dispatch(
                    'adminhtml_controller_colissimo_rule_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();

                $id = $this->getRequest()->getParam('rule_id');

                /** @var \Colissimo\Rule\Model\Rule $model */
                $model = $this->ruleFactory->create();

                if ($id) {
                    $this->ruleResource->load($model, $id);

                    if ($id != $model->getId()) {
                        throw new LocalizedException(__('The wrong rule is specified.'));
                    }
                }

                $session = $this->_objectManager->get('Magento\Backend\Model\Session');

                $validateResult = $model->validateData(new DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('colissimo_rule/*/edit', ['id' => $model->getId()]);
                    return;
                }

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }

                unset($data['rule']);
                $model->loadPost($data);

                $session->setPageData($model->getData());

                $this->ruleRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('colissimo_rule/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('colissimo_rule/*/');
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('colissimo_rule/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('colissimo_rule/*/new');
                }
                return;
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('colissimo_rule/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('colissimo_rule/*/');
    }
}
