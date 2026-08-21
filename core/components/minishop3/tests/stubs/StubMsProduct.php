<?php

declare(strict_types=1);

namespace MiniShop3\Tests\Stubs;

/**
 * Minimal msProduct stand-in for scope / controller smoke tests.
 */
class StubMsProduct
{
    /** @var array<string, mixed> */
    private array $data;

    /** @var array<string, bool>|null null = allow all policies */
    private ?array $policies;

    /**
     * @param array<string, mixed> $data
     * @param array<string, bool>|null $policies null allows every checkPolicy(); map denies/allows per policy
     */
    public function __construct(array $data, ?array $policies = null)
    {
        $this->data = $data;
        $this->policies = $policies;
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function save(): bool
    {
        return true;
    }

    public function checkPolicy(string $policy): bool
    {
        if ($this->policies === null) {
            return true;
        }

        return !empty($this->policies[$policy]);
    }
}
