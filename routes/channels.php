<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('boards', function () {
    return true;
});
