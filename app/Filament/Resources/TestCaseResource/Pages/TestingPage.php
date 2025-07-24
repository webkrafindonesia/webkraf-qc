<?php

namespace App\Filament\Resources\TestCaseResource\Pages;

use App\Filament\Resources\TestCaseResource;
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
use Illuminate\Support\HtmlString;
use Illuminate\Http\Request;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;
use Filament\Forms;
use Filament\Forms\Components\Split;

class TestingPage extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use ExposesTableToWidgets;

    protected static string $resource = TestCaseResource::class;

    protected ?string $maxContentWidth = 'full';

    protected static string $view = 'filament.resources.test-case-i-d-resource.pages.testing-page';

    public ?string $activeTab = null;

    public $test_case_id;

    public function mount(Request $request)
    {
        $this->test_case_id = $request->route('record');
    }

    public function table(Table $table): Table
    {
        $testCaseId = $this->test_case_id;

        return $table
            ->query(TestCaseScenario::query()
                    ->whereHas('testCaseId', function($query) use($testCaseId){
                        $query->whereHas('category', function($query2) use($testCaseId){
                            $query2->where('test_case_id',$testCaseId);
                        });
                    })
                    ->orderBy('sort', 'asc')
                )
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->rowIndex(),
                TextColumn::make('scenario_name')
                    ->searchable()
                    ->description(function($record){
                        $steps = $record->scenario_steps;

                        $data = collect($steps)->map(function ($item, $index) {
                            return ($index + 1) . '. ' . ($item['steps'] ?? '-');
                        })->implode("<br/>");

                        $return = '<strong>Expected result:</strong><br/>'.$record->expected_result.'<br/><br/><strong>Steps:</strong><br/>' . $data;

                        return new HtmlString($return);
                    })
                    ->weight(FontWeight::Bold)
                    ->html()
                    ->wrap()
                    ->label('Scenario'),
                TextColumn::make('actual_result')
                    ->label('Actual Result')
                    ->formatStateUsing(fn ($state) => new HtmlString(nl2br($state)) ?: '')
                    ->description(fn ($record) => ($record->remarks) ? new HtmlString('Remarks: '.nl2br($record->remarks)) : '')
                    ->wrap()
                    ->html(),
                TextColumn::make('status')
                    ->label('Status')
                    ->color(fn ($record) => match ($record->status) {
                        'Passed' => 'success',
                        'Failed' => 'danger',
                        'Remark' => 'warning',
                        default => 'secondary',
                    }),
            ])
            // ->recordClasses(function ($record) {
            //     return match ($record->status) {
            //         'Passed' => 'bg-danger-50',
            //         'Failed' => 'bg-primary-50',
            //         'Remark' => 'bg-gray-200',
            //         default => '',
            //     };
            // })
            ->defaultGroup('testCaseId.id_name')
            ->groups([
                Group::make('testCaseId.id_name')
                    ->getDescriptionFromRecordUsing(fn ($record): string => $record->testCaseId->category->category_name)
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
            ->actions([
                Action::make('result')
                    ->label('Result')
                    ->modalCancelActionLabel('Tutup')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Passed' => 'Passed',
                                'Failed' => 'Failed',
                                'Remark' => 'Remark'
                            ])
                            ->required()
                            ->default(fn($record) => $record->status),
                        Split::make([
                            Forms\Components\Textarea::make('actual_result')
                                ->required()
                                ->default(fn($record) => $record->actual_result),
                            Forms\Components\Textarea::make('remarks')
                                ->default(fn($record) => $record->remarks),
                        ]),
                        SpatieMediaLibraryFileUpload::make('file')
                            ->label('Upload Files')
                            ->disk('minio')
                            ->image()
                            ->multiple(true)
                            ->openable()
                            ->collection('attachments')
                            ->directory('attachments')
                            ->panelLayout('grid')
                            ->visibility('public')
                            ->saveRelationshipsUsing(fn ($component) => $component->saveUploadedFiles())
                            ->default(fn ($record) => $record->getMedia('attachments')->pluck('uuid')->toArray()),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'actual_result' => $data['actual_result'],
                            'status' => $data['status'],
                            'remarks' => $data['remarks'],
                        ]);
                    }),
        ])
        ->filters([
            SelectFilter::make('status')
                ->options([
                    'Passed' => 'Passed',
                    'Failed' => 'Failed',
                    'Remark' => 'Remark',
                ])
        ]);
    }

    public function getHeaderWidgets(): array
    {
        return [
            TestCaseResource\Widgets\StatsOverview::make([
                'test_case_id' => $this->test_case_id,
            ]),
        ];
    }
}
