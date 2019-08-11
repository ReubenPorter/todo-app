<?php


namespace App\Serializer\Normalizer;


use App\Entity\TaskList;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TaskListNormalizer implements NormalizerInterface
{

    /**
     * @var Packages
     */
    private $packages;
    /**
     * @var ObjectNormalizer
     */
    private $objectNormalizer;

    public function __construct(ObjectNormalizer $objectNormalizer, Packages $packages)
    {
        $this->packages = $packages;
        $this->objectNormalizer = $objectNormalizer;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed $object Object to normalize
     * @param string $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool
     *
     * @throws InvalidArgumentException   Occurs when the object given is not an attempted type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     * @throws ExceptionInterface         Occurs for all the other cases of errors
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $object->setBackgroundPath(
            $this->packages->getUrl($object->getBackgroundPath(), 'backgrounds')
        );

        $context['ignored_attributes'] = ['user'];

        $data = $this->objectNormalizer->normalize($object, $format, $context);

        return $data;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof TaskList;
    }
}