
<?php

class HelloController
{
    public function sayHello(string $name): void
    {
        echo '<h2>Привет, ' .
            htmlspecialchars($name, ENT_QUOTES, 'UTF-8') .
            '</h2>';
    }

    public function sayBye(string $name): void
    {
        echo '<h2>Пока, ' .
            htmlspecialchars($name, ENT_QUOTES, 'UTF-8') .
            '</h2>';
    }
}
