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
use Symfony\Component\PropertyInfo\Type;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\DataObjectFieldTypeMetadataFactoryInterface;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\UnionType;

class ManyToOneObjectRelationFactory implements DataObjectFieldTypeMetadataFactoryInterface
{
    public function supports($classDefinition, Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof Data\ManyToOneRelation;
    }

    public function create(
        $classDefinition,
        Data $fieldDefinition,
        PropertyMetadata $propertyMetadata
    ): PropertyMetadata {
        $types = [];
        $classes = $fieldDefinition->getClasses();

        if (count($classes) === 0) {
            $types[] = new Type(Type::BUILTIN_TYPE_OBJECT, true, AbstractObject::class);
        } elseif (count($classes) === 1) {
            $className = sprintf('Pimcore\Model\DataObject\%s', ucfirst($classes[0]['classes']));

            $propertyMetadata = $propertyMetadata->withType(new Type(Type::BUILTIN_TYPE_OBJECT, true, $className));

            return $propertyMetadata;
        } elseif (is_array($classes)) {
            foreach ($classes as $item) {
                $className = sprintf('Pimcore\Model\DataObject\%s', ucfirst($item['classes']));

                $types[] = new Type(Type::BUILTIN_TYPE_OBJECT, true, $className);
            }
        }

        $propertyMetadata = $propertyMetadata->withType(new UnionType($types));

        return $propertyMetadata;
    }
}
