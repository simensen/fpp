<?php

declare(strict_types=1);

namespace Fpp\Dumper;

use Fpp\Definition;

class AggregateChangedDumper implements Dumper
{
    public function dump(Definition $definition): string
    {
        $class = (new AggregateChangedClassDumper())->dump($definition);
        $functionalConstructor = (new FunctionalAggregateChangedConstructorDumper())->dump($definition);
        $functions = (new FunctionNamespaceDumper($functionalConstructor))->dump($definition);

        return $class . $functions;
    }
}
