<?php

namespace Orange\Catalog\Block\Category;

use Magento\Framework\App\ObjectManager;

/**
 * Class View
 * @package Magento\Catalog\Block\Category
 */
class View extends \Magento\Catalog\Block\Category\View {

    protected function _prepareLayout() {
        $this->getLayout()->createBlock('Magento\Catalog\Block\Breadcrumbs');

        $category = $this->getCurrentCategory();
        if ($category) {
            $title = $category->getMetaTitle();
            if ($title) {
                $this->pageConfig->getTitle()->set($title);
            }
            $description = $category->getMetaDescription();
            if ($description) {
                $this->pageConfig->setDescription($description);
            }
            $keywords = $category->getMetaKeywords();
            if ($keywords) {
                $this->pageConfig->setKeywords($keywords);
            }

            if ($this->_categoryHelper->canUseCanonicalTag()) {

                if ($this->getCustomerTypeId() == 0) {

                    $this->pageConfig->addRemotePageAsset(
                            $category->getUrl(), 'canonical', ['attributes' => ['rel' => 'canonical']]
                    );
                } else if ($this->getCustomerTypeId() == 4) {

                    $url = $category->getUrl();
                    if (strpos($url, '/nl/') !== false) {
                        $url = str_replace("/nl/", "/nl/zelfstandigen/", $url);
                        $this->pageConfig->addRemotePageAsset(
                                $url, 'canonical', ['attributes' => ['rel' => 'canonical']]
                        );
                    } else if (strpos($url, '/fr/') !== false) {
                        $url = str_replace("/fr/", "/fr/independants/", $url);
                        $this->pageConfig->addRemotePageAsset(
                                $url, 'canonical', ['attributes' => ['rel' => 'canonical']]
                        );
                    }
                } else {
                    $this->pageConfig->addRemotePageAsset(
                            $category->getUrl(), 'canonical', ['attributes' => ['rel' => 'canonical']]
                    );
                }
            }

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle($this->getCurrentCategory()->getName());
            }
        }

        return $this;
    }

    public function getCustomerTypeId() {
        $objectManager = ObjectManager::getInstance();
        $customerGroup = $objectManager->create('Orange\Customer\Model\Session')->getCustomerTypeId();
        return $customerGroup;
    }

}
