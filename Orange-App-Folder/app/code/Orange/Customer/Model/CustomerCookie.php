<?php
namespace Orange\Customer\Model;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
 
class CustomerCookie
{
	const COOKIE_NAME = 'SEGMENT';
	const ORDER_COOKIE_NAME = 'lastorder';
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;
 
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;
 
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_sessionManager;
 
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
 
    /**
     * @var Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddressInstance;
 
    /**
     * [__construct ]
     *
     * @param CookieManagerInterface                    $cookieManager
     * @param CookieMetadataFactory                     $cookieMetadataFactory
     * @param SessionManagerInterface                   $sessionManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_sessionManager = $sessionManager;
        $this->_objectManager = $objectManager;
        $this->_remoteAddressInstance = $this->_objectManager->get(
            'Magento\Framework\HTTP\PhpEnvironment\RemoteAddress'
        );
    }
 
    /**
     * Get data from cookie
     *
     * @return value
     */
    public function get()
    {
        return $this->_cookieManager->getCookie(self::COOKIE_NAME);
    }
 
    /**
     * Set data to cookie
     *
     * @param [string] $value    [value of cookie]
     * @param integer  $duration [duration for cookie]
     *
     * @return void
     */
    public function set($value, $duration = 86400)
    {
		$this->delete();
        $metadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath("/")
            ->setDomain($this->_sessionManager->getCookieDomain());
 
        $this->_cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            $value,
            $metadata
        );
    }
 
    /**
     * delete cookie
     *
     * @return void
     */
    public function delete()
    {
        $this->_cookieManager->deleteCookie(
            self::COOKIE_NAME,
            $this->_cookieMetadataFactory
                ->createCookieMetadata()
                ->setPath($this->_sessionManager->getCookiePath())
                ->setDomain($this->_sessionManager->getCookieDomain())
        );
    }
 
    /**
     * used to get remote address, in which cookie data is set
     *
     * @return [string] [returns remote address]
     */
    public function getRemoteAddress()
    {
        return $this->_remoteAddressInstance->getRemoteAddress();
    }
	
	/**
     * Set data to cookie
     *
     * @param [string] $value    [value of cookie]
     * @param integer  $duration [duration for cookie]
     *
     * @return void
     */
	public function setLastOrderCookie($value, $duration = 86400)
    {
		$this->orderCookiedelete();
        $metadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath("/")
            ->setDomain($this->_sessionManager->getCookieDomain());
 
        $this->_cookieManager->setPublicCookie(
            self::ORDER_COOKIE_NAME,
            $value,
            $metadata
        );
    }
	
	/**
     * delete cookie
     *
     * @return void
     */
	public function orderCookiedelete()
    {
        $this->_cookieManager->deleteCookie(
            self::ORDER_COOKIE_NAME,
            $this->_cookieMetadataFactory
                ->createCookieMetadata()
                ->setPath($this->_sessionManager->getCookiePath())
                ->setDomain($this->_sessionManager->getCookieDomain())
        );
    }
	
	/**
     * Get data from cookie
     *
     * @return value
     */
	 
	public function getLastOrderCookie()
    {
        return $this->_cookieManager->getCookie(self::ORDER_COOKIE_NAME);
    }
	
}