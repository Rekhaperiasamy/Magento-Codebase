<?php
namespace Orange\Checkout\Framework\View\Asset;

use Magento\Framework\View\Asset\MergeService;
class MergeServices extends MergeService
{
    public function getMergedAssets(array $assets, $contentType)
    {
        $isCss = $contentType == 'css';
        $isJs = $contentType == 'js';
        if (!$isCss && !$isJs) {
            throw new \InvalidArgumentException("Merge for content type '{$contentType}' is not supported.");
        }

        $isCssMergeEnabled = $this->config->isMergeCssFiles();
        $isJsMergeEnabled = $this->config->isMergeJsFiles();
		$requestOb = $this->objectManager->get('Magento\Framework\App\Request\Http'); 
		$frontName = $requestOb->getFrontname(); 
		$controlName = $requestOb->getControllername();
		if (($frontName == "checkout" && $controlName=="index") || ($frontName == "checkout" && $controlName=="cart")) {
			$isJsMergeEnabled = 1;
		}
        if (($isCss && $isCssMergeEnabled) || ($isJs && $isJsMergeEnabled)) {
            $mergeStrategyClass = \Magento\Framework\View\Asset\MergeStrategy\FileExists::class;
            if ($this->state->getMode() === \Magento\Framework\App\State::MODE_DEVELOPER) {
                $mergeStrategyClass = \Magento\Framework\View\Asset\MergeStrategy\Checksum::class;
            }

            $mergeStrategy = $this->objectManager->get($mergeStrategyClass);

            $assets = $this->objectManager->create(
                'Magento\Framework\View\Asset\Merged',
                ['assets' => $assets, 'mergeStrategy' => $mergeStrategy]
            );
        }

        return $assets;
    }
}
