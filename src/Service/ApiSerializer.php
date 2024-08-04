<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiSerializer
{
    private array $normalizers;
    /**
     * @var array|JsonEncoder[]
     */
    private array $encoders;

    public function __construct()
    {
        $phpDocExtractor = new PhpDocExtractor();
        $typeExtractor = new PropertyInfoExtractor(
            typeExtractors: [
                new ConstructorExtractor(
                    [
                        $phpDocExtractor,
                    ],
                ),
                $phpDocExtractor,
            ],
        );

        $this->normalizers = [
            new ObjectNormalizer(propertyTypeExtractor: $typeExtractor),
            new ArrayDenormalizer(),
        ];

        $this->encoders = [
            new JsonEncoder(),
        ];
    }

    public function deserialize(string $json, string $class): mixed
    {
        return (new Serializer($this->normalizers, $this->encoders))->deserialize($json, $class, 'json');
    }

    public function serialize(mixed $data): string
    {
        return (new Serializer($this->normalizers, $this->encoders))->serialize($data, 'json');
    }
}
