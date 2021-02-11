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
use Pimcore\Model\Document;
use Pimcore\Model\DataObject;
use Pimcore\Model\Asset;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ElementDataPersister implements ContextAwareDataPersisterInterface
{
    protected $resourceMetadataFactory;

    public function __construct(ResourceMetadataFactoryInterface $resourceMetadataFactory)
    {
        $this->resourceMetadataFactory = $resourceMetadataFactory;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ElementInterface;
    }

    public function persist($data, array $context = [])
    {
        $resourceMetadata = $this->resourceMetadataFactory->create($context['resource_class']);

        $operationName = $context['collection_operation_name'] ?? null;
        if (null !== $operationName) {
            $pimcoreAttributes = $resourceMetadata->getCollectionOperationAttribute($operationName, 'pimcore', [], true);
            $path = $pimcoreAttributes['path'] ?? null;
            $key = $pimcoreAttributes['key'] ?? null;
            $published = $pimcoreAttributes['published'] ?? null;

            if ($path) {
                if ($data instanceof Document) {
                    $data->setParent(Document\Service::createFolderByPath($path));
                }
                elseif ($data instanceof DataObject\AbstractObject) {
                    $data->setParent(DataObject\Service::createFolderByPath($path));
                }
                if ($data instanceof Asset) {
                    $data->setParent(Asset\Service::createFolderByPath($path));
                }
            }

            if ($key) {
                $expressionService = new ExpressionLanguage();

                $data->setKey($expressionService->evaluate($key, [
                    'element' => $data,
                    'pimcoreAttributes' => $pimcoreAttributes,
                    'context' => $context
                ]));
            }

            if (isset($published)) {
                $expressionService = new ExpressionLanguage();

                $data->setPublished($expressionService->evaluate($published, [
                    'element' => $data,
                    'pimcoreAttributes' => $pimcoreAttributes,
                    'context' => $context
                ]));
            }
        }

        if ($data->getParent() && $data->getKey()) {
            if ($data instanceof DataObject\AbstractObject) {
                $data->setKey(DataObject\Service::getUniqueKey($data));
            }
            elseif ($data instanceof Asset) {
                $data->setKey(Asset\Service::getUniqueKey($data));
            }
            elseif ($data instanceof Document) {
                $data->setKey(Document\Service::getUniqueKey($data));
            }
        }

        /**
         * @var ElementInterface $data
         */
        $data->save();
    }

    public function remove($data, array $context = [])
    {
        /**
         * @var ElementInterface $data
         */
        $data->delete();
    }
}
