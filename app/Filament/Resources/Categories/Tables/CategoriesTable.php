<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\{BulkActionGroup, DeleteAction, DeleteBulkAction, EditAction};
use Filament\Tables\{Columns, Filters, Table};

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('slug')->searchable(),
                Columns\TextColumn::make('section')
                    ->label('Категория')
                    ->badge()
                    ->colors([
                        'primary' => 'tourism',
                        'success' => 'active',
                        'warning' => 'gastronomy',
                    ]),
                Columns\TextColumn::make('sort_order')
                    ->label('Порядок сортировки')
                    ->sortable(),
            ])
            ->filters([
                Filters\SelectFilter::make('section')
                    ->options([
                        'tourism' => 'Туризм',
                        'active' => 'Активный отдых',
                        'gastronomy' => 'Гастрономия',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
