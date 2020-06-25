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

use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use Pimcore\Model\Listing\AbstractListing;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\BatchListing;

final class PaginationExtension implements ListingResultCollectionExtensionInterface
{
    /**
     * @var Pagination
     */
    private $pagination;

    public function __construct(
        Pagination $pagination
    ) {
        $this->pagination = $pagination;
    }

    public function applyToList(
        AbstractListing $abstractListing,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        if (null === $pagination = $this->getPagination($abstractListing, $resourceClass, $operationName, $context)) {
            return;
        }

        [$offset, $limit] = $pagination;

        $abstractListing
            ->setLimit($limit)
            ->setOffset($offset);
    }

    public function supportsResult(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        if ($context['graphql_operation_name'] ?? false) {
            return $this->pagination->isGraphQlEnabled($resourceClass, $operationName, $context);
        }

        return $this->pagination->isEnabled($resourceClass, $operationName, $context);
    }

    public function getResult(
        AbstractListing $abstractListing,
        string $resourceClass = null,
        string $operationName = null,
        array $context = []
    ): iterable {
        return new BatchListing($abstractListing, 20);
    }

    private function getPagination(
        AbstractListing $listing,
        string $resourceClass,
        ?string $operationName,
        array $context
    ): ?array {
        $request = null;

        if (!$this->pagination->isEnabled($resourceClass, $operationName, $context)) {
            return null;
        }

        if (($context['graphql_operation_name'] ?? false) && !$this->pagination->isGraphQlEnabled($resourceClass,
                $operationName, $context)) {
            return null;
        }

        $context = $this->addCountToContext($listing, $context);

        return \array_slice($this->pagination->getPagination($resourceClass, $operationName, $context), 1);
    }

    private function addCountToContext(AbstractListing $listing, array $context): array
    {
        if (!($context['graphql_operation_name'] ?? false)) {
            return $context;
        }

        if (isset($context['filters']['last']) && !isset($context['filters']['before'])) {
            $context['count'] = $listing->getTotalCount();
        }

        return $context;
    }
}
