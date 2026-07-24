<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$pageTitle|default:$app.name}</title>
    {$viteTags nofilter}
</head>
<body class="bg-stone-50 text-slate-900 antialiased">
    <div class="min-h-screen">
        {include file="partials/header.tpl"}
        {block name=content}{/block}
    </div>
</body>
</html>
