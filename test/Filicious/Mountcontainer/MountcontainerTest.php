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
class MergedFilesystemTest extends \PHPUnit_Framework_TestCase
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
	 * @var LocalFilesystem
	 */
	protected $test;

	/**
	 * @var LocalFilesystem
	 */
	protected $nest;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->merged = MountcontainerFilesystem::newConfig()->create();
		$this->src = LocalFilesystem::newConfig()->setBasePath(__DIR__ . '/../../../src')->create();
		$this->test = LocalFilesystem::newConfig()->setBasePath(__DIR__ . '/../../../test')->create();
		$this->nest = LocalFilesystem::newConfig(array('base' => __DIR__ . '/../../../test'))->create();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::mount
	 */
	public function testMount()
	{
		$this->merged->mount($this->src, 'lib/php-filesystem/src');
		$this->merged->mount($this->test, 'lib/php-filesystem/test');
		$this->merged->mount($this->nest, 'lib/php-filesystem/test/nest');
		$this->assertEquals(
			array
			(
				0 => '/lib/php-filesystem/src',
				1 => '/lib/php-filesystem/test',
				2 => '/lib/php-filesystem/test/nest'
			),
			$this->merged->mounts()
		);
		return $this->merged;
	}

	/**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::getRoot
	 */
	public function testGetRoot()
	{
		$virtualRoot = new MountcontainerFile('', null, $this->merged);
		$this->assertEquals($virtualRoot->getPathname(), $this->merged->getRoot()->getPathname());
	}

	/**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::getFile
	 * @todo   Implement testGetFile().
	 * /
	public function testGetFile()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::getFreeSpace
	 * @todo   Implement testDiskFreeSpace().
	 * /
	public function testDiskFreeSpace()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::getTotalSpace
	 * @todo   Implement testDiskTotalSpace().
	 * /
	public function testDiskTotalSpace()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
	*/

	   /**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::getFile
	 * @depends testMount
	 */
	public function testGetVirtualFile($merged)
	{
		$this->assertEquals(
			new MountcontainerFile('/lib/php-filesystem', null, $merged),
			$merged->getFile('/lib/php-filesystem')
		);
	}

	static function recursiveIterate($root, $mode)
	{
		//
		$filesystemIterator = new RecursiveFilesystemIterator($root, $mode);
		$treeIterator = new \RecursiveTreeIterator($filesystemIterator);

		$arrResult = array();
		foreach ($treeIterator as $path) {
			$arrResult[] = $path;
		}
		return $arrResult;
	}

	   /**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::glob
	 * @depends testMount
	 */
	public function testNest($merged)
	{
		$root = $this->nest->getRoot();
		$arrTest = self::recursiveIterate($root, FilesystemIterator::CURRENT_AS_BASENAME);

		$root = $merged->getFile('/lib/php-filesystem/test/nest');
		$arrNest = self::recursiveIterate($root, FilesystemIterator::CURRENT_AS_BASENAME);

		$this->assertEquals($arrTest, $arrNest);
	}

	/**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::glob
	 * @depends testMount
	 */
	public function testTree($merged)
	{
		$root = $merged->getRoot();

		$filesystemIterator = new RecursiveFilesystemIterator($root, FilesystemIterator::CURRENT_AS_BASENAME);
		$treeIterator = new \RecursiveTreeIterator($filesystemIterator);

		foreach ($treeIterator as $path) {
			echo $path . "\n";
		}
	}

	/**
	 * @covers Filicious\Mountcontainer\MountcontainerFilesystem::umount
	 * @depends testMount
	 * Note: keep this last, as otherwise the umount test will be run before the tests depending on mount are run,
	 * causing those to fail.
	 */
	public function testUmount($merged)
	{
		$merged->umount('lib/php-filesystem/test/nest');
		$merged->umount('lib/php-filesystem/test');
		$merged->umount('lib/php-filesystem/src');

		$this->assertEquals(
			$merged->mounts(),
			array()
		);
	}
}
