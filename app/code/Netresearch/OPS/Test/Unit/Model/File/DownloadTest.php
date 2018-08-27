<?php
namespace Netresearch\OPS\Test\Unit\Model\File;

class DownloadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $objectManager;

    /**
     * @var string
     */
    private $testFile;

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->testFile      = BP . '/var/log/ops.log';
        if (!file_exists($this->testFile)) {
            $file = fopen($this->testFile, 'c');
            fclose($file);
        }
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testFailingGetFile()
    {
        /** @var \Netresearch\OPS\Model\File\Download $model */
        $model = $this->getMock('\Netresearch\OPS\Model\File\Download', null, [], '', false, false);
        $path  = 'abc';
        $model->getFile($path);
    }

    public function testSuccessGetFile()
    {
        /** @var \Netresearch\OPS\Model\File\Download $model */
        $model = $this->getMock('\Netresearch\OPS\Model\File\Download', null, [], '', false, false);
        if (filesize($this->testFile) > $model::ONE_MEGABYTE) {
            $this->assertEquals(0, strpos(basename($model->getFile($this->testFile)), 'tempFile'));
        } else {
            $this->assertEquals($model->getFile($this->testFile), $this->testFile);
        }
    }
}
