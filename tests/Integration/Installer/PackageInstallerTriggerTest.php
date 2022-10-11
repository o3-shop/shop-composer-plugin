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

namespace OxidEsales\ComposerPlugin\Tests\Integration\Installer;

use Composer\Composer;
use Composer\Config;
use Composer\IO\NullIO;
use OxidEsales\ComposerPlugin\Installer\PackageInstallerTrigger;

class PackageInstallerTriggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * The composer.json file already in source for 5.3.
     */
    public function testGetShopSourcePathByConfiguration()
    {
        $composerMock = $this->getMockBuilder(Composer::class)->getMock();
        $composerMock->method('getConfig')->withAnyParameters()->willReturn(new Config());

        $packageInstallerStub = new PackageInstallerTrigger(new NullIO(), $composerMock);
        $packageInstallerStub->setSettings([
            'source-path' => 'some/path/to/source'
        ]);
        $this->assertEquals($packageInstallerStub->getShopSourcePath(), 'some/path/to/source');
    }

    /**
     * The composer.json file is taken up from the source directory for 6.0, so we should add source to path.
     */
    public function testGetShopSourcePathFor60()
    {
        $composerMock = $this->getMockBuilder(Composer::class)->getMock();
        $composerMock->method('getConfig')->withAnyParameters()->willReturn(new Config());

        $packageInstallerStub = new PackageInstallerTrigger(new NullIO(), $composerMock);
        $result = $packageInstallerStub->getShopSourcePath();

        $this->assertEquals($result, getcwd() . '/source');
    }
}
