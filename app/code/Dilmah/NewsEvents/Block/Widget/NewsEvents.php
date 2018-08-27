<?php
namespace Dilmah\NewsEvents\Block\Widget;

class NewsEvents extends \Magento\Framework\View\Element\Template
{
    /**
     * NewsAndEvents reference
     * @var \Dilmah\NewsEvents\Block\NewsAndEvents
     */
    protected $newsEvents;

    /**
     * NewsEvents constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Dilmah\NewsEvents\Block\NewsAndEvents $newsEvent
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Dilmah\NewsEvents\Block\NewsAndEvents $newsEvent,
        array $data = []
    ) {
        $this->newsEvents = $newsEvent;
        $this->setTemplate('Dilmah_NewsEvents::widget/home_news.phtml');
        parent::__construct($context, $data);
    }

    /**
     * News item list from NewsAndEvent class
     * @param string $mode
     * @return string
     */
    public function getNewsItems($mode)
    {
        return $this->newsEvents->getNewsItems($mode);
    }
}
