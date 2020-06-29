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
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\DataObjectFieldTypeMetadataFactory;

abstract class AbstractDefinitionPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    private $pimcoreTypeFactory;

    public function __construct(
        DataObjectFieldTypeMetadataFactory $pimcoreTypeFactory
    ) {
        $this->pimcoreTypeFactory = $pimcoreTypeFactory;
    }

    protected function processPimcoreProperty(
        $definition,
        Data $fieldDefinition,
        PropertyMetadata $propertyMetadata
    ): PropertyMetadata {
        $propertyMetadata = $this->pimcoreTypeFactory->create($definition, $fieldDefinition, $propertyMetadata);
        $propertyMetadata = $propertyMetadata->withDescription($fieldDefinition->getTitle());

        return $propertyMetadata;
    }
}
