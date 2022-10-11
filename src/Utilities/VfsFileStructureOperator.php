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

namespace OxidEsales\ComposerPlugin\Utilities;

/**
 * Class VfsFileStructureOperator.
 */
class VfsFileStructureOperator
{
    /**
     * Convert given flat file system structure into nested one.
     *
     * @param array|null $flatFileSystemStructure
     *
     * @return array
     */
    public static function nest($flatFileSystemStructure = null)
    {
        if (null !== $flatFileSystemStructure && false === is_array($flatFileSystemStructure)) {
            throw new \InvalidArgumentException("Given input argument must be an array.");
        }

        if (null === $flatFileSystemStructure) {
            return [];
        }

        $nestedFileSystemStructure = [];

        foreach ($flatFileSystemStructure as $pathEntry => $contents) {
            $pathEntries = explode(DIRECTORY_SEPARATOR, $pathEntry);

            $pointerToBranch = &$nestedFileSystemStructure;
            foreach ($pathEntries as $singlePathEntry) {
                $singlePathEntry = trim($singlePathEntry);

                if ($singlePathEntry !== '') {
                    if (!is_array($pointerToBranch)) {
                        $pointerToBranch = [];
                    }

                    if (!key_exists($singlePathEntry, $pointerToBranch)) {
                        $pointerToBranch[$singlePathEntry] = [];
                    }

                    $pointerToBranch = &$pointerToBranch[$singlePathEntry];
                }
            }

            if (substr($pathEntry, -1) !== DIRECTORY_SEPARATOR) {
                $pointerToBranch = $contents;
            }
        }

        return $nestedFileSystemStructure;
    }
}
