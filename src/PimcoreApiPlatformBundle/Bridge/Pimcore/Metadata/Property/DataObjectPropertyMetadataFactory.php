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

use ApiPlatform\Core\Metadata\Extractor\ExtractorInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Concrete;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\DataObjectFieldTypeMetadataFactory;

final class DataObjectPropertyMetadataFactory extends AbstractDefinitionPropertyMetadataFactory
{
    private $decorated;
    private $extractor;

    public function __construct(
        PropertyMetadataFactoryInterface $decorated,
        DataObjectFieldTypeMetadataFactory $pimcoreTypeFactory,
        ExtractorInterface $extractor
    )
    {
        parent::__construct($pimcoreTypeFactory);

        $this->decorated = $decorated;
        $this->extractor = $extractor;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass, string $property, array $options = []): PropertyMetadata
    {
        /**
         * @var PropertyMetadata $propertyMetadata
         */
        $propertyMetadata = $this->decorated->create($resourceClass, $property, $options);

        if (!is_subclass_of($resourceClass, Concrete::class)) {
            return $propertyMetadata;
        }

        if ($property === 'id') {
            $metadata = $this->extractor->getResources()[$resourceClass]['properties'][$property] ?? null;

            if (null === $metadata) {
                $propertyMetadata = $propertyMetadata->withIdentifier(true);
            }
            else {
                $metadataAccessors = [
                    'description' => 'get',
                    'readable' => 'is',
                    'writable' => 'is',
                    'writableLink' => 'is',
                    'readableLink' => 'is',
                    'required' => 'is',
                    'identifier' => 'is',
                    'iri' => 'get',
                    'attributes' => 'get',
                ];

                foreach ($metadataAccessors as $metadataKey => $accessorPrefix) {
                    if (null === $metadata[$metadataKey]) {
                        continue;
                    }

                    $propertyMetadata = $propertyMetadata->{'with'.ucfirst($metadataKey)}($metadata[$metadataKey]);
                }
            }

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

        return $this->processPimcoreProperty($class, $fieldDefinition, $propertyMetadata);
    }
}
