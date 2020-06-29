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
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection\Definition;
use Pimcore\Model\DataObject\Objectbrick;
use Symfony\Component\Serializer\Mapping\AttributeMetadata;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Loader\LoaderInterface;

abstract class AbstractPimcoreDefinitionSerializerLoader implements LoaderInterface
{
    const DEFAULT_READ_GROUPS = ['read', 'object:read'];
    const DEFAULT_WRITE_GROUPS = ['write', 'object:read'];

    protected function addMetadata(ClassMetadataInterface $classMetadata, $className, $name)
    {
        $attributesMetadata = $classMetadata->getAttributesMetadata();

        if (!isset($attributesMetadata[$name])) {
            $attributesMetadata[$name] = new AttributeMetadata($name);
            $classMetadata->addAttributeMetadata($attributesMetadata[$name]);
        }

        foreach (self::DEFAULT_READ_GROUPS as $defaultGroup) {
            $attributesMetadata[$name]->addGroup($defaultGroup);
        }

        foreach (self::DEFAULT_WRITE_GROUPS as $defaultGroup) {
            $attributesMetadata[$name]->addGroup($defaultGroup);
        }

        $attributesMetadata[$name]->addGroup(strtolower($className).':read');
        $attributesMetadata[$name]->addGroup(strtolower($className).':write');
    }
}
