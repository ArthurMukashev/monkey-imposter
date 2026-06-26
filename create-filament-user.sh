#!/bin/bash

set -e  # остановка при ошибке

EMAIL="admin@mail.ru"
PASSWORD="admin"   # если пароль слишком короткий, замените на что-то вроде "admin123"

echo "📦 Проверка существования пользователя с email: $EMAIL..."

# Проверяем, существует ли уже пользователь (используем Artisan tinker)
EXISTS=$(php artisan tinker --execute="echo App\Models\User::where('email', '$EMAIL')->exists() ? 'true' : 'false';" 2>/dev/null)

if [ "$EXISTS" = "true" ]; then
    echo "✅ Пользователь $EMAIL уже существует."
else
    echo "👤 Создаём пользователя $EMAIL..."
    # Создаём пользователя напрямую через tinker (обходит валидацию пароля)
    php artisan tinker --execute="
        App\Models\User::create([
            'name' => 'Admin',
            'email' => '$EMAIL',
            'password' => '$PASSWORD',
        ]);
    "
    echo "✅ Пользователь создан."
fi

echo "🎉 Готово."
