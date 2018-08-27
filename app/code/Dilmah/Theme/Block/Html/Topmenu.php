<?php
/**
 * Netstarter Pty Ltd.
 *
 * @category    Dilmah
 * @package     Dilmah_Theme
 * @author      Netstarter Team <contact@netstarter.com>
 * @copyright   Copyright (c) 2015 Netstarter Pty Ltd. (http://www.netstarter.com.au)
 */

namespace Dilmah\Theme\Block\Html;

/**
 * Class Topmenu.
 */
class Topmenu extends \Magento\Theme\Block\Html\Topmenu
{
    /**
     * custom top menu links static block identifier.
     */
    const CUSTOM_TOP_LINKS_STATIC_BLOCK_ID = 'main_nav_custom_links';

    /**
     * mega menu banner static block identifier.
     */
    const MENU_BANNER_CUSTOM_LINKS_STATIC_BLOCK_ID = 'main_nav_tea_ranges_custom_links';

    /**
     * mega menu banner static block identifier.
     */
    const MENU_BANNER_STATIC_BLOCK_ID = 'main_nav_tea_ranges_banner';

    /**
     * Get top menu html.
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int    $limit
     *
     * @return string
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_before',
            ['menu' => $this->_menu, 'block' => $this]
        );

        $this->_menu->setOutermostClass($outermostClass);
        $this->_menu->setChildrenWrapClass($childrenWrapClass);

        $html = $this->_getHtml($this->_menu, $childrenWrapClass, $limit);

        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
        $this->_eventManager->dispatch(
            'page_block_html_topmenu_gethtml_after',
            ['menu' => $this->_menu, 'transportObject' => $transportObject]
        );
        $html = $transportObject->getHtml();

        // append the custom top menu items
        $menuContent = $html.$this->getCustomMenu();

        return $menuContent;
    }

    /**
     * Add sub menu HTML code for current menu item.
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string                            $childLevel
     * @param string                            $childrenWrapClass
     * @param int                               $limit
     * @param int                               $counter
     *
     * @return string HTML code
     */
    protected function _insertSubMenu($child, $childLevel, $childrenWrapClass, $limit, $counter)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = null;
        if ($childLevel == 0 && $limit) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $customClass = '';
        if ($isMegaMenu = ($childLevel == 0 && $counter == 1)) {
            $customClass = ' mega-menu';
        }

        $html .= '<ul class="level'.$childLevel.' submenu'.$customClass.'">';
        if ($isMegaMenu) {
            $html .= '<li><div class="mega-menu-wrapper"><div class="first-column"><ul>';
        }
        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);

        if ($isMegaMenu) {
            $html .= '</ul>';
            $customLinks = $this->getLayout()
                ->createBlock('Magento\Cms\Block\Block')
                ->setBlockId(self::MENU_BANNER_CUSTOM_LINKS_STATIC_BLOCK_ID)
                ->toHtml();
            $html .= '<div class="submenu-show-all">
                        <a href="'.$child->getUrl().'">'.__('See All').'</a>
                    </div>'; // submenu see all link
            $html .= '<div class="submenu-custom-links">'.$customLinks.'</div>'; // submenu custom links
            $html .= '</div><div class="second-column">';
            $menuBanner = $this->getLayout()
                ->createBlock('Magento\Cms\Block\Block')
                ->setBlockId(self::MENU_BANNER_STATIC_BLOCK_ID)
                ->toHtml();
            $html .= $menuBanner; // submenu banner
            $html .= '</div></div></li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree.
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string                            $childrenWrapClass
     * @param int                               $limit
     * @param array                             $colBrakes
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(
        \Magento\Framework\Data\Tree\Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    ) {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass.'-' : 'nav-';

        foreach ($children as $child) {
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix.$counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="'.$outermostClass.'" ';
                $child->setClass($outermostClass);
            }

            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '<li></li></ul></li><li class="column"><ul>';
            }

            $html .= '<li '.$this->_getRenderedMenuItemAttributes($child).'>';
            $html .= '<a href="'.$child->getUrl().'" '.$outermostClassCode.'><span>'.$this->escapeHtml(
                $child->getName()
            ).'</span></a>'.$this->_insertSubMenu(
                $child,
                $childLevel,
                $childrenWrapClass,
                $limit,
                $counter
            ).'</li>';
            ++$itemPosition;
            ++$counter;
        }

        if (count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>'.$html.'</ul></li>';
        }

        return $html;
    }

    /**
     * custom topmenu links comes from the static block.
     *
     * @return mixed
     */
    protected function getCustomMenu()
    {
        return $this->getLayout()
            ->createBlock('Magento\Cms\Block\Block')
            ->setBlockId(self::CUSTOM_TOP_LINKS_STATIC_BLOCK_ID)
            ->toHtml();
    }
}
