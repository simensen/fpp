<?php

declare(strict_types=1);

namespace Fpp\Deriving;

use Fpp\Constructor;
use Fpp\Deriving as FppDeriving;

class FromScalar implements FppDeriving
{
    const VALUE = 'FromScalar';

    public function forbidsDerivings(): array
    {
        return [
            AggregateChanged::VALUE,
            Command::VALUE,
            DomainEvent::VALUE,
            Enum::VALUE,
            Query::VALUE,
            Uuid::VALUE,
        ];
    }

    /**
     * @param Constructor[] $constructors
     * @return bool
     */
    public function fulfillsConstructorRequirements(array $constructors): bool
    {
        if (count($constructors) > 1) {
            return false;
        }

        foreach ($constructors as $constructor) {
            if (count($constructor->arguments()) > 1) {
                return false;
            }

            if (isset($constructor->arguments()[0])) {
                $argument = $constructor->arguments()[0];

                if (! $argument->isScalartypeHint()) {
                    return false;
                }
            } elseif (! in_array($constructor->name(), ['String', 'Int', 'Float', 'Bool'], true)) {
                return false;
            }
        }

        return true;
    }

    public function __toString(): string
    {
        return self::VALUE;
    }
}
