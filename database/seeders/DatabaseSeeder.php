<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\CuisineType;
use App\Models\Place;
use App\Models\Promo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mail.ru'],
            ['name' => 'Admin', 'password' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password']
        );

        $cities = collect([
            ['slug' => 'orenburg', 'title' => 'Оренбург', 'sort_order' => 0],
            ['slug' => 'orsk', 'title' => 'Орск', 'sort_order' => 1],
            ['slug' => 'buzuluk', 'title' => 'Бузулук', 'sort_order' => 2],
            ['slug' => 'sol-iletsk', 'title' => 'Соль-Илецк', 'sort_order' => 3],
            ['slug' => 'buguruslan', 'title' => 'Бугуруслан', 'sort_order' => 4],
        ])->mapWithKeys(fn (array $city) => [
            $city['slug'] => City::updateOrCreate(['slug' => $city['slug']], $city),
        ]);

        $categories = collect([
            ['slug' => 'istoricheskie-mesta', 'section' => 'tourism', 'title' => 'Исторические места', 'short_description' => 'Архитектура, музеи и городские символы Оренбуржья.', 'sort_order' => 0],
            ['slug' => 'prirodnye-marshruty', 'section' => 'tourism', 'title' => 'Природные маршруты', 'short_description' => 'Степи, боры, озёра и соляные ландшафты.', 'sort_order' => 1],
            ['slug' => 'muzei-i-kultura', 'section' => 'tourism', 'title' => 'Музеи и культура', 'short_description' => 'Экспозиции, народные промыслы и культурные центры.', 'sort_order' => 2],
            ['slug' => 'koncerty-i-spektakli', 'section' => 'active', 'title' => 'Концерты и спектакли', 'short_description' => 'Сцены, филармония и вечерние события.', 'sort_order' => 0],
            ['slug' => 'sport-i-aktivnyy-otdyh', 'section' => 'active', 'title' => 'Спорт и активный отдых', 'short_description' => 'Катки, веломаршруты, парки и семейные активности.', 'sort_order' => 1],
            ['slug' => 'festivali-i-yarmarki', 'section' => 'active', 'title' => 'Фестивали и ярмарки', 'short_description' => 'Городские праздники, маркеты и сезонные программы.', 'sort_order' => 2],
            ['slug' => 'restorany', 'section' => 'gastronomy', 'title' => 'Рестораны', 'short_description' => 'Места для ужина, деловой встречи и знакомства с локальной кухней.', 'sort_order' => 0],
            ['slug' => 'kafe-i-kofeyni', 'section' => 'gastronomy', 'title' => 'Кафе и кофейни', 'short_description' => 'Завтраки, кофе, десерты и спокойные встречи.', 'sort_order' => 1],
            ['slug' => 'lokalnaya-kuhnya', 'section' => 'gastronomy', 'title' => 'Локальная кухня', 'short_description' => 'Оренбургские продукты, пуховый платок как сувенир и степные вкусы.', 'sort_order' => 2],
        ])->mapWithKeys(fn (array $category) => [
            $category['slug'] => Category::updateOrCreate(['slug' => $category['slug']], $category),
        ]);

        $tags = collect([
            ['slug' => 's-semey', 'title' => 'С семьёй', 'color' => '#4F7CAC'],
            ['slug' => 'besplatno', 'title' => 'Бесплатно', 'color' => '#5C946E'],
            ['slug' => 'na-vyhodnye', 'title' => 'На выходные', 'color' => '#D99152'],
            ['slug' => 'dlya-detey', 'title' => 'Для детей', 'color' => '#B56576'],
            ['slug' => 'na-prirode', 'title' => 'На природе', 'color' => '#6A994E'],
            ['slug' => 'vecherniy', 'title' => 'Вечерний', 'color' => '#5E548E'],
            ['slug' => 'v-centre', 'title' => 'В центре', 'color' => '#8D6A9F'],
            ['slug' => 'lokalnoe', 'title' => 'Локальное', 'color' => '#BC6C25'],
        ])->mapWithKeys(fn (array $tag) => [
            $tag['slug'] => Tag::updateOrCreate(['slug' => $tag['slug']], $tag),
        ]);

        $cuisines = collect([
            ['slug' => 'mestnaya-kuhnya', 'title' => 'Местная кухня'],
            ['slug' => 'evropeyskaya', 'title' => 'Европейская'],
            ['slug' => 'kavkazskaya', 'title' => 'Кавказская'],
            ['slug' => 'aziatskaya', 'title' => 'Азиатская'],
            ['slug' => 'avtorskaya', 'title' => 'Авторская'],
            ['slug' => 'kofe-i-deserty', 'title' => 'Кофе и десерты'],
        ])->mapWithKeys(fn (array $cuisine) => [
            $cuisine['slug'] => CuisineType::updateOrCreate(['slug' => $cuisine['slug']], $cuisine),
        ]);

        $places = [
            ['slug' => 'nacionalnaya-derevnya', 'section' => 'tourism', 'title' => 'Национальная деревня', 'category' => 'muzei-i-kultura', 'city' => 'orenburg', 'lat' => 51.7682, 'lng' => 55.0969, 'address' => 'ул. Алтайская, 3', 'hours' => 'Ежедневно 10:00–20:00', 'tags' => ['s-semey', 'besplatno', 'v-centre'], 'short' => 'Культурный комплекс с подворьями народов Оренбуржья.', 'image' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'orenburgskaya-naberezhnaya', 'section' => 'tourism', 'title' => 'Оренбургская набережная', 'category' => 'istoricheskie-mesta', 'city' => 'orenburg', 'lat' => 51.7727, 'lng' => 55.0901, 'address' => 'Набережная реки Урал', 'hours' => 'Круглосуточно', 'tags' => ['besplatno', 's-semey', 'v-centre'], 'short' => 'Главная прогулочная зона у Урала с видом на пешеходный мост.', 'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'peshehodnyy-most-evropa-aziya', 'section' => 'tourism', 'title' => 'Пешеходный мост Европа — Азия', 'category' => 'istoricheskie-mesta', 'city' => 'orenburg', 'lat' => 51.7732, 'lng' => 55.0884, 'address' => 'река Урал', 'hours' => 'Круглосуточно', 'tags' => ['besplatno', 'v-centre'], 'short' => 'Символическая точка перехода между Европой и Азией.', 'image' => 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'orenburgskiy-muzey-izobrazitelnyh-iskusstv', 'section' => 'tourism', 'title' => 'Музей изобразительных искусств', 'category' => 'muzei-i-kultura', 'city' => 'orenburg', 'lat' => 51.7669, 'lng' => 55.1005, 'address' => 'ул. Каширина, 29', 'hours' => 'Вт–Вс 10:00–18:00', 'tags' => ['v-centre', 'dlya-detey'], 'short' => 'Коллекция живописи, графики и оренбургского пухового платка.', 'image' => 'https://images.unsplash.com/photo-1564399579883-451a5d44ec08?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'buzulukskiy-bor', 'section' => 'tourism', 'title' => 'Бузулукский бор', 'category' => 'prirodnye-marshruty', 'city' => 'buzuluk', 'lat' => 53.0124, 'lng' => 52.1047, 'address' => 'Национальный парк Бузулукский бор', 'hours' => 'По режиму национального парка', 'tags' => ['na-prirode', 'na-vyhodnye', 's-semey'], 'short' => 'Сосновый бор, экотропы и маршруты выходного дня.', 'image' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'sol-ileckie-ozera', 'section' => 'tourism', 'title' => 'Соль-Илецкие озёра', 'category' => 'prirodnye-marshruty', 'city' => 'sol-iletsk', 'lat' => 51.1631, 'lng' => 54.9918, 'address' => 'курортная зона Соль-Илецка', 'hours' => 'Сезонно', 'tags' => ['na-prirode', 'na-vyhodnye'], 'short' => 'Солёные озёра и летний курорт на юге области.', 'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'orenburgskaya-filarmoniya', 'section' => 'active', 'title' => 'Концерт в Оренбургской филармонии', 'category' => 'koncerty-i-spektakli', 'city' => 'orenburg', 'lat' => 51.7688, 'lng' => 55.1017, 'address' => 'ул. Маршала Жукова, 34', 'hours' => 'По афише', 'tags' => ['vecherniy', 'v-centre'], 'short' => 'Классические и современные программы в центре города.', 'schedule' => ['date' => now()->addDays(5)->toDateString(), 'time' => '19:00', 'timezone' => '+05:00'], 'image' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'teatr-muzykalnoy-komedii', 'section' => 'active', 'title' => 'Спектакль в Театре музыкальной комедии', 'category' => 'koncerty-i-spektakli', 'city' => 'orenburg', 'lat' => 51.7674, 'lng' => 55.0992, 'address' => 'ул. Терешковой, 13', 'hours' => 'По афише', 'tags' => ['vecherniy', 's-semey'], 'short' => 'Музыкальные постановки и семейные спектакли.', 'schedule' => ['date' => now()->addDays(9)->toDateString(), 'time' => '18:30', 'timezone' => '+05:00'], 'image' => 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'park-topolya', 'section' => 'active', 'title' => 'Парк «Тополя»', 'category' => 'sport-i-aktivnyy-otdyh', 'city' => 'orenburg', 'lat' => 51.7913, 'lng' => 55.1191, 'address' => 'ул. Постникова, 30', 'hours' => 'Ежедневно 10:00–22:00', 'tags' => ['s-semey', 'dlya-detey', 'na-prirode'], 'short' => 'Аттракционы, прогулки и сезонные активности для детей.', 'image' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'den-goroda-orenburg', 'section' => 'active', 'title' => 'День города Оренбурга', 'category' => 'festivali-i-yarmarki', 'city' => 'orenburg', 'lat' => 51.7680, 'lng' => 55.0975, 'address' => 'центральные площадки города', 'hours' => 'По программе фестиваля', 'tags' => ['besplatno', 's-semey', 'vecherniy'], 'short' => 'Городской праздник с концертами, ярмаркой и вечерней программой.', 'schedule' => ['date' => now()->addWeeks(3)->toDateString(), 'time' => null, 'timezone' => '+05:00'], 'image' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'katok-zvezdnyy', 'section' => 'active', 'title' => 'Каток в ЛД «Звёздный»', 'category' => 'sport-i-aktivnyy-otdyh', 'city' => 'orenburg', 'lat' => 51.8075, 'lng' => 55.1604, 'address' => 'пр. Гагарина, 21/1', 'hours' => 'По расписанию сеансов', 'tags' => ['s-semey', 'dlya-detey'], 'short' => 'Массовые катания и спортивные секции.', 'image' => 'https://images.unsplash.com/photo-1515703407324-5f753afd8be8?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'yarmarka-v-orske', 'section' => 'active', 'title' => 'Городская ярмарка в Орске', 'category' => 'festivali-i-yarmarki', 'city' => 'orsk', 'lat' => 51.2296, 'lng' => 58.4752, 'address' => 'центральная площадь Орска', 'hours' => 'Сб–Вс 11:00–18:00', 'tags' => ['na-vyhodnye', 'besplatno', 'lokalnoe'], 'short' => 'Локальные продукты, ремесленники и семейная программа выходного дня.', 'schedule' => ['date' => now()->addDays(12)->toDateString(), 'time' => '11:00', 'timezone' => '+05:00'], 'image' => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'restoran-staraya-melnica', 'section' => 'gastronomy', 'title' => 'Ресторан «Старая мельница»', 'category' => 'restorany', 'city' => 'orenburg', 'lat' => 51.7697, 'lng' => 55.0970, 'address' => 'ул. Советская, 31', 'hours' => 'Ежедневно 12:00–23:00', 'bill' => '1500–2500 ₽', 'cuisines' => ['mestnaya-kuhnya', 'evropeyskaya'], 'tags' => ['v-centre', 'lokalnoe'], 'short' => 'Уютный ресторан с блюдами локальной и европейской кухни.', 'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'kofeynya-zerno', 'section' => 'gastronomy', 'title' => 'Кофейня «Зерно»', 'category' => 'kafe-i-kofeyni', 'city' => 'orenburg', 'lat' => 51.7665, 'lng' => 55.1011, 'address' => 'ул. Советская, 24', 'hours' => 'Ежедневно 08:00–22:00', 'bill' => '400–800 ₽', 'cuisines' => ['kofe-i-deserty', 'evropeyskaya'], 'tags' => ['v-centre'], 'short' => 'Кофе, завтраки и десерты рядом с прогулочным центром.', 'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'chayhana-karavan', 'section' => 'gastronomy', 'title' => 'Чайхана «Караван»', 'category' => 'lokalnaya-kuhnya', 'city' => 'orsk', 'lat' => 51.2303, 'lng' => 58.4731, 'address' => 'пр. Ленина, 45', 'hours' => 'Ежедневно 11:00–23:00', 'bill' => '900–1600 ₽', 'cuisines' => ['kavkazskaya', 'mestnaya-kuhnya'], 'tags' => ['s-semey', 'lokalnoe'], 'short' => 'Восточная кухня, чай и большие блюда для компании.', 'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'buzuluk-gastrobar-step', 'section' => 'gastronomy', 'title' => 'Гастробар «Степь»', 'category' => 'restorany', 'city' => 'buzuluk', 'lat' => 52.7881, 'lng' => 52.2623, 'address' => 'ул. Ленина, 56', 'hours' => 'Пн–Чт 12:00–22:00, Пт–Сб 12:00–00:00', 'bill' => '1200–2200 ₽', 'cuisines' => ['avtorskaya', 'mestnaya-kuhnya'], 'tags' => ['vecherniy', 'lokalnoe'], 'short' => 'Авторское меню на локальных продуктах и спокойный вечерний формат.', 'image' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'sol-iletsk-kafe-kurortnoe', 'section' => 'gastronomy', 'title' => 'Кафе «Курортное»', 'category' => 'kafe-i-kofeyni', 'city' => 'sol-iletsk', 'lat' => 51.1614, 'lng' => 54.9910, 'address' => 'ул. Советская, 6', 'hours' => 'Сезонно 09:00–22:00', 'bill' => '500–1000 ₽', 'cuisines' => ['evropeyskaya', 'kofe-i-deserty'], 'tags' => ['s-semey', 'na-vyhodnye'], 'short' => 'Простое кафе для завтрака и обеда перед поездкой к озёрам.', 'image' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1200&q=80'],
            ['slug' => 'buguruslan-pelmennaya', 'section' => 'gastronomy', 'title' => 'Пельменная «Домашняя»', 'category' => 'lokalnaya-kuhnya', 'city' => 'buguruslan', 'lat' => 53.6554, 'lng' => 52.4420, 'address' => 'ул. Революционная, 18', 'hours' => 'Ежедневно 10:00–21:00', 'bill' => '400–900 ₽', 'cuisines' => ['mestnaya-kuhnya'], 'tags' => ['s-semey', 'lokalnoe'], 'short' => 'Домашняя кухня, пельмени и горячие обеды в центре Бугуруслана.', 'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&q=80'],
        ];

        foreach ($places as $item) {
            $place = Place::updateOrCreate(['slug' => $item['slug']], [
                'section' => $item['section'],
                'title' => $item['title'],
                'short_description' => $item['short'],
                'description_html' => '<p>'.$item['short'].' Добавлено как демонстрационный контент для локальной CMS.</p>',
                'category_id' => $categories[$item['category']]->id,
                'city_id' => $cities[$item['city']]->id,
                'latitude' => $item['lat'],
                'longitude' => $item['lng'],
                'address' => $item['address'],
                'working_hours' => $item['hours'],
                'average_bill' => $item['bill'] ?? null,
                'menu_html' => $item['section'] === 'gastronomy' ? '<p>Демо-меню уточняется в CMS.</p>' : null,
                'schedule' => $item['schedule'] ?? null,
                'seo_title' => $item['title'].' — Афиша Оренбуржья',
                'seo_description' => $item['short'],
                'seo_canonical_path' => '/places/'.$item['slug'],
                'is_published' => true,
            ]);

            $place->tags()->sync(collect($item['tags'])->map(fn (string $slug) => $tags[$slug]->id)->all());

            if (($item['section'] === 'gastronomy') && isset($item['cuisines'])) {
                $place->cuisineTypes()->sync(collect($item['cuisines'])->map(fn (string $slug) => $cuisines[$slug]->id)->all());
            }

            $place->images()->updateOrCreate(['sort_order' => 0], [
                'url' => $item['image'],
                'alt' => $item['title'],
                'title' => $item['title'],
                'is_cover' => true,
            ]);
        }

        $promos = [
            ['placement' => 'kiosk-home', 'priority' => 100, 'section' => null, 'title' => 'Начните с Национальной деревни', 'teaser' => 'Быстрый маршрут для первого знакомства с Оренбургом.', 'target_type' => 'place', 'target_slug' => 'nacionalnaya-derevnya', 'target_url' => null, 'image' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1200&q=80'],
            ['placement' => 'home', 'priority' => 90, 'section' => null, 'title' => 'Выходные в Бузулукском бору', 'teaser' => 'Природа, прогулки и семейный маршрут на два дня.', 'target_type' => 'place', 'target_slug' => 'buzulukskiy-bor', 'target_url' => null, 'image' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1200&q=80'],
            ['placement' => 'section', 'priority' => 80, 'section' => 'gastronomy', 'title' => 'Локальная кухня Оренбуржья', 'teaser' => 'Рестораны и кафе с местными продуктами.', 'target_type' => 'section', 'target_slug' => 'gastronomy', 'target_url' => null, 'image' => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=1200&q=80'],
            ['placement' => 'place-details', 'priority' => 70, 'section' => 'active', 'title' => 'Афиша ближайших событий', 'teaser' => 'Концерты, ярмарки и семейные активности на этой неделе.', 'target_type' => 'section', 'target_slug' => 'active', 'target_url' => null, 'image' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=1200&q=80'],
        ];

        foreach ($promos as $item) {
            $promo = Promo::updateOrCreate(['placement' => $item['placement'], 'title' => $item['title']], [
                'priority' => $item['priority'],
                'section' => $item['section'],
                'active_from' => now()->subDay(),
                'active_until' => now()->addMonth(),
                'teaser' => $item['teaser'],
                'target_type' => $item['target_type'],
                'target_slug' => $item['target_slug'],
                'target_url' => $item['target_url'],
            ]);

            $promo->image()->updateOrCreate(['sort_order' => 0], [
                'url' => $item['image'],
                'alt' => $item['title'],
                'title' => $item['title'],
                'is_cover' => true,
            ]);
        }
    }
}
