<?php
namespace Orange\Seooptimization\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class SeoRobots implements ObserverInterface 
{
	protected $request;
	protected $layoutFactory;

	public function __construct(
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\View\Page\Config $layoutFactory
	)
	{
		$this->request = $request;
		$this->layoutFactory = $layoutFactory;
	}

	public function execute(Observer $observer) 
	{
		$fullActionName = $this->request->getFullActionName();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		if ($fullActionName == "catalog_product_view"){
			$product = $objectManager->get('Magento\Framework\Registry')->registry('current_product');
			if($product->getExcludeInSitemap() == 1) {
				$this->layoutFactory->setRobots('NOINDEX,NOFOLLOW');
			}
		}
		if ($fullActionName == "catalog_category_view"){
			$category = $objectManager->get('Magento\Framework\Registry')->registry('current_category');
			if($category->getExcludesitemap() == 1) {
				$this->layoutFactory->setRobots('NOINDEX,NOFOLLOW');
			}
		}
	}
}