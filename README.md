# Задание 1 #
файл /orders.php

Запустить можно с php-cli: 
- `# php demo.php`

либо положить в папку веб сервера и открыть /demo.php

Ответ API (/approve) будет выбран случайно.

# Задание 2 #
## 2.1 ##

Разделить эту таблицу на две: с билетами, с категориями билетов.
т.к. детский и взрослый билет это тоже категории, то колонки ticket_adult_* ticket_kid_* теперь лишние


![scr](/img/1.png)


## 2.2 ##

Делаем **на каждый билет одну запись в таблице**, тогда у каждого посетителя будет и свой баркод. Соотвественно столбец ticket_quantity теперь не нужен.

![scr](/img/2.png)
На данный момент таблица уже отвечает заданным требованиям: у каждого посетителя свой баркод и возможно неограниченое количество категорий. 

В дополнение, можно сделать таблицу с заказами, что бы хранить там информацию об оплате и группировать билеты по заказу.

![scr](/img/3.png)


# Задание 3 #

## Метод create ##

	create(int event_id, string event_date, int ticket_adult_price, int ticket_adult_quantity, int ticket_kid_price, int ticket_kid_quantity)

Создаёт новый заказ.

Принимаемые параметры: 

* `event_id` - id мероприятия
* `event_date` - время в формате "YYYY-MM-DD hh:mm:ss", строка
* `ticket_adult_price` - цена взрослых билетов
* `ticket_adult_quantity` - кол-во взрослых билетов
* `ticket_kid_price` - цена детских билетов
* `ticket_kid_quantity` - кол-во детских билетов

В случае успеха возвращает `barcode` - уникальный набор чисел.

В случае неудачи бросает исключение

