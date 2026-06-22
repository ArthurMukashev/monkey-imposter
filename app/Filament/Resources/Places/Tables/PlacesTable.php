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
                Columns\TextColumn::make('title')->searchable()->sortable(),
                Columns\BadgeColumn::make('section')
                    ->colors(['primary' => 'tourism', 'success' => 'active', 'warning' => 'gastronomy']),
                Columns\TextColumn::make('category.title')->sortable(),
                Columns\TextColumn::make('city.title')->sortable(),
                Columns\IconColumn::make('is_published')->boolean(),
                Columns\TextColumn::make('created_at')->dateTime(),
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
