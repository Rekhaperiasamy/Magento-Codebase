<?php
/**
 * An extension to fix the bug on GitHub issue #13929
 *
 * @category  Sitewards
 * @package   Sitewards_Fix13929
 * @copyright Copyright (c) Sitewards GmbH (http://www.sitewards.com)
 * @contact   mail@sitewards.com
 */

/**
 * TODO: temporary override until patch or Magento 2.3 is released.
 * https://github.com/magento/magento2/issues/13929
 */
namespace Dilmah\Symblink\Override\App\Filesystem;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Magento directories resolver.
 */
class DirectoryResolver
{
    /**
     * @var DirectoryList
     */
    private $oDirectoryList;

    /**
     * @param DirectoryList $oDirectoryList
     */
    public function __construct(DirectoryList $oDirectoryList)
    {
        $this->oDirectoryList = $oDirectoryList;
    }

    /**
     * Validate path.
     *
     * Gets real path for directory provided in parameters and compares it with specified root directory.
     * Will return TRUE if real path of provided value contains root directory path and FALSE if not.
     * Throws the \Magento\Framework\Exception\FileSystemException in case when directory path is absent
     * in Directories configuration.
     *
     * @param string $sPath
     * @param string $sDirectoryConfig
     *
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function validatePath($sPath, $sDirectoryConfig = DirectoryList::MEDIA)
    {
        $sRealPath = realpath($sPath);

        // BEGIN EDIT by @erikhansen
        /**
         * Since media directory is a symlink, need to run both paths through realpath in order for the comparison to
         * work.
         * The proper fix for this should involve a STORE > Configuration setting where an admin can choose whether to
         * allow symlinked directories.
         */
        $sRoot = realpath($this->oDirectoryList->getPath($sDirectoryConfig));

        // END EDIT

        return strpos($sRealPath, $sRoot) === 0;
    }
}
