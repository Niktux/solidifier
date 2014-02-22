<?php

namespace Solidifier\Dispatchers;

use Solidifier\Dispatcher;
use Solidifier\Event;

class NullDispatcher implements Dispatcher
{
    public function dispatch(Event $event)
    {
    }
}