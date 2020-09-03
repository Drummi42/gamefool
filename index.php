<?php
spl_autoload_register(function (string $className) {
    if (file_exists($className . '.php')) {
        include_once "$className.php";
    }
});

try {
    (new GameFool())
    (new Player('Rick'))
    (new Player('Morty'))
    (new Player('Summer'))
    (new CardsDeck(rand(1, 0xffff)))
    ();
} catch (Throwable $e) {
    echo $e->getMessage();
}
