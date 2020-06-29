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
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Data\QuantityValue;
use Symfony\Component\PropertyInfo\Type;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\DataObjectFieldTypeMetadataFactoryInterface;

class QuantityTypeFactory implements DataObjectFieldTypeMetadataFactoryInterface
{
    public function supports($classDefinition, Data $fieldDefinition): bool
    {
        return $fieldDefinition instanceof Data\InputQuantityValue || $fieldDefinition instanceof Data\QuantityValue;
    }

    public function create(
        $classDefinition,
        Data $fieldDefinition,
        PropertyMetadata $propertyMetadata
    ): PropertyMetadata {
        $propertyMetadata = $propertyMetadata->withType(new Type(Type::BUILTIN_TYPE_OBJECT, true,
            QuantityValue::class));

        return $propertyMetadata;
    }
}
