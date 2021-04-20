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

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Pimcore\Model\Element\Note;
use Pimcore\Model\Element\Tag;

final class NoteDataPersister implements ContextAwareDataPersisterInterface
{
    protected $resourceMetadataFactory;

    public function __construct(ResourceMetadataFactoryInterface $resourceMetadataFactory)
    {
        $this->resourceMetadataFactory = $resourceMetadataFactory;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Note;
    }

    public function persist($data, array $context = [])
    {
        /**
         * @var Note $data
         */
        $data->save();
    }

    public function remove($data, array $context = [])
    {
        /**
         * @var Note $data
         */
        $data->delete();
    }
}
