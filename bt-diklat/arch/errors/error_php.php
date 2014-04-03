<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>A PHP Error was encountered :(</title>
		<link href="<?php echo theme_url('css/error.css') ?>" media="all" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="container">
		    <h1>A PHP Error was encountered <span>:(</span></h1>
		    <p>Severity: <?php echo (isset($exception)) ? get_class($exception) : $severity; ?></p>
		    <p>Message:  <?php echo (isset($exception)) ? $exception->getMessage() : $message; ?></p>
		    <p>Filename: <?php echo (isset($exception)) ? $exception->getFile() : $filepath; ?></p>
		    <p>Line Number: <?php echo (isset($exception)) ? $exception->getLine() : $line; ?></p>
		    <?php if (isset($exception)): ?>
		    <pre><code><?php echo $exception->getTraceAsString() ?></code></pre>
		    <?php endif ?>

            <script>
                var GOOG_FIXURL_LANG = (navigator.language || '').slice(0,2),GOOG_FIXURL_SITE = location.host;
            </script>
            <script src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>
        </div>
    </body>
</html>