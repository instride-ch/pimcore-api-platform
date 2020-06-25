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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Pimcore\Model\Listing\AbstractListing;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\ListingCollectionExtensionInterface;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Extension\ListingResultCollectionExtensionInterface;

abstract class AbstractCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $collectionExtensions;

    /**
     * @param ListingCollectionExtensionInterface[] $collectionExtensions
     */
    public function __construct(iterable $collectionExtensions = [])
    {
        $this->collectionExtensions = $collectionExtensions;
    }

    protected function loadData(
        AbstractListing $list,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToList($list, $resourceClass, $operationName, $context);

            if ($extension instanceof ListingResultCollectionExtensionInterface && $extension->supportsResult($resourceClass,
                    $operationName, $context)) {
                return $extension->getResult($list, $resourceClass, $operationName, $context);
            }
        }

        return $list->getData();
    }
}
