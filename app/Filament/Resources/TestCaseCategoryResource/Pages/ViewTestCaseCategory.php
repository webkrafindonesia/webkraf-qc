<?php

namespace App\Filament\Resources\TestCaseCategoryResource\Pages;

use App\Filament\Resources\TestCaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTestCaseCategory extends ViewRecord
{
    protected static string $resource = TestCaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
