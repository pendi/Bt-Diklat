<script type="text/javascript">
    $(function() {
        function resize() {
            $('#login-pane').css({
                left: ($(window).width() - $('#login-pane').width()) / 2,
                top: (($(window).height() - $('#login-pane').height()) / 2) - 25
            });
        }

        $(window).resize(function() {
            resize();
        });
        resize();
    });
</script>

<style>
    .login-pane .title {
        margin: 10px 0;
        text-align: center;
        font-size: 1.6em;
        color: #F93;
        text-shadow: 0 1px 2px white;
        padding: 10px 0;
        text-transform: uppercase;
    }

    .login-pane .title strong {
        font-weight: normal;
        font-size: 0.6em;
        line-height: 20px;
        color: #236A94;
        text-shadow: 0 1px 2px white;
    }
</style>

<div id="login-pane" class="login-pane<?php echo (is_error_exists()) ? " accessdenied" : '' ?>">
    <div>
        <form action="" method="post">
            <div class="login-form">

                <?php /* Put your logo here inside div.logo */ ?>
                <div class="logo">
                    <img src="<?php echo theme_url('img/logo.png') ?>" width="30%">
                    <div class="title">Monitoring Diklat<br/>Kementerian Perhubungan<br /><strong>DIREKTORAT JENDERAL PERHUBUNGAN LAUT</strong></div>
                </div>

                <div class="system-time">
                    <span class="xinix-date"></span> &#149; <span class="xinix-time"></span>
                </div>
                <?php if (!$CI->config->item('use_db')): ?>
                <div style="text-align: center; color: red; font-weight: bold">
                    Database not ready!
                </div>
                <?php endif ?>
                <div>
                    <input type="text" name="login" value=""  placeholder="<?php echo l('Username/Email') ?>" />
                </div>
                <div>
                    <input type="password" name="password" value="" placeholder="<?php echo l('Password') ?>" />
                </div>
                <div style="padding-top:10px">
                    <input type="hidden" name="continue" value="" />
                    <input type="submit" value="Masuk" />
                </div>
            </div>
        </form>
    </div>
</div>