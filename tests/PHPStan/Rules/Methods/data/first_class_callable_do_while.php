<?php declare(strict_types=1);

namespace FirstClassCallableInDoWhile;

final class SomeFirstClassCallableInDoWhile
{
    public function getSubscribedEvents()
    {
        do {

        } while ($this->textElement(...));
    }

    public function textElement()
    {
         return 1;
    }
}
