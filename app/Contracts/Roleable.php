<?php

namespace App\Contracts;

interface Roleable
{
    public function getPermissions(): array;
    public function getRoleType(): string;
}