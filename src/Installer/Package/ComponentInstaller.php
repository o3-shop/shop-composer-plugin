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

use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ProjectYamlImportServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateServiceInterface;
use Psr\Container\ContainerInterface;

class ComponentInstaller extends AbstractPackageInstaller
{
    public function install($packagePath)
    {
        $this->writeInstallingMessage("component");
        $this->importServiceFile($packagePath);
    }

    public function update($packagePath)
    {
        $this->writeUpdatingMessage("component");
        $this->importServiceFile($packagePath);
    }

    /**
     * @param string $packagePath
     */
    public function uninstall(string $packagePath): void
    {
        //not implemented yet
    }

    /**
     * @param $packagePath
     */
    protected function importServiceFile($packagePath)
    {
        $projectYamlImportService = BootstrapContainerFactory::getBootstrapContainer()
            ->get(ProjectYamlImportServiceInterface::class);

        $projectYamlImportService->removeNonExistingImports();
        $projectYamlImportService->addImport($packagePath);
    }
}
