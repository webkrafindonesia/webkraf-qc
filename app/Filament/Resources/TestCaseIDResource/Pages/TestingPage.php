<?php

namespace App\Filament\Resources\TestCaseIDResource\Pages;

use App\Filament\Resources\TestCaseIDResource;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\DateRangeFilter;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Models\TestCaseScenario;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Actions\Action;

class TestingPage extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use ExposesTableToWidgets;

    protected static string $resource = TestCaseIDResource::class;

    protected static string $view = 'filament.resources.test-case-i-d-resource.pages.testing-page';

    public ?string $activeTab = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(TestCaseScenario::query()
                )
            ->columns([
                TextColumn::make('scenario_name')
                    ->searchable()
                    ->description(function($record){
                        $data = explode(',',$record->steps);
dd($record);
                        return collect($data)->map(function ($item, $index) {
                            $obj = json_decode($item);
                            return ($index + 1) . '. ' . ($obj->steps ?? '-');
                        })->implode("<br/>");
                    })
                    ->label('Scenario Name'),
                TextColumn::make('scenario_steps')
                    ->searchable()
                    ->label('Scenario Steps')
                    ->formatStateUsing(function ($state) {
                        $data = explode(',',$state);

                        return collect($data)->map(function ($item, $index) {
                            $obj = json_decode($item);
                            return ($index + 1) . '. ' . ($obj->steps ?? '-');
                        })->implode("<br/>");
                        
                    })
                    ->html()
                    ->wrap(),
                TextColumn::make('expected_result'),
                TextInputColumn::make('actual_result'),
                SelectColumn::make('status')
                    ->options([
                        'Passed' => 'Passed',
                        'Failed' => 'Failed',
                        'Remark' => 'Remark'
                    ]),
                TextInputColumn::make('remarks'),
            ])
            ->recordClasses(function ($record) {
                return match ($record->status) {
                    'Passed' => 'bg-danger-50',
                    'Failed' => 'bg-green-100',
                    'Remark' => 'bg-gray-200',
                    default => '',
                };
            })
            ->defaultGroup('testCaseId.id_name')
            ->groups([
                Group::make('testCaseId.id_name')
                ->titlePrefixedWithLabel(false),
            ])
            ->actions([
            Action::make('upload')
                ->label('Upload File')
                ->form([
                    SpatieMediaLibraryFileUpload::make('file')
                        ->label('Upload File')
                        ->disk('public')
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    $record->addMedia($data['file'])->toMediaCollection('attachments');
                })
        ]);
    }
}
