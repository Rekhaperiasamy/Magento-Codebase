<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/01/2016
 * Time: 11:30
 */

namespace Magenest\AbandonedCartReminder\Block\Adminhtml\Mail;

class Preview extends \Magento\Newsletter\Block\Adminhtml\Queue\Preview
{

    protected $_mailFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context           $context
     * @param \Magento\Newsletter\Model\TemplateFactory         $templateFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory       $subscriberFactory
     * @param \Magento\Newsletter\Model\QueueFactory            $queueFactory
     * @param \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Newsletter\Model\TemplateFactory $templateFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Newsletter\Model\QueueFactory $queueFactory,
        \Magenest\AbandonedCartReminder\Model\MailFactory $mailFactory,
        array $data = []
    ) {
        $this->_queueFactory = $queueFactory;
        $this->_mailFactory  = $mailFactory;
        parent::__construct($context, $templateFactory, $subscriberFactory, $queueFactory, $data);

    }//end __construct()


    /**
     * @param \Magento\Newsletter\Model\Template $template
     * @param string                             $id
     * @return $this
     */
    protected function loadTemplate(\Magento\Newsletter\Model\Template $template, $id)
    {
        /*
            * @var \Magento\Newsletter\Model\Queue $queue
        */
        $queue = $this->_mailFactory->create()->load($id);

        // $template->setTemplateType();
        $template->setTemplateText(htmlspecialchars_decode($queue->getContent()));
        $template->setTemplateStyles($queue->getStyles());
        return $this;

    }//end loadTemplate()
}//end class
