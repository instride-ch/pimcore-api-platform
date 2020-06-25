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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\Normalizer;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class ConcreteNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'CONCRETE_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';
    private $resourceMetadataFactory;

    public function __construct(ResourceMetadataFactoryInterface $resourceMetadataFactory)
    {
        $this->resourceMetadataFactory = $resourceMetadataFactory;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        if (get_class($object) !== $context['resource_class']) {
            $resourceMetadata = $this->resourceMetadataFactory->create(get_class($object));
            $groupsOfSubResource = $resourceMetadata->getAttribute( 'normalization_context', ['groups' => []])['groups'];

            $context[AbstractNormalizer::GROUPS] = $groupsOfSubResource;
        }

        $context[self::ALREADY_CALLED] = true;

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null, array $context = [])
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        if (!is_object($data)) {
            return false;
        }

        return get_class($data) !== $context['resource_class'] && $context['resource_class'] === Concrete::class;
    }
}
