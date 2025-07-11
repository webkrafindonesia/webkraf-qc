<?php

namespace App\Filament\Resources\TestCaseCategoryResource\Pages;

use App\Filament\Resources\TestCaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestCaseCategory extends EditRecord
{
    protected static string $resource = TestCaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
