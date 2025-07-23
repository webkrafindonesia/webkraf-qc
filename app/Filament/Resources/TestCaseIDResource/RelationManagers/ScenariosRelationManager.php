<?php

namespace App\Filament\Resources\TestCaseIDResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\TestCaseScenario;

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
            ->reorderable('sort')
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
                    ->modalWidth('sm')
                    ->mutateFormDataUsing(function (array $data) {
                        $scenario = TestCaseScenario::where('test_case_id_id',$this->ownerRecord->id)->orderBy('sort','desc')->first();
                        $data['sort'] = $scenario ? $scenario->sort + 1 : 1;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('sm'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Action::make('copy')
                    ->label('Copy')
                    ->icon('heroicon-m-document-duplicate')
                    ->color('gray')
                    ->action(function ($record) {
                        $new = $record->replicate(); // salin data
                        $new->save(); // simpan sebagai record baru
                    })
                    ->requiresConfirmation()
                    ->tooltip('Copy this row'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ])
                ->orderBy('sort', 'asc')
            );
    }
}
