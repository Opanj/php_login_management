<?php

namespace Opanjr\LoginManagement\Middleware;

interface Middleware
{
  function before(): void;
}
