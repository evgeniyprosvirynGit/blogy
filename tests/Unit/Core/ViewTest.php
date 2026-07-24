<?php

declare(strict_types=1);

it('escapes dynamic template data by default', function (): void {
    $html = testView()->render('errors/show.tpl', [
        'pageTitle' => '<script>alert("x")</script>',
        'statusCode' => '500',
        'title' => '<b>Broken</b>',
        'message' => '<img src=x onerror=alert(1)>',
    ]);

    expect($html)
        ->toContain('&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;')
        ->toContain('&lt;b&gt;Broken&lt;/b&gt;')
        ->toContain('&lt;img src=x onerror=alert(1)&gt;')
        ->not->toContain('<script>alert("x")</script>')
        ->not->toContain('<b>Broken</b>')
        ->not->toContain('<img src=x onerror=alert(1)>');
});
