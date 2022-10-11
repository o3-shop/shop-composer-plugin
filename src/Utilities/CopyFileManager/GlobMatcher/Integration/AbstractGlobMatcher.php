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

namespace OxidEsales\ComposerPlugin\Utilities\CopyFileManager\GlobMatcher\Integration;

use InvalidArgumentException;
use Webmozart\PathUtil\Path;

/**
 * Class AbstractGlobMatcher.
 *
 * Abstract which defines API for matching a path against a glob expression.
 */
abstract class AbstractGlobMatcher
{
    /**
     * Returns true if given path matches a glob expression.
     *
     * @param string $relativePath
     * @param string $globExpression Glob filter expressions, e.g. "*.txt" or "*.pdf".
     *
     * @throws \InvalidArgumentException If given $globExpression is not a valid string.
     * @throws \InvalidArgumentException If given $globExpression is an absolute path.
     *
     * @return bool
     */
    public function match($relativePath, $globExpression)
    {
        if (!is_string($globExpression) && !is_null($globExpression)) {
            $message = "Given value \"$globExpression\" is not a valid glob expression. " .
                "Valid expression must be a string e.g. \"*.txt\".";

            throw new InvalidArgumentException($message);
        }

        if (Path::isAbsolute((string)$globExpression)) {
            $message = "Given value \"$globExpression\" is an absolute path. " .
                "Glob expression can only be accepted if it's a relative path.";

            throw new InvalidArgumentException($message);
        }

        if (is_null($globExpression)) {
            return true;
        }

        return static::isGlobMatch($relativePath, $globExpression);
    }

    /**
     * Implementation details for matching a given path against glob expression.
     *
     * @param string $relativePath
     * @param string $globExpression Glob filter expressions, e.g. "*.txt" or "*.pdf".
     */
    abstract protected function isGlobMatch($relativePath, $globExpression);
}
