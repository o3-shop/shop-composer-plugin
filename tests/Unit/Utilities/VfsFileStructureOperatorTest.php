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

namespace OxidEsales\ComposerPlugin\Tests\Unit\Utilities;

use OxidEsales\ComposerPlugin\Utilities\VfsFileStructureOperator;

/**
 * Class FileStructureOperatorTest.
 */
class VfsFileStructureOperatorTest extends \PHPUnit\Framework\TestCase
{
    public function testReturnEmptyListWhenNoInputIsProvided()
    {
        $this->assertSame([], VfsFileStructureOperator::nest());
    }

    public function testReturnEmptyListWhenNullInputIsProvided()
    {
        $this->assertSame([], VfsFileStructureOperator::nest(null));
    }

    public function testReturnEmptyListWhenEmptyInputArrayIsProvided()
    {
        $this->assertSame([], VfsFileStructureOperator::nest([]));
    }

    public function testThrowAnExceptionIfInputIsNotAnArray()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Given input argument must be an array.");
        VfsFileStructureOperator::nest(1);
    }

    public function testReturnArrayAsIsWhenOnlyItemIsPresent()
    {
        $this->assertSame(['vendor' => 'abc'], VfsFileStructureOperator::nest(['vendor' => 'abc']));
    }

    public function testReturnArrayAsIsWhenMultipleItemsArePresent()
    {
        $this->assertSame(
            ['foo' => 'abc', 'bar' => 'def'],
            VfsFileStructureOperator::nest(['foo' => 'abc', 'bar' => 'def'])
        );
    }

    public function testReturnArrayAsIsWhenOnlyOneFileIsPresent()
    {
        $input = [
            'file' => 'Contents'
        ];

        $this->assertSame($input, VfsFileStructureOperator::nest($input));
    }

    public function testReturnArrayAsIsWhenOnlyOneFileIsPresentIgnoringSpacesAtBeginningAndEnd()
    {
        $input = [
            '  file ' => 'Contents'
        ];

        $expectedOutput = [
            'file' => 'Contents'
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnArrayAsIsWhenMultipleFilesArePresent()
    {
        $input = [
            'file'        => 'Contents',
            'second_file' => 'Second Contents'
        ];

        $this->assertSame($input, VfsFileStructureOperator::nest($input));
    }

    public function testReturnArrayWithSingleItemWhenSameMultipleFilesArePresentLastOneBeingAsOverrider()
    {
        $input = [
            'file'     => 'Contents',
            '  file  ' => 'Second Contents'
        ];

        $expectedOutput = [
            'file' => 'Second Contents',
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenSingleItemContainsMultiLevelPath()
    {
        $input = [
            'directory/file' => 'contents'
        ];

        $expectedOutput = [
            'directory' => [
                'file' => 'contents'
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenLastItemContainsMultiLevelPath()
    {
        $input = [
            'directory/fake_file'           => 'contents',
            'directory/fake_file/real_file' => 'real contents',
        ];

        $expectedOutput = [
            'directory' => [
                'fake_file' => [
                    'real_file' => 'real contents'
                ]
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenSingleItemContainsMultiLevelPathWithTrailingSlash()
    {
        $input = [
            'directory/sub/' => 'contents'
        ];

        $expectedOutput = [
            'directory' => [
                'sub' => []
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenMultipleItemsContainsMultiLevelPathWithSameBase()
    {
        $input = [
            'directory/file'        => 'contents',
            'directory/second_file' => 'second contents',
        ];

        $expectedOutput = [
            'directory' => [
                'file'        => 'contents',
                'second_file' => 'second contents',
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenMultipleItemsContainsMultiLevelPathWithSameBaseButWithBreakPointInside()
    {
        $input = [
            'directory/file'        => 'contents',
            'directory_a/file'      => 'a contents',
            'directory/second_file' => 'second contents',
        ];

        $expectedOutput = [
            'directory'   => [
                'file'        => 'contents',
                'second_file' => 'second contents',
            ],
            'directory_a' => [
                'file' => 'a contents',
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenSingleItemContainsMultiLevelPathMoreThenOneLevelDeep()
    {
        $input = [
            'directory/another_directory/file' => 'contents'
        ];

        $expectedOutput = [
            'directory' => [
                'another_directory' => [
                    'file' => 'contents'
                ]
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenMultipleItemsContainsMultiLevelPathWithDifferentBase()
    {
        $input = [
            'directory/file'               => 'contents',
            'second_directory/second_file' => 'second contents',
        ];

        $expectedOutput = [
            'directory'        => [
                'file' => 'contents',
            ],
            'second_directory' => [
                'second_file' => 'second contents',
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenComplexCasePresented()
    {
        $input = [
            'file'                                     => 'contents',
            'directory/file'                           => 'second contents',
            'directory_a/directory_b/directory_c/file' => 'third contents',
            'directory/file_b'                         => 'b contents',
            ' file_c  '                                => 'c contents',
            ' file_c   '                               => 'c override contents',
            'fake_file'                                => 'fake contents',
            'fake_file/real_file'                      => 'real contents',
        ];

        $expectedOutput = [
            'file'        => 'contents',
            'directory'   => [
                'file'   => 'second contents',
                'file_b' => 'b contents',
            ],
            'directory_a' => [
                'directory_b' => [
                    'directory_c' => [
                        'file' => 'third contents',
                    ]
                ]
            ],
            'file_c'      => 'c override contents',
            'fake_file'   => [
                'real_file' => 'real contents',
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }
}
