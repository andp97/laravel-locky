<?php

namespace Pavons\Locky\Exceptions;

use Exception;

class CouldNotAcquireLockException extends Exception
{
    public static function for(string $key, int $attempts): self
    {
        return new self(
            message: sprintf('Could not acquire lock \'%s\' after %d attempts.', $key, $attempts),
        );
    }
}
