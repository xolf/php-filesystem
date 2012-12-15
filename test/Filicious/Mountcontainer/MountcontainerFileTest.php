<?php

/**
 * High level object oriented filesystem abstraction.
 *
 * @package filicious-core
 * @author  Tristan Lins <tristan.lins@bit3.de>
 * @author  Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author  Oliver Hoff <oliver@hofff.com>
 * @link    http://filicious.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Filicious\Mountcontainer;

use Filicious\FilesystemConfig;
use Filicious\Local\LocalFilesystem;
use Filicious\Iterator\FilesystemIterator;
use Filicious\Iterator\RecursiveFilesystemIterator;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-10-17 at 10:47:54.
 */
class MountcontainerFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MergedFilesystem
     */
    protected $merged;

    /**
     * @var LocalFilesystem
     */
    protected $src;

	/**
	 * The source file from the source fs.
	 */
	protected $srcFile;

	/**
	 * The merged file.
	 */
	protected $mergedFile;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->merged = FilesystemConfig::newConfig()->create('Filicious\Mountcontainer\MountcontainerFilesystem');
        $this->src = FilesystemConfig::newConfig()->setBasePath(__DIR__ . '/../../../src')->create('Filicious\Local\LocalFilesystem');
        $this->merged->mount($this->src, '_test');

		$this->srcFile = $this->src->getFile('/Filicious/Merged/MergedFile.php');
		$this->mergedFile = $this->merged->getFile('/_test/Filicious/Merged/MergedFile.php');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Filicious\Mountcontainer\MountcontainerFile::getMD5
     */
    public function testExpectedClass()
    {
		$this->assertEquals('Filicious\Mountcontainer\MountcontainerFile', get_class($this->mergedFile));
    }

    /**
     * @covers Filicious\Mountcontainer\MountcontainerFile::getMD5
     */
    public function testMD5()
    {
		$this->assertEquals($this->srcFile->getMD5(), $this->mergedFile->getMD5());
    }

    /**
     * @covers Filicious\Mountcontainer\MountcontainerFile::getSHA1
     */
    public function testSHA1()
    {
		$this->assertEquals($this->srcFile->getSHA1(), $this->mergedFile->getSHA1());
    }

    /**
     * @covers Filicious\Mountcontainer\MountcontainerFile::getSHA1
     */
    public function testgetFileSystem()
    {
		$this->assertEquals($this->merged, $this->mergedFile->getFileSystem());
    }

	/**
	 * @covers Filicious\Mountcontainer\MountcontainerFile::getPathname
	 */
	public function testgetPathname()
	{
		$this->assertEquals('/_test/Filicious/Merged/MergedFile.php', $this->mergedFile->getPathname());
	}
}
