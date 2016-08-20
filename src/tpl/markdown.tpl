<!DOCTYPE html>
<html>
<head>
    <title>Autofile | {{$document.relative_name}}</title>
    <script type="text/javascript" src="{$config.base_url}.static/js/jquery-3.1.0.min.js"></script>
    <script type="text/javascript" src="{$config.base_url}.static/js/highlight.min.js"></script>
    <script type="text/javascript" src="{$config.base_url}.static/js/main.js"></script>
    <link href="{$config.base_url}.static/style/style.css" type="text/css" rel="stylesheet">
    <link href="{$config.base_url}.static/style/github-markdown.css" type="text/css" rel="stylesheet">
    <link href="{$config.base_url}.static/style/github-highlight.css" type="text/css" rel="stylesheet">
    <link rel="shortcut icon" href="{$config.base_url}.static/img/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="markdown-page">
        <div class="markdown-body">
            {$markdown}
        </div>
    </div>
</body>
</html>
