<?php
/**
 * \Netresearch\OPS\Helper\Data
 *
 * @package
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
namespace Netresearch\OPS\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data
{
    const LOG_FILE_NAME = 'ops.log';

    /**
     * @var \Netresearch\OPS\Model\Config
     */
    protected $oPSConfig;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $opsLogger;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * Data constructor.
     * @param \Netresearch\OPS\Model\Config $oPSConfig
     * @param \Magento\Framework\Session\Generic $frameworkGeneric
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $opsLogger
     */
    public function __construct(
        \Netresearch\OPS\Model\Config $oPSConfig,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $opsLogger,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->oPSConfig = $oPSConfig;
        $this->backendAuthSession = $backendAuthSession;
        $this->checkoutSession = $checkoutSession;
        $this->opsLogger = $opsLogger;
        $this->moduleList = $moduleList;
    }

    /**
     * Returns config model
     *
     * @return \Netresearch\OPS\Model\Config
     */
    public function getConfig()
    {
        return $this->oPSConfig;
    }

    public function getModuleVersionString()
    {
        $version = (string) $this->moduleList->getOne('Netresearch_OPS')['setup_version'];
        $plainversion = str_replace('.', '', $version);
        return 'OGNM2' . $plainversion;
    }

    /**
     * Checks if logging is enabled and if yes, logs given message to logfile
     *
     * @param string $message
     */
    public function log($message)
    {
        $separator = "\n"."===================================================================";
        $message = $this->clearMsg($message);
        if ($this->getConfig()->shouldLogRequests()) {
            $this->opsLogger->info($message . $separator);
        }
    }

    /**
     * Returns full path to ops.log
     */
    public function getLogPath()
    {
        $logPath = self::getPathToLogFile();
        return BP .  "/{$logPath}/" . self::LOG_FILE_NAME;
    }

    /**
     * @return string
     */
    public static function getPathToLogFile()
    {
        $dirConfig = DirectoryList::getDefaultConfig();
        return $dirConfig[DirectoryList::LOG][DirectoryList::PATH];
    }

    /**
     * deletes certain keys from the message which is going to logged
     *
     * @param $message - the message
     *
     * @return array - the cleared message
     */
    public function clearMsg($message)
    {
        if (is_array($message)) {
            $keysToBeDeleted = ['cvc', 'CVC'];
            foreach ($keysToBeDeleted as $keyToDelete) {
                if (array_key_exists($keyToDelete, $message)) {
                    unset($message[$keyToDelete]);
                }
            }
        }
        if (is_string($message)) {
            $message = preg_replace('/"CVC":".*"(,)/i', '', $message);
            $message = preg_replace('/"CVC":".*"/i', '', $message);
            $message = preg_replace('/"CVC".*"[A-Z]*";/', '', $message);
            $message = preg_replace('/"CVC":".*"(})/i', '}', $message);
        }
        return $message;
    }

    public function getStatusText($statusCode)
    {
        $translationOrigin = "STATUS_".$statusCode;
        $translationResult = __($translationOrigin);
        if ($translationOrigin != $translationResult) :
            return $translationResult. " ($statusCode)";
        else :
            return $statusCode;
        endif;
    }

    public function getAmount($amount)
    {
        return round($amount * 100);
    }

    public function getAdminSession()
    {
        return $this->backendAuthSession;
    }

    public function isAdminSession()
    {
        if ($this->getAdminSession()->getUser()) {
            return 0 < $this->getAdminSession()->getUser()->getUserId() || $this->getAdminSession()->isLoggedIn();
        }
        return false;
    }

    /*
     * check if user is registering or not
     */
    public function checkIfUserIsRegistering()
    {
        $isRegistering = false;
        $checkoutMethod = $this->checkoutSession->getQuote()->getCheckoutMethod();
        if ($checkoutMethod === \Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER
            || $checkoutMethod === \Magento\Quote\Model\Quote::CHECKOUT_METHOD_LOGIN_IN
           ) {
                $isRegistering = true;
        }
        return $isRegistering;
    }

    /*
     * check if user is registering or not
     */
    public function checkIfUserIsNotRegistering()
    {
        $isRegistering = false;
        $checkoutMethod = $this->checkoutSession->getQuote()->getCheckoutMethod();
        if ($checkoutMethod === \Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER) {
                $isRegistering = true;
        }
        return $isRegistering;
    }

    /**
     * @param $mappedFields
     * @param $key
     * @param $value
     * @param $frontendFields
     * @return mixed
     */
    public function getFrontendValidationFields($mappedFields, $key, $value, $frontendFields)
    {
        if (!is_array($mappedFields[$key])) {
            $frontendFields[$mappedFields[$key]] = $value;
        } else {
            foreach ($mappedFields[$key] as $mKey) {
                $frontendFields[$mKey] = $value;
            }
        }

        return $frontendFields;
    }
}
