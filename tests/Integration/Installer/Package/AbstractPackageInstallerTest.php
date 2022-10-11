<?php

/**
 * This file is part of O3-Shop.
 *
 * O3-Shop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2022 OXID eSales AG (https://www.oxid-esales.com)
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

declare(strict_types=1);

namespace OxidEsales\ComposerPlugin\Tests\Integration\Installer\Package;

use OxidEsales\ComposerPlugin\Utilities\VfsFileStructureOperator;
use org\bovigo\vfs\vfsStream;
use Webmozart\PathUtil\Path;

abstract class AbstractPackageInstallerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->setupVirtualFileSystem();
    }

    protected function setupVirtualFileSystem()
    {
        vfsStream::setup(
            'root',
            777,
            [
                'vendor' => [],
                'source' => [],
            ]
        );
    }

    protected function setupVirtualProjectRoot($prefix, $input)
    {
        $updated = [];

        foreach ($input as $path => $contents) {
            $updated[Path::join($prefix, $path)] = $contents;
        }

        return vfsStream::create(VfsFileStructureOperator::nest($updated));
    }

    protected function getVirtualShopSourcePath()
    {
        return $this->getVirtualFileSystemRootPath('source');
    }

    protected function getVirtualVendorPath()
    {
        return $this->getVirtualFileSystemRootPath('vendor');
    }

    protected function getVirtualFileSystemRootPath($suffix = '')
    {
        return Path::join(vfsStream::url('root'), $suffix);
    }

    protected function assertVirtualFileExists($path)
    {
        $this->assertFileExists($this->getVirtualFileSystemRootPath($path));
    }

    protected function assertVirtualFileNotExists($path)
    {
        $this->assertFileDoesNotExist($this->getVirtualFileSystemRootPath($path));
    }

    protected function assertVirtualFileEquals($expected, $actual)
    {
        $this->assertFileEquals(
            $this->getVirtualFileSystemRootPath($expected),
            $this->getVirtualFileSystemRootPath($actual)
        );
    }

    protected function assertVirtualFileNotEquals($expected, $actual)
    {
        $this->assertFileNotEquals(
            $this->getVirtualFileSystemRootPath($expected),
            $this->getVirtualFileSystemRootPath($actual)
        );
    }
}
