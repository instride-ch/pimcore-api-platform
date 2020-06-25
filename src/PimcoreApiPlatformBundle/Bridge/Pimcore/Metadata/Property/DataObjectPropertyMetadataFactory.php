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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Metadata\Property;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\PropertyInfo\Type;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\UnionType;

final class DataObjectPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    private $decorated;

    public function __construct(PropertyMetadataFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass, string $property, array $options = []): PropertyMetadata
    {
        $propertyMetadata = $this->decorated->create($resourceClass, $property, $options);

        if (!is_subclass_of($resourceClass, Concrete::class)) {
            return $propertyMetadata;
        }

        if ($property === 'id') {
            $propertyMetadata = $propertyMetadata->withIdentifier(true);

            if (null !== $propertyMetadata->isWritable()) {
                return $propertyMetadata;
            }

            $propertyMetadata = $propertyMetadata->withWritable(false);
        }

        if (null === $propertyMetadata->isIdentifier()) {
            $propertyMetadata = $propertyMetadata->withIdentifier(false);
        }

        $class = ClassDefinition::getById($resourceClass::classId());

        if (!$class) {
            return $propertyMetadata;
        }

        $fieldDefinition = $class->getFieldDefinition($property);

        if (!$fieldDefinition) {
            return $propertyMetadata;
        }

        if ($fieldDefinition instanceof ClassDefinition\Data\Input) {
            $propertyMetadata = $propertyMetadata->withType(new Type(Type::BUILTIN_TYPE_STRING));
        }
        elseif ($fieldDefinition instanceof ClassDefinition\Data\Image) {
            $propertyMetadata = $propertyMetadata->withType(new Type(Type::BUILTIN_TYPE_OBJECT, true, Image::class));
        }
        elseif ($fieldDefinition instanceof ClassDefinition\Data\ManyToManyObjectRelation) {
            $types = [];

            $classes = $fieldDefinition->getClasses();

            if (count($classes) === 1) {
                $className = sprintf('Pimcore\Model\DataObject\%s', ucfirst($classes[0]['classes']));
                $classType = new Type(Type::BUILTIN_TYPE_OBJECT, false, $className);

                $propertyMetadata = $propertyMetadata->withType(new Type(Type::BUILTIN_TYPE_ARRAY, true, null, true, new Type(Type::BUILTIN_TYPE_ARRAY), $classType));
            }
            else {
                if (count($classes) === 0) {
                    $types[] = new Type(Type::BUILTIN_TYPE_OBJECT, true, AbstractObject::class);
                } elseif (is_array($classes)) {
                    foreach ($classes as $item) {
                        $className = sprintf('Pimcore\Model\DataObject\%s', ucfirst($item['classes']));

                        $types[] = new Type(Type::BUILTIN_TYPE_OBJECT, true, $className);
                    }
                }

                $propertyMetadata = $propertyMetadata->withType(new UnionType($types));
            }
        }
        elseif ($fieldDefinition instanceof ClassDefinition\Data\Objectbricks) {
            $containerType = new Type(
                Type::BUILTIN_TYPE_OBJECT,
                true,
                sprintf('Pimcore\Model\DataObject\%s\%s',
                    ucfirst($class->getName()),
                    ucfirst($fieldDefinition->getName())
                )
            );

            $propertyMetadata = $propertyMetadata->withType($containerType);
        }
        elseif ($fieldDefinition instanceof ClassDefinition\Data\Fieldcollections) {
            $types = [];

            $allowedCollections = $fieldDefinition->getAllowedTypes();

            if (count($allowedCollections) === 1) {
                $className = sprintf('Pimcore\Model\DataObject\Fieldcollection\Data\%s', ucfirst($allowedCollections[0]));
                $classType = new Type(Type::BUILTIN_TYPE_OBJECT, false, $className);

                $propertyMetadata = $propertyMetadata->withType(new Type(Type::BUILTIN_TYPE_ARRAY, true, null, true, new Type(Type::BUILTIN_TYPE_ARRAY), $classType));
            }
            else {
                foreach ($allowedCollections as $item) {
                    $className = sprintf('Pimcore\Model\DataObject\Fieldcollection\Data\%s', ucfirst($item));

                    $types[] = new Type(Type::BUILTIN_TYPE_OBJECT, true, $className);
                }

                $propertyMetadata = $propertyMetadata->withType(new UnionType($types));
            }
        }

        $propertyMetadata = $propertyMetadata->withDescription($fieldDefinition->getTitle());

        return $propertyMetadata;
    }
}
