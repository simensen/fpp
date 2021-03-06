<?php

/**
 * This file is part of prolic/fpp.
 * (c) 2018-2019 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fpp\Builder;

use Fpp\Constructor;
use Fpp\Definition;
use Fpp\DefinitionCollection;
use Fpp\Deriving;

const buildArguments = '\Fpp\Builder\buildArguments';

function buildArguments(Definition $definition, ?Constructor $constructor, DefinitionCollection $collection, string $placeHolder): string
{
    if (null === $constructor) {
        return $placeHolder;
    }

    $argumentList = '';

    foreach ($constructor->arguments() as $argument) {
        if (null === $argument->type()) {
            $argumentList .= '$' . $argument->name();

            if (null !== $argument->defaultValue()) {
                $argumentList .= ' = ' . $argument->defaultValue();
            }

            $argumentList .= ', ';

            continue;
        }

        if ($argument->nullable()) {
            $argumentList .= '?';
        }

        if ($argument->isScalartypeHint() && 1 === \count($constructor->arguments())) {
            $argumentType = $argument->isList() ? $argument->type() . ' ...' : $argument->type() . ' ';
            $argumentList .= $argumentType . '$' . $argument->name();

            if (null !== $argument->defaultValue()) {
                $argumentList .= ' = ' . $argument->defaultValue();
            }

            $argumentList .= ', ';
            continue;
        } elseif ($argument->isScalartypeHint()) {
            $argumentType = $argument->isList() ? 'array' : $argument->type();
            $argumentList .= $argumentType . ' $' . $argument->name();

            if (null !== $argument->defaultValue()) {
                $argumentList .= ' = ' . $argument->defaultValue();
            }

            $argumentList .= ', ';
            continue;
        }

        $nsPosition = \strrpos($argument->type(), '\\');

        $namespace = \substr($argument->type(), 0, $nsPosition);
        $name = \substr($argument->type(), $nsPosition + 1);

        $type = $namespace === $definition->namespace()
            ? $name
            : '\\' . $argument->type();

        if ($argument->isList() && 1 === \count($constructor->arguments())) {
            $argumentList .= $type . ' ...$' . $argument->name() . ', ';
        } elseif ($argument->isList()) {
            $argumentList .= 'array $' . $argument->name() . ', ';
        } else {
            $argumentList .= $type . ' $' . $argument->name();

            if (null !== $argument->defaultValue()) {
                $argumentList .= ' = ' . $argument->defaultValue();
            }

            $argumentList .= ', ';
        }
    }

    foreach ($definition->derivings() as $deriving) {
        if ($deriving->equals(new Deriving\Exception())) {
            /** @var Deriving\Exception $deriving */
            $deriving = $deriving;
            $argumentList .= \sprintf('string $message = \'%s\', int $code = 0, \\Exception $previous = null, ', $deriving->defaultMessage());
            break;
        }
    }

    if ('' === $argumentList) {
        return $placeHolder;
    }

    return \substr($argumentList, 0, -2);
}
