<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestCaseIDResource\Pages;
use App\Filament\Resources\TestCaseIDResource\RelationManagers;
use App\Models\TestCaseID;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TestCaseIDResource extends Resource
{
    protected static ?string $model = TestCaseID::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 3;

    protected static bool $isLazy = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('test_case')
                    ->label('Test Case')
                    ->content(fn (TestCaseID $record): string => $record->category->testCase->system_name.' ('.$record->category->testCase->system_version.')'),
                Forms\Components\Placeholder::make('test_case_type')
                    ->label('Type')
                    ->content(fn (TestCaseID $record): string => $record->category->testCase->type),
                Forms\Components\TextInput::make('id_name')
                    ->label('Test Case ID')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.testCase.system_name')
                    ->description(function($record) {
                        return ($record->category->testCase) ? $record->category->testCase->system_version.' ['.$record->category->testCase->type.']' : '';
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.category_name')
                    ->label('Test Case Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_name')
                    ->label('Test Case ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            RelationManagers\ScenariosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestCaseIDS::route('/'),
            'create' => Pages\CreateTestCaseID::route('/create'),
            'view' => Pages\ViewTestCaseID::route('/{record}'),
            'edit' => Pages\EditTestCaseID::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->whereHas('category', function (Builder $query) {
                $query->whereHas('testCase');
            });
    }

    public static function canAccess(): bool
    {
        return auth()->user()->id === 1;
    }
}
