<?php

// Show CSRF Tokens
$view->getEnvironment()->addFunction(new \Twig_Function('csrf_tokens', function () use ($container) {
    $csrfTokens = new \App\ViewHelpers\CsrfTokensHelper($container);

    return $csrfTokens->render();
}));
