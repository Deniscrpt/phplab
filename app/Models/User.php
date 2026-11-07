<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ExportableInterface;
use App\Traits\JsonExportTrait;

/**
 * Користувач блогу (роль: reader, author або admin).
 */
class User extends AbstractEntity implements ExportableInterface
{
    use JsonExportTrait;

    public function __construct(
        int $id,
        string $createdAt,
        protected string $name,
        protected string $email,
        protected string $role = 'reader',
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return sprintf(
            'Користувач #%d %s <%s>, роль: %s, зареєстровано: %s',
            $this->id,
            $this->name,
            $this->email,
            $this->role,
            $this->createdAt
        );
    }
}
