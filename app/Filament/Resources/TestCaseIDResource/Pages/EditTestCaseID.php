<?php

namespace App\Filament\Resources\TestCaseIDResource\Pages;

use App\Filament\Resources\TestCaseIDResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestCaseID extends EditRecord
{
    protected static string $resource = TestCaseIDResource::class;

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
