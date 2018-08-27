<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Orange\Seooptimization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Api\Data\GroupInterface;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
	/**
     * Generate XML file
     *
     * @see http://www.sitemaps.org/protocol.html
     *
     * @return $this
     */
    public function generateXml()
    {
        $this->_initSitemapItems();  
		/** @var $_objectManager \Magento\Framework\App\ObjectManager */
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		/** @var $sohoKeyword URL Keyword for SOHO customers */
		if($this->getStoreId() == 1) {
			$sohoKeyword = 'zelfstandigen';
		}
		else {
			$sohoKeyword = 'independants';
		}	
		/** @var $sitemapItem \Magento\Framework\DataObject */
        foreach ($this->_sitemapItems as $sitemapItem) {
            $changefreq = $sitemapItem->getChangefreq();
            $priority = $sitemapItem->getPriority();
            foreach ($sitemapItem->getCollection() as $item) {
                if($item->getContextVisibility() == GroupInterface::CUST_GROUP_ALL || $item->getContextVisibility() == '0' || $item->getContextVisibility() == '') {
                    $xml = $this->_getSitemapRow(
                        $item->getUrl(),
                        $item->getUpdatedAt(),
                        $changefreq,
                        $priority,
                        $item->getImages()
                    );
                    if ($this->_isSplitRequired($xml) && $this->_sitemapIncrement > 0) {
                        $this->_finalizeSitemap();
                    }
                    if (!$this->_fileSize) {
                        $this->_createSitemap();
                    }
                    $this->_writeSitemapRow($xml);
                    // Increase counters
                    $this->_lineCount++;
                    $this->_fileSize += strlen($xml);
				}
                if($item->getContextVisibility() == GroupInterface::CUST_GROUP_ALL || $item->getContextVisibility() == '4' || $item->getContextVisibility() == '') {
    				/** @var $sohoUrl Formatted SOHO Url */
    				$sohoUrl = $sohoKeyword.'/'.$item->getUrl();
    				$xml = $this->_getSitemapRow(
                        $sohoUrl,
                        $item->getUpdatedAt(),
                        $changefreq,
                        $priority,
                        $item->getImages()
                    );
                    if ($this->_isSplitRequired($xml) && $this->_sitemapIncrement > 0) {
                        $this->_finalizeSitemap();
                    }
                    if (!$this->_fileSize) {
                        $this->_createSitemap();
                    }
                    $this->_writeSitemapRow($xml);
                    // Increase counters
                    $this->_lineCount++;
                    $this->_fileSize += strlen($xml);				
				    /** EOF SOHO URL Generation */
                }
            }
        }
        $this->_finalizeSitemap();
		
        if ($this->_sitemapIncrement == 1) {
            // In case when only one increment file was created use it as default sitemap
            $path = rtrim(
                $this->getSitemapPath(),
                '/'
            ) . '/' . $this->_getCurrentSitemapFilename(
                $this->_sitemapIncrement
            );
            $destination = rtrim($this->getSitemapPath(), '/') . '/' . $this->getSitemapFilename();

            $this->_directory->renameFile($path, $destination);
        } else {
            // Otherwise create index file with list of generated sitemaps
            $this->_createSitemapIndex();
        }

        // Push sitemap to robots.txt
        if ($this->_isEnabledSubmissionRobots()) {
            $this->_addSitemapToRobotsTxt($this->getSitemapFilename());
        }

        $this->setSitemapTime($this->_dateModel->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }
}