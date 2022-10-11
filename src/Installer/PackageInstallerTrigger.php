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

namespace OxidEsales\ComposerPlugin\Installer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;
use OxidEsales\ComposerPlugin\Installer\Package\AbstractPackageInstaller;
use OxidEsales\ComposerPlugin\Installer\Package\ComponentInstaller;
use OxidEsales\ComposerPlugin\Installer\Package\ShopPackageInstaller;
use OxidEsales\ComposerPlugin\Installer\Package\ModulePackageInstaller;
use OxidEsales\ComposerPlugin\Installer\Package\ThemePackageInstaller;
use Webmozart\PathUtil\Path;

/**
 * Class responsible for triggering installation process.
 */
class PackageInstallerTrigger extends LibraryInstaller
{
    public const TYPE_ESHOP = 'oxideshop';
    public const TYPE_MODULE = 'oxideshop-module';
    public const TYPE_THEME = 'oxideshop-theme';
    public const TYPE_DEMODATA = 'oxideshop-demodata';
    public const TYPE_COMPONENT = 'oxideshop-component';

    /** @var array Available installers for packages. */
    private $installers = [
        self::TYPE_ESHOP => ShopPackageInstaller::class,
        self::TYPE_MODULE => ModulePackageInstaller::class,
        self::TYPE_THEME => ThemePackageInstaller::class,
        self::TYPE_COMPONENT => ComponentInstaller::class,
    ];

    /**
     * @var array configurations
     */
    protected $settings = [];

    /**
     * Decides if the installer supports the given type
     *
     * @param  string $packageType
     * @return bool
     */
    public function supports($packageType)
    {
        return array_key_exists($packageType, $this->installers);
    }

    /**
     * @param array $settings Set additional settings.
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param PackageInterface $package
     */
    public function installPackage(PackageInterface $package)
    {
        $installer = $this->createInstaller($package);
        if (!$installer->isInstalled()) {
            $installer->install($this->getInstallPath($package));
        }
    }

    /**
     * @param PackageInterface $package
     */
    public function updatePackage(PackageInterface $package)
    {
        $installer = $this->createInstaller($package);
        $installer->update($this->getInstallPath($package));
    }

    /**
     * @param PackageInterface $package
     */
    public function uninstallPackage(PackageInterface $package): void
    {
        $installer = $this->createInstaller($package);
        $installer->uninstall($this->getInstallPath($package));
    }

    /**
     * Get the path to shop's source directory.
     *
     * @return string
     */
    public function getShopSourcePath()
    {
        $shopSource = Path::join(getcwd(), ShopPackageInstaller::SHOP_SOURCE_DIRECTORY);

        if (isset($this->settings[AbstractPackageInstaller::EXTRA_PARAMETER_SOURCE_PATH])) {
            $shopSource = $this->settings[AbstractPackageInstaller::EXTRA_PARAMETER_SOURCE_PATH];
        }

        return $shopSource;
    }

    /**
     * Creates package installer.
     *
     * @param PackageInterface $package
     * @return AbstractPackageInstaller
     */
    protected function createInstaller(PackageInterface $package)
    {
        return new $this->installers[$package->getType()]($this->io, $this->getShopSourcePath(), $package);
    }
}
