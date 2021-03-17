<?php

namespace App\SisProviders;

use App\Models\School;
use App\Models\Tenant;
use Illuminate\Support\Collection;

interface SisProvider
{
    public function __construct(Tenant $tenant);

    public function getAllSchools(): array;

    public function syncSchools(): Collection;

    public function getSchool($sisId);

    public function syncSchool($sisId): School;

    public function syncSchoolCourses($sisId);
}
