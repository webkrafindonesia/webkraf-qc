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
                )
            ->columns([
                TextColumn::make('scenario_name')
                    ->searchable()
                    ->description(function($record){
                        $steps = $record->scenario_steps;

                        $data = collect($steps)->map(function ($item, $index) {
                            return ($index + 1) . '. ' . ($item['steps'] ?? '-');
                        })->implode("<br/>");

                        return new HtmlString($data);
                    })
                    ->html()
                    ->label('Scenario Name'),
                TextColumn::make('expected_result')
                    ->wrap(),
                TextInputColumn::make('actual_result'),
                SelectColumn::make('status')
                    ->options([
                        'Passed' => 'Passed',
                        'Failed' => 'Failed',
                        'Remark' => 'Remark'
                    ]),
                TextInputColumn::make('remarks'),
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
                ->titlePrefixedWithLabel(false),
            ])
            ->actions([
                Action::make('upload')
                    ->label('Upload Files')
                    ->modalCancelActionLabel('Tutup')
                    ->form([
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
                    ]),
            // Action::make('viewAttachment')
            //     ->label('Lihat Attachment')
            //     ->icon('heroicon-o-photo')
            //     ->modalHeading('Attachment')
            //     ->modalSubmitAction(false) // gak perlu tombol submit
            //     ->modalCancelActionLabel('Tutup')
            //     ->form([]) // tetap butuh ini biar bisa pakai modal
            //     ->modalContent(function ($record) {
            //         // Ambil semua media dari koleksi 'uploads'
            //         $attachments = $record->getMedia('attachments');

            //         // Return tampilan kustom pakai view() helper
            //         return view('filament.components.testing-attachments', [
            //             'attachments' => $attachments,
            //         ]);
            //     }),
        ])
        ->filters([
            SelectFilter::make('status')
                ->options([
                    'Passed' => 'Passed',
                    'Failed' => 'Failed',
                    'Remark' => 'Remark',
                ])
                // ->query(function(){
                //     //dd($this->test_case_id);
                //         TestCaseScenario::query()
                //         ->whereHas('testCaseId', function($query){
                //             $query->whereHas('category', function($query2){
                //                 $query2->where('test_case_id',1);
                //             });
                //         });
                //     }
                // )
                // ->query(function ($query, array $data) {
                //     dd($data, $query->toSql(), $query->getBindings());
                // })
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
