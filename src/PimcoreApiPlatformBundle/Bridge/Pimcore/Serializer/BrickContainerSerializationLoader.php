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

use Pimcore\Model\DataObject\Objectbrick;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;

class BrickContainerSerializationLoader implements LoaderInterface
{
    public function loadClassMetadata(ClassMetadataInterface $classMetadata)
    {
        $class = $classMetadata->getName();

        if (!is_subclass_of($class, Objectbrick::class)) {
            return $classMetadata;
        }

        $tempInstance = $classMetadata->getReflectionClass()->newInstanceWithoutConstructor();

        if (!$tempInstance instanceof Objectbrick) {
            return $classMetadata;
        }

        $getters = $tempInstance->getBrickGetters();
        $attributesMetadata = $classMetadata->getAttributesMetadata();

        foreach ($getters as $getter) {
            $getter = substr($getter, 3);

            $definition = Objectbrick\Definition::getByKey($getter);

            if (!$definition) {
                continue;
            }

            if (!isset($attributesMetadata[$getter])) {
                $attributesMetadata[$getter] = new AttributeMetadata($getter);
                $classMetadata->addAttributeMetadata($attributesMetadata[$getter]);
            }

            foreach (AbstractPimcoreDefinitionSerializerLoader::DEFAULT_READ_GROUPS as $defaultGroup) {
                $attributesMetadata[$getter]->addGroup($defaultGroup);
            }

            foreach (AbstractPimcoreDefinitionSerializerLoader::DEFAULT_WRITE_GROUPS as $defaultGroup) {
                $attributesMetadata[$getter]->addGroup($defaultGroup);
            }

            foreach ($definition->getClassDefinitions() as $classDefinition) {
                $attributesMetadata[$getter]->addGroup(strtolower($classDefinition['classname']).':read');
                $attributesMetadata[$getter]->addGroup(strtolower($classDefinition['classname']).':write');
            }
        }

        return $classMetadata;
    }
}
