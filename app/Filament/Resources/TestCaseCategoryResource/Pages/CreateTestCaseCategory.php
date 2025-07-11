<?php

namespace App\Filament\Resources\TestCaseCategoryResource\Pages;

use App\Filament\Resources\TestCaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTestCaseCategory extends CreateRecord
{
    protected static string $resource = TestCaseCategoryResource::class;
}
