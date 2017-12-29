<?php

// Show CSRF Tokens
$view->getEnvironment()->addFunction(new \Twig_Function('csrf_tokens', function () {
    echo 12333;
}));