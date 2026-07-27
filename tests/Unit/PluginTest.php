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

declare(strict_types=1);

namespace OxidEsales\ComposerPlugin\Tests\Unit;

use Composer\Composer;
use Composer\Config;
use Composer\IO\NullIO;
use OxidEsales\ComposerPlugin\Plugin;
use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    /**
     * The project configuration is generated in a subprocess. A non-zero exit code of that subprocess must surface
     * as an exception instead of leaving the shop with a silently missing configuration.
     */
    public function testFailingProjectConfigurationGenerationThrows(): void
    {
        $plugin = new Plugin();
        $plugin->activate($this->makeComposer('/does/not/exist'), new NullIO());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Generating the default project configuration failed');

        $this->generateDefaultProjectConfigurationIfMissing($plugin);
    }

    private function makeComposer(string $vendorDir): Composer
    {
        $config = new Config(false);
        $config->merge(['config' => ['vendor-dir' => $vendorDir]]);

        $composer = $this->createMock(Composer::class);
        $composer->method('getConfig')->willReturn($config);

        return $composer;
    }

    private function generateDefaultProjectConfigurationIfMissing(Plugin $plugin): void
    {
        $method = new \ReflectionMethod(Plugin::class, 'generateDefaultProjectConfigurationIfMissing');
        $method->setAccessible(true);
        $method->invoke($plugin);
    }
}
