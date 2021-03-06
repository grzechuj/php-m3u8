<?php

namespace Chrisyue\PhpM3u8\Transformer\Factory\Annotation;

use Chrisyue\PhpM3u8\Transformer\Factory\FactoryInterface;
use Chrisyue\PhpM3u8\Transformer\AttrListTransformer;
use Chrisyue\PhpM3u8\Transformer\KvTransformer;
use Chrisyue\PhpM3u8\DataAccessor\ObjectAccessor;
use Chrisyue\PhpM3u8\PropertyReader\PropertyReader;
use Chrisyue\PhpM3u8\Transformer\AttrTransformers;
use Chrisyue\PhpM3u8\PropertyReader\PropertyReaderAwareInterface;
use Chrisyue\PhpM3u8\PropertyReader\PropertyReaderInterface;
use Chrisyue\PhpM3u8\PropertyReader\PropertyReaderAwareTrait;
use Chrisyue\PhpM3u8\Transformer\AttrListReverser;
use Chrisyue\PhpM3u8\DataAccessor\Factory;
use Chrisyue\PhpM3u8\Transformer\AttrReversers;
use Chrisyue\PhpM3u8\Transformer\KvReverser;

/**
 * @Annotation
 */
class AttrList implements FactoryInterface, PropertyReaderAwareInterface
{
    use PropertyReaderAwareTrait;

    private $class;

    public function __construct(array $options)
    {
        $this->class = $options['class'];
    }

    public function createTransformer()
    {
        $attrTransformers = new AttrTransformers();
        foreach ($this->reader->read($this->class, FactoryInterface::class) as $key => $factory) {
            $attrTransformers->set($key, $factory->createTransformer());
        }

        return new AttrListTransformer(
            new KvTransformer(),
            $attrTransformers,
            new ObjectAccessor(new $this->class)
        );
    }

    public function createReverser()
    {
        $attrReversers = new AttrReversers();
        foreach ($this->reader->read($this->class, FactoryInterface::class) as $key => $factory) {
            $attrReversers->set($key, $factory->createReverser());
        }

        return new AttrListReverser(
            new KvReverser(),
            $attrReversers,
            new Factory()
        );
    }
}
