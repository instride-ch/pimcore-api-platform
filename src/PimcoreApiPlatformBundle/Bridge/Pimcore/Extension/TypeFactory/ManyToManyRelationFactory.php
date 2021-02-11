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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\TypeFactory;

use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\PropertyInfo\Type;

class ManyToManyRelationFactory extends ManyToManyObjectRelationFactory
{
    public function supports($classDefinition, Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof Data\ManyToManyRelation && !$fieldDefinition instanceof Data\AdvancedManyToManyRelation;
    }

    public function create(
        $classDefinition,
        Data $fieldDefinition,
        PropertyMetadata $propertyMetadata
    ): PropertyMetadata {
        $types = [];
        $classes = $fieldDefinition->getClasses();

        if (count($classes) === 1) {
            $item = $classes[0];

            $className = sprintf('Pimcore\Model\DataObject\%s', ucfirst($item['classes']));
            $type = new Type(Type::BUILTIN_TYPE_OBJECT, true, null, true, new Type(TYpe::BUILTIN_TYPE_INT),
                new Type(Type::BUILTIN_TYPE_OBJECT, false, $className));

            $propertyMetadata = $propertyMetadata->withType($type);

            return $propertyMetadata;
        }

        $type = new Type(
            Type::BUILTIN_TYPE_OBJECT,
            true,
            null,
            true,
            new Type(Type::BUILTIN_TYPE_INT),
            new Type(
                Type::BUILTIN_TYPE_OBJECT,
                false,
                ElementInterface::class
            )
        );

        $propertyMetadata = $propertyMetadata->withType($type);

        return $propertyMetadata;
    }
}
