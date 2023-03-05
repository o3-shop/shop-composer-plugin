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

namespace OxidEsales\ComposerPlugin\Installer\Package;

use Composer\Package\PackageInterface;
use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleFilesInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use Webmozart\PathUtil\Path;

/**
 * @inheritdoc
 */
class ModulePackageInstaller extends AbstractPackageInstaller
{
    /**
     * @deprecated will be removed in next major version
     *
     * @var string MODULES_DIRECTORY
     */
    public const MODULES_DIRECTORY = 'modules';

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return file_exists($this->formTargetPath());
    }

    /**
     * Copies module files to shop directory.
     *
     * @param string $packagePath
     */
    public function install($packagePath)
    {
        $this->getIO()->write("Installing module {$this->getPackageName()} package.");
        $this->getBootstrapModuleInstaller()->install($this->getOxidShopPackage($packagePath));
    }

    /**
     * @param PackageInterface $package
     */
    public function uninstall(string $packagePath): void
    {
        $moduleInstaller = $this->getModuleInstaller();
        $moduleInstaller->uninstall($this->getOxidShopPackage($packagePath));
    }

    /**
     * Update module files.
     *
     * @param string $packagePath
     */
    public function update($packagePath)
    {
        $package = $this->getOxidShopPackage($packagePath);

        /**
         * We check only files because during the first composer update modules may not have installed configuration
         * and module files are getting overwritten without asking if ModuleInstallerInterface is used.
         */
        if ($this->getModuleFilesInstaller()->isInstalled($package)) {
            $question = "Update operation will overwrite {$this->getPackageName()} files in the directory ";
            $question .= "source/modules. Do you want to overwrite them? (y/N) ";
            if ($this->askQuestion($question)) {
                $this->getIO()->write("Updating module {$this->getPackageName()} files...");
                $this->getBootstrapModuleInstaller()->install($package);
            }
        } else {
            $this->install($packagePath);
        }
    }

    /**
     * @return ModuleInstallerInterface
     */
    private function getModuleInstaller(): ModuleInstallerInterface
    {
        if ($this->isShopLaunched()) {
            return ContainerFactory::getInstance()->getContainer()
                ->get(ModuleInstallerInterface::class);
        } else {
            return $this->getBootstrapModuleInstaller();
        }
    }

    /**
     * @return bool
     */
    private function isShopLaunched(): bool
    {
        $container = BootstrapContainerFactory::getBootstrapContainer();
        $shopStateService = $container->get(ShopStateServiceInterface::class);

        return $shopStateService->isLaunched();
    }

    /**
     * @return ModuleFilesInstallerInterface
     */
    private function getModuleFilesInstaller(): ModuleFilesInstallerInterface
    {
        return BootstrapContainerFactory::getBootstrapContainer()->get(ModuleFilesInstallerInterface::class);
    }

    /**
     * @param string $packagePath
     *
     * @return OxidEshopPackage
     */
    private function getOxidShopPackage(string $packagePath): OxidEshopPackage
    {
        $package = new OxidEshopPackage($this->getPackage()->getName(), $packagePath);
        $extraParameters = $this->getPackage()->getExtra();

        if (isset($extraParameters['oxideshop']['blacklist-filter'])) {
            $package->setBlackListFilters($extraParameters['oxideshop']['blacklist-filter']);
        }

        if (isset($extraParameters['oxideshop']['source-directory'])) {
            $package->setSourceDirectory($extraParameters['oxideshop']['source-directory']);
        }

        if (isset($extraParameters['oxideshop']['target-directory'])) {
            $package->setTargetDirectory($extraParameters['oxideshop']['target-directory']);
        }

        return $package;
    }

    /**
     * @return string
     */
    protected function formTargetPath()
    {
        $targetDirectory = $this->getExtraParameterValueByKey(
            static::EXTRA_PARAMETER_KEY_TARGET,
            $this->getPackage()->getName()
        );

        return Path::join($this->getRootDirectory(), static::MODULES_DIRECTORY, $targetDirectory);
    }

    private function getBootstrapModuleInstaller(): ModuleInstallerInterface
    {
        return BootstrapContainerFactory::getBootstrapContainer()
            ->get('oxid_esales.module.install.service.bootstrap_module_installer');
    }
}
