<?php
namespace App\Saga\Interfaces;

interface SagaStep
{
    public function execute(array $data): bool;
    public function compensate(array $data): void;
}