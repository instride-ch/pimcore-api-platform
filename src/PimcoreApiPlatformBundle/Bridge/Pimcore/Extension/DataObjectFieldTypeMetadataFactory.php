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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension;

use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use Pimcore\Model\DataObject\ClassDefinition\Data;

final class DataObjectFieldTypeMetadataFactory
{
    protected $factories;

    public function __construct(iterable $factories)
    {
        $this->factories = $factories;
    }

    public function create($classDefinition, Data $data, PropertyMetadata $propertyMetadata): PropertyMetadata
    {
        foreach ($this->factories as $factory) {
            if (!$factory instanceof DataObjectFieldTypeMetadataFactoryInterface) {
                throw new \InvalidArgumentException('Expected type DataObjectFieldTypeMetadataFactoryInterface, but got '.get_class($factory));
            }

            if ($factory->supports($classDefinition, $data)) {
                $propertyMetadata = $factory->create($classDefinition, $data, $propertyMetadata);
            }
        }

        return $propertyMetadata;
    }
}
