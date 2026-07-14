<?php

namespace App\Filament\Resources\Places\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use Filament\Tables\Filters;

class PlacesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Columns\BadgeColumn::make('section')
                    ->label('Категория')
                    ->colors(['primary' => 'tourism', 'success' => 'active', 'warning' => 'gastronomy']),
                Columns\TextColumn::make('category.title')
                    ->label('Название категории')
                    ->sortable(),
                Columns\TextColumn::make('city.title')
                    ->label('Город')
                    ->sortable(),
                Columns\IconColumn::make('is_published')
                    ->label('Опубликовано')
                    ->boolean(),
                Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime(),
            ])
            ->filters([
                // Фильтры: по section, city, category, is_published.
                Filters\SelectFilter::make('section')
                    ->options([
                        'tourism' => 'Туризм',
                        'active' => 'Активный отдых',
                        'gastronomy' => 'Гастрономия',
                    ]),

            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
