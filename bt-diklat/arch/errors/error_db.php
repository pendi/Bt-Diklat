<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $heading; ?> :(</title>
		<link href="<?php echo theme_url('css/error.css') ?>" media="all" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="container">
		    <h1><?php echo $heading; ?> <span>:(</span></h1>
		    <?php echo $message; ?>

            <script>
                var GOOG_FIXURL_LANG = (navigator.language || '').slice(0,2),GOOG_FIXURL_SITE = location.host;
            </script>
            <script src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>
        </div>
    </body>
</html>