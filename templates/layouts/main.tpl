<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$pageTitle|default:$app.name}</title>
    {$viteTags nofilter}
</head>
<body class="bg-stone-50 text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col">
        {include file="partials/header.tpl"}
        <div class="flex-1">
            {block name=content}{/block}
        </div>
        {include file="partials/footer.tpl"}
    </div>
</body>
</html>
