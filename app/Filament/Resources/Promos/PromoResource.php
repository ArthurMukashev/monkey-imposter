<?php

namespace App\Filament\Resources\Promos;

use App\Filament\Resources\Promos\Pages\CreatePromo;
use App\Filament\Resources\Promos\Pages\EditPromo;
use App\Filament\Resources\Promos\Pages\ListPromos;
use App\Filament\Resources\Promos\Schemas\PromoForm;
use App\Filament\Resources\Promos\Tables\PromosTable;
use App\Models\Image;
use App\Models\Promo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Storage;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;
    protected static ?string $pluralModelLabel = 'Промо';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return Heroicon::Megaphone;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PromoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromos::route('/'),
            'create' => CreatePromo::route('/create'),
            'edit' => EditPromo::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return self::handleImageUpload($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return self::handleImageUpload($data);
    }

    private static function handleImageUpload(array $data): array
    {
        if (!empty($data['image_file']) && is_string($data['image_file'])) {
            // image_file уже содержит путь (например, promos/filename.webp)
            $path = $data['image_file'];
            $url = Storage::disk('public')->url($path);

            // Создаём запись изображения (пока без привязки к промо)
            $image = Image::create([
                'url' => $url,
                'alt' => $data['image_alt'] ?? '',
                'title' => $data['image_title'] ?? '',
                'is_cover' => true,
            ]);

            // Сохраняем ID изображения, чтобы привязать после создания промо
            $data['image_id'] = $image->id;
        }

        // Удаляем временные поля
        unset($data['image_file'], $data['image_alt'], $data['image_title']);

        return $data;
    }

    public static function afterCreate($record): void
    {
        self::attachImage($record);
    }

    public static function afterSave($record): void
    {
        self::attachImage($record);
    }

    private static function attachImage($record): void
    {
        if (isset($record->image_id)) {
            $image = Image::find($record->image_id);
            if ($image) {
                $image->imageable_id = $record->id;
                $image->imageable_type = get_class($record);
                $image->save();
            }
            unset($record->image_id);
        }
    }
}
