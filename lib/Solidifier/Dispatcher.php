<?php

namespace Solidifier;

interface Dispatcher
{
    public function dispatch(Event $event);
}