<?php

namespace App\Filament\Resources\TestCaseResource\Pages;

use App\Filament\Resources\TestCaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTestCases extends ListRecords
{
    protected static string $resource = TestCaseResource::class;
    protected static ?string $title = 'Test Scenario';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn()=>auth()->user()->id == 1),
        ];
    }
    
    public function getBreadcrumbs(): array
    {
        return [];
    }
}
