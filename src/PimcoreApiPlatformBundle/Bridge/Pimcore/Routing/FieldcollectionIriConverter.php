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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Routing;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Api\UrlGeneratorInterface;
use Pimcore\Model\DataObject\Fieldcollection;

class FieldcollectionIriConverter implements IriConverterInterface
{
    /**
     * @var IriConverterInterface
     */
    protected $inner;

    public function getItemFromIri(string $iri, array $context = [])
    {
        return $this->inner->getItemFromIri($iri, $context);
    }

    public function getIriFromItem($item, int $referenceType = UrlGeneratorInterface::ABS_PATH): string
    {
        if ($item instanceof Fieldcollection) {
            return '/null';
        }
        
        return $this->inner->getIriFromItem($item, $referenceType);
    }

    public function getIriFromResourceClass(
        string $resourceClass,
        int $referenceType = UrlGeneratorInterface::ABS_PATH
    ): string {
        return $this->inner->getIriFromResourceClass($resourceClass, $referenceType);
    }

    public function getItemIriFromResourceClass(
        string $resourceClass,
        array $identifiers,
        int $referenceType = UrlGeneratorInterface::ABS_PATH
    ): string {
        return $this->inner->getItemIriFromResourceClass($resourceClass, $identifiers, $referenceType);
    }

    public function getSubresourceIriFromResourceClass(
        string $resourceClass,
        array $identifiers,
        int $referenceType = UrlGeneratorInterface::ABS_PATH
    ): string {
        return $this->inner->getSubresourceIriFromResourceClass($resourceClass, $identifiers, $referenceType);
    }
}
