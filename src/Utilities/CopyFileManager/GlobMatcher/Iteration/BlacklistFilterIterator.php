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

namespace OxidEsales\ComposerPlugin\Utilities\CopyFileManager\GlobMatcher\Iteration;

use OxidEsales\ComposerPlugin\Utilities\CopyFileManager\GlobMatcher\GlobMatcher;
use Webmozart\PathUtil\Path;

/**
 * Class BlacklistFilterIterator.
 *
 * An iterator which iterates through given iterator of files/directories and filters out the items described in list of
 * glob filter definitions (black list filtering).
 */
class BlacklistFilterIterator extends \FilterIterator
{
    /** @var array List of glob expressions, e.g. ["*.txt", "*.pdf"]. */
    private $globExpressionList;

    /** @var string Absolute root path from the start of iteration. */
    private $rootPath;

    /**
     * BlacklistFilterIterator constructor.
     *
     * @param \Iterator $iterator           An iterator which iterates through files/directories.
     * @param string    $rootPath           Absolute root path from the start of iteration.
     * @param array     $globExpressionList List of glob expressions, e.g. ["*.txt", "*.pdf"].
     */
    public function __construct(\Iterator $iterator, $rootPath, $globExpressionList)
    {
        parent::__construct($iterator);

        $this->globExpressionList = $globExpressionList;
        $this->rootPath = $rootPath;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function accept(): bool
    {
        $path = $this->convertFromSplFileInfoToString(parent::current());

        return !GlobMatcher::matchAny($this->getRelativePath($path), $this->globExpressionList);
    }

    /**
     * Get relative path from given item of iteration compared to provided root path.
     *
     * @param string $absolutePath Absolute path from iteration.
     *
     * @return string
     */
    private function getRelativePath($absolutePath)
    {
        return Path::makeRelative($absolutePath, $this->rootPath);
    }

    /**
     * Returns string to absolute path from an entry of SplFileInfo.
     *
     * @param \SplFileInfo $item Item from iteration.
     *
     * @return string
     */
    private function convertFromSplFileInfoToString(\SplFileInfo $item)
    {
        return (string)$item;
    }
}
