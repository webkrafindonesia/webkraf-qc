<?php

namespace App\Filament\Resources\TestCaseIDResource\Pages;

use App\Filament\Resources\TestCaseIDResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTestCaseID extends ViewRecord
{
    protected static string $resource = TestCaseIDResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
