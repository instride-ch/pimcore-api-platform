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
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\DataObjectFieldTypeMetadataFactory;

final class FieldcollectionPropertyMetadataFactory extends AbstractDefinitionPropertyMetadataFactory
{
    private $decorated;

    public function __construct(
        PropertyMetadataFactoryInterface $decorated,
        DataObjectFieldTypeMetadataFactory $pimcoreTypeFactory
    )
    {
        parent::__construct($pimcoreTypeFactory);

        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass, string $property, array $options = []): PropertyMetadata
    {
        $propertyMetadata = $this->decorated->create($resourceClass, $property, $options);

        if (!is_subclass_of($resourceClass, AbstractData::class)) {
            return $propertyMetadata;
        }

        $identifiers = ['index', 'fieldName', 'object', 'type'];

        if (in_array($property, $identifiers, true)) {
            $propertyMetadata = $propertyMetadata->withIdentifier(true);

            return $propertyMetadata;
        }

        $reflectionClass = new \ReflectionClass($resourceClass);
        $tempInstance = $reflectionClass->newInstanceWithoutConstructor();

        if (!$tempInstance instanceof AbstractData) {
            return $propertyMetadata;
        }

        $definition = $tempInstance->getDefinition();

        $fieldDefinition = $definition->getFieldDefinition($property);

        if (!$fieldDefinition) {
            return $propertyMetadata;
        }

        return $this->processPimcoreProperty($definition, $fieldDefinition, $propertyMetadata);
    }
}
