<?php

namespace App\Filament\Resources\TestCaseIDResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScenariosRelationManager extends RelationManager
{
    protected static string $relationship = 'scenarios';

    protected static bool $isLazy = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('scenario_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('expected_result')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('scenario_steps')
                    ->schema([
                        Forms\Components\TextInput::make('steps')
                            ->required()
                            ->maxLength(255)
                    ]),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('scenario_name')
            ->columns([
                Tables\Columns\TextColumn::make('scenario_name')
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('expected_result')
                    ->wrap()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalWidth('sm'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('sm'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
