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

namespace Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\GraphQl;

use ApiPlatform\Core\GraphQl\Type\TypeConverterInterface;
use ApiPlatform\Core\GraphQl\Type\TypesContainerInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use GraphQL\Type\Definition\Type as GraphQLType;
use Symfony\Component\PropertyInfo\Type;
use Wvision\Bundle\PimcoreApiPlatformBundle\Bridge\Pimcore\UnionType;

class TypeConverter implements TypeConverterInterface
{
    protected $resourceClassResolver;
    protected $typesContainer;
    protected $inner;

    public function __construct(
        TypeConverterInterface $inner,
        TypesContainerInterface $typesContainer,
        ResourceMetadataFactoryInterface $resourceClassResolver
    )
    {
        $this->inner = $inner;
        $this->typesContainer = $typesContainer;
        $this->resourceClassResolver = $resourceClassResolver;
    }

    public function convertType(
        Type $type,
        bool $input,
        ?string $queryName,
        ?string $mutationName,
        string $resourceClass,
        string $rootResource,
        ?string $property,
        int $depth
    ) {
        if ($type instanceof UnionType) {
            if ($mutationName) {
                return null;
            }

            $meta = $this->resourceClassResolver->create($rootResource);
            $name = $meta->getShortName() . '_' . $property . '_elements';

            if ($this->typesContainer->has($name)) {
                return $this->typesContainer->get($name);
            }

            $types = [];
            $typeCache = [];

            foreach ($type->getTypes() as $subType) {
                $convertedType = $this->inner->convertType(
                    $subType,
                    $input,
                    $queryName,
                    $mutationName,
                    $resourceClass,
                    $rootResource,
                    $property,
                    $depth
                );

                if ($convertedType) {
                    $types[] = $convertedType;
                    $typeCache[$subType->getClassName()] = $convertedType;
                }
            }

            //$containerType = $this->inner->convertType($type, $input, $queryName, $mutationName, $resourceClass, $rootResource, $property, $depth);

            $config = [
                'name' => $name,
                'types' => $types,
                'resolveType' => static function ($value) use ($typeCache) {
                    if (!isset($value['#itemResourceClass'])) {
                        return null;
                    }

                    if (!isset($typeCache[$value['#itemResourceClass']])) {
                        return null;
                    }

                    return $typeCache[$value['#itemResourceClass']];
                },
            ];
            $unionType = new \GraphQL\Type\Definition\UnionType($config);

            $this->typesContainer->set($name, $unionType);

            return \GraphQL\Type\Definition\Type::listOf($unionType);
        }

        return $this->inner->convertType(
            $type,
            $input,
            $queryName,
            $mutationName,
            $resourceClass,
            $rootResource,
            $property,
            $depth
        );
    }

    public function resolveType(string $type): ?GraphQLType
    {
        return $this->inner->resolveType($type);
    }
}
