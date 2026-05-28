<?php
// install.php - запустите этот файл первым в браузере
$dbFile = __DIR__ . '/database.sqlite';

try {
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Таблица категорий номеров
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL
        )
    ");

    // Таблица номеров
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rooms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            price_per_person INTEGER NOT NULL,
            image TEXT NOT NULL,
            features TEXT NOT NULL,
            FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
        )
    ");

    // Таблица заявок на бронирование
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            room_id INTEGER NOT NULL,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            phone TEXT NOT NULL,
            email TEXT NOT NULL,
            check_in DATE NOT NULL,
            check_out DATE NOT NULL,
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (room_id) REFERENCES rooms (id) ON DELETE CASCADE
        )
    ");

    // Таблица пользователей (админ)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password_hash TEXT NOT NULL
        )
    ");

    // Вставка категорий
    $categories = ['Стандарт', 'Студия', 'Люкс'];
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO categories (name) VALUES (?)");
    foreach ($categories as $cat) {
        $stmt->execute([$cat]);
    }

    // Получаем ID категорий
    $catIds = [];
    $res = $pdo->query("SELECT id, name FROM categories");
    while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        $catIds[$row['name']] = $row['id'];
    }

    // Очищаем и вставляем номера
    $pdo->exec("DELETE FROM rooms");
    
    $roomsData = [
        ['Стандарт', 'Уютный стандарт', 10000, 'standart.png', '["Включен завтрак","Душ + Ванна"]'],
        ['Студия', 'Светлая студия', 8000, 'studio.jpg', '["Включен завтрак, обед","Душ + Ванна","Кондиционер"]'],
        ['Люкс', 'Роскошный люкс', 19000, 'lux.png', '["Включен завтрак, обед, ужин","Душ + Ванна","Кондиционер","Телевизор","Мини-бар","Вид на город"]'],
        ['Стандарт', 'Стандарт с видом', 10500, 'standart.png', '["Включен завтрак","Душ + Ванна","Вид во двор"]'],
        ['Студия', 'Студия с террасой', 9500, 'studio.jpg', '["Включен завтрак, обед","Душ + Ванна","Кондиционер","Терраса"]'],
        ['Люкс', 'Люкс президентский', 25000, 'lux.png', '["Включен завтрак, обед, ужин","Джакузи","Кондиционер","Телевизор","Мини-бар","Вид на город","Сауна"]'],
        ['Стандарт', 'Стандарт эконом', 8000, 'standart.png', '["Включен завтрак","Душ"]'],
        ['Студия', 'Студия для семьи', 12000, 'studio.jpg', '["Включен завтрак, обед","Душ + Ванна","Кондиционер","Детская кровать"]'],
        ['Люкс', 'Люкс бизнес', 22000, 'lux.png', '["Включен завтрак, обед, ужин","Душ + Ванна","Кондиционер","Телевизор","Мини-бар","Вид на город","Рабочая зона"]']
    ];

    $stmt = $pdo->prepare("INSERT INTO rooms (category_id, title, price_per_person, image, features) VALUES (?, ?, ?, ?, ?)");
    foreach ($roomsData as $room) {
        $stmt->execute([
            $catIds[$room[0]],
            $room[1],
            $room[2],
            $room[3],
            $room[4]
        ]);
    }

    // Создание администратора (пароль: admin123)
    $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("DELETE FROM users");
    $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)")->execute(['admin', $passwordHash]);

    echo "<h2 style='color: green;'>✅ База данных успешно создана и заполнена!</h2>";
    echo "<p>Логин администратора: <strong>admin</strong></p>";
    echo "<p>Пароль администратора: <strong>admin123</strong></p>";
    echo "<p><a href='index.php'>Перейти на главную страницу</a></p>";

} catch (PDOException $e) {
    die("Ошибка установки: " . $e->getMessage());
}
