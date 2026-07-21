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
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

/*
 * Generates the default O3-Shop project configuration in a dedicated PHP process.
 *
 * The composer plugin runs inside composer's own long-lived runtime. When
 * o3-shop/shop-ce upgrades itself during the same `composer update`, that runtime
 * can still hold the pre-update shop classes (and opcode cache), so compiling the
 * shop DI container in-process fails and the update has to be run a second time.
 * Running the container compile here, in a fresh process, always loads the
 * freshly installed shop-ce code — so a single `composer update` is enough.
 *
 * Usage: php generate-project-configuration.php <vendor-dir>
 */

use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\Facts\Facts;

$vendorDir = $argv[1] ?? '';
$autoloadFile = $vendorDir . '/autoload.php';

if ($vendorDir === '' || !is_file($autoloadFile)) {
    fwrite(STDERR, 'generate-project-configuration: composer autoload not found at ' . $autoloadFile . PHP_EOL);
    exit(1);
}

require_once $autoloadFile;

$bootstrapContainer = BootstrapContainerFactory::getBootstrapContainer();
$isShopLaunched = $bootstrapContainer->get(ShopStateServiceInterface::class)->isLaunched();

if ($isShopLaunched) {
    require_once (new Facts())->getSourcePath() . DIRECTORY_SEPARATOR . 'bootstrap.php';
}

if (!$bootstrapContainer->get(ProjectConfigurationDaoInterface::class)->isConfigurationEmpty()) {
    exit(0);
}

if ($isShopLaunched) {
    ContainerFactory::getInstance()->getContainer()
        ->get('oxid_esales.module.install.service.launched_shop_project_configuration_generator')
        ->generate();
} else {
    $bootstrapContainer
        ->get('oxid_esales.module.install.service.installed_shop_project_configuration_generator')
        ->generate();
}

exit(0);
