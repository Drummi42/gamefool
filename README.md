# gamefool

Тестовое задание с собеса. На чистом PHP написать автоматически играющую партию в "дурака".

Запускаться игра должна таким кодом:

```php
(new GameFool())
    (new Player('Rick'))
    (new Player('Morty'))
    (new Player('Summer'))
    (new CardsDeck(rand(1, 0xffff)))
    ();
```

