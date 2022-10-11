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

class ShopPackageInstallerHtaccessFilesTest extends AbstractShopPackageInstallerTest
{
    public function providerHtaccessFiles()
    {
        return [
            ['.htaccess'],
            ['bin/.htaccess'],
            ['cache/.htaccess'],
            ['out/downloads/.htaccess'],
            ['Application/views/admin/tpl/.htaccess'],
            ['test/.htaccess'],
        ];
    }

    /**
     * @dataProvider providerHtaccessFiles
     */
    public function testShopInstallProcessCopiesHtaccessFilesIfTheyAreMissing($htaccessFile)
    {
        $this->setupVirtualProjectRoot('vendor/test-vendor/test-package/source', [
            'index.php' => '<?php',
            $htaccessFile => 'Original htaccess',
        ]);

        $installer = $this->getPackageInstaller();
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileEquals("vendor/test-vendor/test-package/source/$htaccessFile", "source/$htaccessFile");
    }

    /**
     * @dataProvider providerHtaccessFiles
     */
    public function testShopInstallProcessDoesNotCopyHtaccessFilesIfTheyAreAlreadyPresent($htaccessFile)
    {
        $this->setupVirtualProjectRoot('vendor/test-vendor/test-package/source', [
            'index.php' => '<?php',
            $htaccessFile => 'Original htaccess',
        ]);
        $this->setupVirtualProjectRoot('source', [
            $htaccessFile => 'Old',
        ]);

        $installer = $this->getPackageInstaller();
        $installer->install($this->getVirtualFileSystemRootPath('vendor/test-vendor/test-package'));

        $this->assertVirtualFileNotEquals(
            "vendor/test-vendor/test-package/source/$htaccessFile",
            "source/$htaccessFile"
        );
    }
}
