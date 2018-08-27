<?php
/**
 * Copyright © 2018 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Scommerce\Gdpr\Helper;

/**
 * Class Email
 * @package Scommerce\Gdpr\Helper
 */
class Email
{
    /** @var \Magento\Framework\Translate\Inline\StateInterface */
    private $inlineTranslation;

    /** @var \Magento\Framework\Mail\Template\TransportBuilder */
    private $transportBuilder;

    /**
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param string $template Email template identifier
     * @param string $sender Sender identifier
     * @param array $receiver Recipient ['email' => 'customer@example.com', 'name' => 'Customer']
     * @param array $data Template variables
     * @throws \Exception
     */
    public function send($template, $sender, $receiver, $data = [])
    {
        try {
            $this->inlineTranslation->suspend();
            $this->sendEmail($template, $sender, $receiver, $data);
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            throw $e;
        }
    }

    /**
     * @param string $template Email template identifier
     * @param string $sender Sender identifier
     * @param array $receiver Recipient
     * @param array $data Template variables
     * @throws \Magento\Framework\Exception\MailException
     */
    private function sendEmail($template, $sender, $receiver, $data = [])
    {
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($template)
            ->setTemplateOptions([
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars($data)
            ->setFrom($sender)
            ->addTo($receiver['email'], $receiver['name'])
            ->getTransport();
        $transport->sendMessage();
    }
}
