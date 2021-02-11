<?php
/**
 * Pimcore Api Platform Bundle
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/DataDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore;

use Symfony\Component\PropertyInfo\Type;

class UnionType extends Type
{
    /**
     * @var Type[]
     */
    private $types;

    public function __construct($types, Type $collectionValueType, bool $nullable = false, string $containerClassName = null)
    {
        $this->types = $types;

        parent::__construct(static::BUILTIN_TYPE_ARRAY, $nullable, $containerClassName);
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function isNullable(): bool
    {
        return true;
    }

    public function isCollection(): bool
    {
        return true;
    }

    public function getCollectionKeyType(): ?self
    {
        return new Type(Type::BUILTIN_TYPE_INT);
    }

    public function getCollectionValueType(): ?self
    {
        return $this->getClassName();
    }


}
