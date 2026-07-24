<?php

namespace App\Filament\Resources\Promos\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PromosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ID (можно скрыть)
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                // Изображение (миниатюра)
                ImageColumn::make('image.url')
                    ->label('Изображение')
                    ->rounded()
                    ->size(60)
                    ->toggleable(),

                // Название
                TextColumn::make('title')
                    ->label('Название')
                    ->sortable()
                    ->searchable(),

                // Тизер (можно обрезать)
                TextColumn::make('teaser')
                    ->label('Тизер')
                    ->limit(50)
                    ->toggleable(),

                // Placement
                BadgeColumn::make('placement')
                    ->label('Место')
                    ->colors([
                        'primary' => 'home',
                        'success' => 'section',
                        'warning' => 'place-details',
                        'danger' => 'kiosk-home',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'home' => 'Главная',
                        'section' => 'Раздел',
                        'place-details' => 'Детальная',
                        'kiosk-home' => 'Kiosk',
                        default => $state,
                    })
                    ->sortable(),

                // Раздел (только для placement=section)
                TextColumn::make('section')
                    ->label('Раздел')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'tourism' => 'Туризм',
                        'active' => 'Активный отдых',
                        'gastronomy' => 'Гастрономия',
                        default => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                // Приоритет
                TextColumn::make('priority')
                    ->label('Приоритет')
                    ->sortable()
                    ->numeric(),

                // Активность (даты)
                TextColumn::make('active_from')
                    ->label('Активно с')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('active_until')
                    ->label('Активно до')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),

                // Таргет
                TextColumn::make('target_type')
                    ->label('Тип цели')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'place' => 'Объект',
                        'section' => 'Раздел',
                        'external' => 'Внешняя ссылка',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'place' => 'success',
                        'section' => 'info',
                        'external' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),

                TextColumn::make('target_slug')
                    ->label('Слаг цели')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('target_url')
                    ->label('Внешняя ссылка')
                    ->url(fn($record) => $record->target_url)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Дата создания
                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Дата обновления
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Фильтр по placement
                SelectFilter::make('placement')
                    ->label('Место показа')
                    ->options([
                        'home' => 'Главная',
                        'section' => 'Страница раздела',
                        'place-details' => 'Детальная объекта',
                        'kiosk-home' => 'Kiosk старт',
                    ]),

                // Фильтр по разделу (появляется, если placement=section, но можно просто фильтровать все)
                SelectFilter::make('section')
                    ->label('Раздел')
                    ->options([
                        'tourism' => 'Туризм',
                        'active' => 'Активный отдых',
                        'gastronomy' => 'Гастрономия',
                    ])
                    ->indicateUsing(fn($state) => match ($state) {
                        'tourism' => 'Туризм',
                        'active' => 'Активный отдых',
                        'gastronomy' => 'Гастрономия',
                        default => null,
                    }),

                // Фильтр по активности (активные сейчас)
                Filter::make('active')
                    ->label('Активные сейчас')
                    ->query(fn($query) => $query->active())
                    ->toggle(),

                // Фильтр по дате начала (можно использовать DateFilter, но оставим простой)
                Tables\Filters\Filter::make('active_from')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('active_from_from')
                            ->label('Активно с (от)'),
                        \Filament\Forms\Components\DatePicker::make('active_from_to')
                            ->label('Активно с (до)'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['active_from_from'], fn($q, $date) => $q->whereDate('active_from', '>=', $date))
                            ->when($data['active_from_to'], fn($q, $date) => $q->whereDate('active_from', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['active_from_from'] ?? null) {
                            $indicators['active_from_from'] = 'Активно с от: ' . $data['active_from_from'];
                        }
                        if ($data['active_from_to'] ?? null) {
                            $indicators['active_from_to'] = 'Активно с до: ' . $data['active_from_to'];
                        }
                        return $indicators;
                    }),

                // Фильтр по типу цели
                SelectFilter::make('target_type')
                    ->label('Тип цели')
                    ->options([
                        'place' => 'Объект',
                        'section' => 'Раздел',
                        'external' => 'Внешняя ссылка',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make()
            ])
            ->defaultSort('priority', 'desc')
            ->poll('30s') // автообновление каждые 30 секунд (опционально)
            ->striped();
    }
}
