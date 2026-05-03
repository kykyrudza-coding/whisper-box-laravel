# WhisperBox API

API для анонімних нотаток з можливістю оцінювання (likes).

---

## Base URL

```
/api
```

---

## Формат відповіді

Успішна відповідь:

```json
{
  "status": "success",
  "data": ...
}
```

Відповідь при помилці валідації:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["Error message"]
  }
}
```

---

## Сутність Note

| Поле        | Тип    | Опис             |
| ----------- | ------ | ---------------- |
| id          | int    | ID нотатки       |
| title       | string | Заголовок        |
| content     | string | Текст нотатки    |
| mood        | string | Настрій          |
| likes_count | int    | Кількість лайків |
| created_at  | string | Дата створення   |

### Допустимі значення `mood`

* happy
* sad
* neutral
* angry
* confused

---

## Endpoints

### Отримати список нотаток

```
GET /api/notes
```

#### Query параметри

* `sort=latest` (за замовчуванням)
* `sort=popular` (за likes_count)

#### Відповідь

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Test",
      "content": "Hello",
      "mood": "happy",
      "likes_count": 5,
      "created_at": "2026-05-03T12:00:00.000000Z"
    }
  ]
}
```

---

### Отримати одну нотатку

```
GET /api/notes/{id}
```

#### Відповідь

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "title": "Test",
    "content": "Hello",
    "mood": "happy",
    "likes_count": 5,
    "created_at": "2026-05-03T12:00:00.000000Z"
  }
}
```

---

### Створити нотатку

```
POST /api/notes
```

#### Body

```json
{
  "title": "My note",
  "content": "Some text",
  "mood": "happy"
}
```

#### Відповідь

```json
{
  "status": "success",
  "message": "Note created successfully",
  "data": {
    "id": 1,
    "title": "My note",
    "content": "Some text",
    "mood": "happy",
    "likes_count": 0,
    "created_at": "2026-05-03T12:00:00.000000Z"
  }
}
```

#### Статус

```
200 OK
```

---

### Поставити лайк

```
POST /api/notes/{id}/like
```

#### Відповідь

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "title": "Test",
    "content": "Hello",
    "mood": "happy",
    "likes_count": 6,
    "created_at": "2026-05-03T12:00:00.000000Z"
  }
}
```

---

## Валідація

| Поле    | Правило           |
| ------- | ----------------- |
| title   | required, string  |
| content | required, string  |
| mood    | required, in:enum |

---

## Тести

Покриті сценарії:

* отримання списку нотаток
* перегляд однієї нотатки
* створення нотатки
* валідація
* лайки (за потреби)

Запуск:

```bash
php artisan test
```

---

## Запуск проєкту

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```
