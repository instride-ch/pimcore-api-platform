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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Serializer;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;

abstract class AbstractDefinitionSerializerLoader implements LoaderInterface
{
    public function loadFromDefinition(
        ClassMetadataInterface $classMetadata,
        Definition $definition,
        array $allowedClasses,
        array $defaultFields = ['type']
    ) {
        $localizedFieldDefs = [];
        $localizedFields = $definition->getFieldDefinition('localizedfields');

        if ($localizedFields instanceof ClassDefinition\Data\Localizedfields) {
            $localizedFieldDefs = $localizedFields->getFieldDefinitions();
        }

        foreach ($defaultFields as $defaultField) {
            $this->addMetadata($classMetadata, $allowedClasses, $defaultField);
        }


        foreach ([$definition->getFieldDefinitions(), $localizedFieldDefs] as $fieldDefinitions) {
            foreach ($fieldDefinitions as $fieldDefinition) {
                if ($fieldDefinition instanceof ClassDefinition\Data\Localizedfields) {
                    foreach ($fieldDefinition->getFieldDefinitions() as $subDefinition) {
                        $this->addMetadata($classMetadata, $allowedClasses, $subDefinition->getName());
                    }
                }

                $this->addMetadata($classMetadata, $allowedClasses, $fieldDefinition->getName());
            }
        }

        return $classMetadata;
    }

    protected function addMetadata(ClassMetadataInterface $classMetadata, array $allowedClasses, $name)
    {
        $attributesMetadata = $classMetadata->getAttributesMetadata();

        if (!isset($attributesMetadata[$name])) {
            $attributesMetadata[$name] = new AttributeMetadata($name);
            $classMetadata->addAttributeMetadata($attributesMetadata[$name]);
        }

        foreach (AbstractPimcoreDefinitionSerializerLoader::DEFAULT_READ_GROUPS as $defaultGroup) {
            $attributesMetadata[$name]->addGroup($defaultGroup);
        }

        foreach (AbstractPimcoreDefinitionSerializerLoader::DEFAULT_WRITE_GROUPS as $defaultGroup) {
            $attributesMetadata[$name]->addGroup($defaultGroup);
        }

        foreach ($allowedClasses as $className) {
            $attributesMetadata[$name]->addGroup(strtolower($className).':read');
            $attributesMetadata[$name]->addGroup(strtolower($className).':write');
        }
    }
}
