<?php

require 'vendor/autoload.php';

use App\DB;
use App\Models\Tariffs\TariffFixed;
use App\Models\Tariffs\TariffPercentage;

$db = DB::initDB();

$transportExchange = new App\TransportExchange;

// Создаём клиента (грузодателя)
$provider = $transportExchange->createProvider('Астраханский Мясокомбинат');

// Создаём заявку от имени клиента
$order = $transportExchange->placeOrder(
    $provider,
    'Рыбные консервы', // description
    '2021-12-11 00:00:00', // load_from
    '2021-12-12 00:00:00', // load_to
    '2021-12-13 00:00:00', // unload_from
    '2021-12-14 00:00:00', // unload_to
    5000, // cargo_weight
    320, // cargo_height
    800, // cargo_volume
    'Россия', // load_country
    'Астрахань', // load_city
    'Кирова, 5', // load_address
    'Казахстан', // unload_country
    'Атырау', // unload_city
    'Абая, 11' // unload_address
);

// Создаём заявку 2 от имени клиента
$order2 = $transportExchange->placeOrder(
    $provider,
    'Контейнеры товаров общего пользования', // description
    '2021-12-21 00:00:00', // load_from
    '2021-12-22 00:00:00', // load_to
    '2021-12-23 00:00:00', // unload_from
    '2021-12-24 00:00:00', // unload_to
    2000, // cargo_weight
    240, // cargo_height
    400, // cargo_volume
    'Россия', // load_country
    'Астрахань', // load_city
    'Кирова, 5', // load_address
    'Казахстан', // unload_country
    'Атырау', // unload_city
    'Абая, 11' // unload_address
);

// Создаём перевозчика (исполнителя) с тарифом 5% от суммы сделки
$tariff = $transportExchange->createTariff(TariffPercentage::class, ['rate' => 5]);
$carrier = $transportExchange->createCarrier('Морской Перевозчик "Каспийский Груз"', $tariff);

// Создаём второго перевозчика (исполнителя) с фиксированным тарифом
$tariff2 = $transportExchange->createTariff(TariffFixed::class, ['amount' => 50000, 'currency' => 'RUB']);
$carrier2 = $transportExchange->createCarrier('Башкирские Авиалинии', $tariff2);

// Перевозчик делает ставку 200.000 руб на заказ
$bid = $transportExchange->placeBid($carrier, $order, 200 * 1000, 'RUB', 'Доступно страхование груза.');

// Обмен информацией между клиентом и перевозчиком через комментарии к ставке:
// Комментарий клиента
$transportExchange->addComment($provider, $bid, 'Есть ли возможность получить скидку?');
// Ответ перевозчика
$transportExchange->addComment($carrier, $bid, 'При внесении дополнительной страховки будет скидка 5%.');
// Комментарий клиента
$transportExchange->addComment($provider, $bid, 'Поняли, нас устраивает.');

// Получаем список активных заявок между двумя странами
$orders = $transportExchange->getActiveOrdersBetweenCountries('Россия', 'Казахстан');
echo "Активные заявки между Россией и Казахстаном: " . print_r($orders, true) . "\n\n";

// Сумма комиссии бирже от перевозчика за декабрь до утверждения сделки
$totalFees = $transportExchange->getCarrierFeesForPeriod($carrier, '2021-12-01 00:00:00', '2021-12-31 23:59:59');
echo "Сумма комиссии бирже от перевозчика за декабрь до утверждения сделки: " . print_r($totalFees, true) . "\n\n";

// Утверждение сделки
$deal = $transportExchange->makeDeal($order, $bid, $carrier);

// Комиссия биржи
echo "Комиссия составила " . $deal->fee . " " . $deal->fee_currency . "\n\n";

// Сумма комиссии бирже от перевозчика за декабрь после утверждения сделки
$totalFees = $transportExchange->getCarrierFeesForPeriod($carrier, '2021-12-01 00:00:00', '2021-12-31 23:59:59');
echo "Сумма комиссии бирже от перевозчика за декабрь после утверждения сделки: " . print_r($totalFees, true) . "\n\n";

// Архивируем сделку
$transportExchange->archiveOrder($order);
echo "Архивируем сделку." . "\n\n";

// Получаем список активных заявок между двумя странами (должно стать на одну меньше)
$orders = $transportExchange->getActiveOrdersBetweenCountries('Россия', 'Казахстан');
echo "Активные заявки между Россией и Казахстаном: " . print_r($orders, true) . "\n\n";

// Список завершённых сделок
$deals = $transportExchange->getDeals()->toArray();
echo "Список завершённых сделок: " . print_r($deals, true) . "\n\n";