<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestCaseResource\Pages;
use App\Filament\Resources\TestCaseResource\RelationManagers;
use App\Models\TestCase;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class TestCaseResource extends Resource
{
    protected static ?string $model = TestCase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Test Scenario';

    protected static ?int $navigationSort = 1;

    protected static bool $isLazy = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('System Information')
                    ->description('Informasi umum mengenai sistem yang akan diuji')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('system_name')
                            ->maxLength(255)
                            ->required()
                            ->default(null),
                        Forms\Components\TextInput::make('system_version')
                            ->helperText('Contoh: v1.0.0, v2.3.4')
                            ->maxLength(255)
                            ->required()
                            ->default(null),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('platform_version')
                            ->helperText('Contoh: Android 12, iOS 15, Google Chrome 90, Firefox 88')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('url')
                            ->helperText('URL atau endpoint sistem yang akan diuji')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make('Testing Information')
                    ->description('Keterangan umum untuk testing')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('company')
                            ->maxLength(255)
                            ->required()
                            ->default(null),
                        Forms\Components\Select::make('type')
                            ->options([
                                'SIT' => 'System Integration Testing (SIT)',
                                'UAT' => 'User Acceptance Testing (UAT)',
                            ])
                            ->searchable()
                            ->default('SIT')
                            ->required(),
                        Forms\Components\Repeater::make('testers')
                            ->relationship('testers')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->options(function(){
                                        $users = User::all()->pluck('name','id');
                                        return $users;
                                    })
                                    ->searchable()
                                    ->required(),
                            ])
                            ->columns(1),
                    ]),
                Section::make('Status')
                    ->description('Status testing')
                    ->aside()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'In Progress' => 'In Progress',
                                'Completed' => 'Completed',
                                'Archived' => 'Archived',
                            ])
                            ->searchable()
                            ->default('Draft')
                            ->required(),
                        Split::make([
                            Forms\Components\DatePicker::make('start_date'),
                            Forms\Components\DatePicker::make('end_date'),
                        ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn () => null)
            ->columns([
                Tables\Columns\TextColumn::make('system_name')
                    ->description(fn($record)=>$record->system_version)
                    ->searchable(),
                Tables\Columns\TextColumn::make('company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('testers.tester.name')
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('start_date')
                    ->formatStateUsing(fn($state) => convertDateID($state))
                    ->description(fn($record)=>'End: '.convertDateID($record->end_date))
                    //->date()
                    ->prefix('Start: ')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->visible(fn()=>auth()->user()->id == 1),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => auth()->user()->id == 1),
                Action::make('testing')
                   ->label('Start Testing')
                   ->url(fn ($record): string => TestCaseResource::getUrl('testing', ['record' => $record->id]))
                   ->openUrlInNewTab()
                   ->icon('heroicon-o-eye')
                   ->visible(fn($record) => in_array($record->status, ['In Progress', 'Completed'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestCases::route('/'),
            'create' => Pages\CreateTestCase::route('/create'),
            'view' => Pages\ViewTestCase::route('/{record}'),
            'edit' => Pages\EditTestCase::route('/{record}/edit'),
            'testing' => Pages\TestingPage::route('/{record}/testing'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->when(auth()->user()->id != 1, function($q){
                $q->whereHas('testers', function($q2){
                    $q2->where('user_id', auth()->user()->id);
                })
                ->whereIn('status',['In Progress', 'Completed']);
            })
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
