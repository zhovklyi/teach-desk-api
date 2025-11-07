<?php

namespace App\Data\Common;

use Spatie\LaravelData\Data;

class APIResponseData extends Data
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $message = null,
        public mixed $meta = null,
    ) {}

    public static function success(mixed $data = null, ?string $message = null, mixed $meta = null): self
    {
        return new self(true, $data, $message, $meta);
    }

    public static function error(string $message, mixed $meta = null, mixed $data = null): self
    {
        return new self(false, $data, $message, $meta);
    }
}
