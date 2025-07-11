<?php

namespace App\Filament\Resources\TestCaseIDResource\Pages;

use App\Filament\Resources\TestCaseIDResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestCaseIDS extends ListRecords
{
    protected static string $resource = TestCaseIDResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
