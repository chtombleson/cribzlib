<html>
<head>
    <title>%%$sitename%%</title>
</head>
<body>
    <p>Welcome to: %%$sitename%%</p>
    {if $news}
        <ul>
        (foreach $news as $newsitem)
            <li>
                <h4>&&$newsitem.name&&</h4>
                <p>&&$newsitem.description&&</p>
            </li>
        (/foreach)
        </ul>
    {else}
        <p>No News Today</p>
    {/if}
</body>
</html>
