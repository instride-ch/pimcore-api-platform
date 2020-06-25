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

use ApiPlatform\Core\DataProvider\PartialPaginatorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Pimcore\Model\Listing\AbstractListing;

abstract class AbstractPaginator implements \IteratorAggregate, PartialPaginatorInterface
{
    protected $listing;
    protected $iterator;
    protected $firstResult;
    protected $maxResults;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(AbstractListing $listing)
    {
        $this->listing = $listing;
        $this->maxResults = $listing->getTotalCount();
        $this->firstResult = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage(): float
    {
        if (0 >= $this->maxResults) {
            return 1.;
        }

        return floor($this->firstResult / $this->maxResults) + 1.;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsPerPage(): float
    {
        return (float) $this->maxResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return $this->iterator ?? $this->iterator = $this->listing;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }
}
