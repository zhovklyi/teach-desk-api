<?php

namespace App\Data\Common;

use Spatie\LaravelData\Data;

class APIResponseData extends Data
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $message = null,
        public ?int $code = 200,
    ) {}

    public static function success(mixed $data = null, ?string $message = null, ?int $code = 200): self
    {
        return new self(true, $data, $message, $code);
    }

    public static function error(string $message, int $code, mixed $data = null): self
    {
        return new self(false, $data, $message, $code);
    }
}
