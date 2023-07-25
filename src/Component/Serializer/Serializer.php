<?php

declare(strict_types=1);

namespace App\Component\Serializer;

use App\Component\Serializer\Exception\SerializerException;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer as SerializerComponent;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class Serializer
{
    protected SerializerInterface $serializer;

    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        
        $this->serializer = new SerializerComponent(
            [
                new DateTimeNormalizer(),
                new PropertyNormalizer($classMetadataFactory, propertyTypeExtractor: new ReflectionExtractor())
            ],
            [new JsonEncoder()]
        );
    }

    /** @param array<string, mixed> $context */
    public function serialize(mixed $data, array $context = []): string
    {
        return $this->serializer->serialize($data, 'json', $this->getContext($context));
    }

    /**
     * @param array<string, mixed> $context
     * @psalm-suppress MissingReturnType
     */
    public function deserialize(string $data, string $class, array $context = []) /* @phpstan-ignore-line */ // phpcs:ignore
    {
        try {
            $object = $this->serializer->deserialize($data, $class, 'json', $this->getContext($context));
        } catch (Throwable $e) {
            throw new SerializerException($e->getMessage());
        }

        return $object;
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    private function getContext(array $context = []): array
    {
        return (new ObjectNormalizerContextBuilder())
            ->withContext($context)
            ->withContext([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])
            ->toArray();
    }
}
