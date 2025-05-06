<?php

declare(strict_types=1);

namespace App\Domains\Authorization\DTOs;

final readonly class VerifyEmailDTO
{
    public function __construct(
        public int $id,
        public string $hash,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'], $data['hash'])) {
            throw new \InvalidArgumentException('Missing required fields: id or hash');
        }

        return new self(
            id: $data['id'],
            hash: $data['hash'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'hash' => $this->hash,
        ];
    }
}
