<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/Controllers/AppController.php';
(new AppController())->handle($route);
