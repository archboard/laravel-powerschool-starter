<?php

namespace App\SisProviders;

use App\Models\School;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;

interface SisProvider
{
    public function __construct(Tenant $tenant);

    public function configured(): bool;

    public function getAllSchools(): Collection;

    public function syncSchools(): Collection;

    public function syncSchool($sisId): School;

    public function fullSchoolSync($sisId): void;

    public function syncStudent($sisId): Student;

    public function syncUser($sisId): User;

    public function syncTeacher($sisId): User;
}
