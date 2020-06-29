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

use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyNameCollection;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;

final class BrickPropertyNameCollectionFactory implements PropertyNameCollectionFactoryInterface
{
    private $decorated;

    public function __construct(PropertyNameCollectionFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(string $resourceClass, array $options = []): PropertyNameCollection
    {
        $collection = $this->decorated->create($resourceClass, $options);

        if (!is_subclass_of($resourceClass, AbstractData::class)) {
            return $collection;
        }

        $reflectionClass = new \ReflectionClass($resourceClass);
        $tempInstance = $reflectionClass->newInstanceWithoutConstructor();

        if (!$tempInstance instanceof AbstractData) {
            return $collection;
        }

        $definition = $tempInstance->getDefinition();

        $properties = [];

        foreach ($definition->getFieldDefinitions() as $fieldDefinition) {
            $properties[$fieldDefinition->getName()] = true;
        }

        $localizedFields = $definition->getFieldDefinition('localizedfields');

        if ($localizedFields instanceof ClassDefinition\Data\Localizedfields) {
            foreach ($localizedFields->getFieldDefinitions() as $localizedField) {
                $properties[$localizedField->getName()] = true;
            }
        }

        $properties['fieldName'] = true;
        $properties['object'] = true;
        $properties['type'] = true;

        unset($properties['localizedfields']);

        return new PropertyNameCollection(array_keys($properties));
    }
}
