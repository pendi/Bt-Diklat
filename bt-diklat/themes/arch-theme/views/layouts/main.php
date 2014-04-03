<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" >
<html>
<head>
    <title><?php echo $CI->_page_title; ?></title>
    <!-- Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="<?php echo theme_url('css/bootstrap.css') ?>" rel="stylesheet" media="screen" />
    <link href="<?php echo theme_url('css/bootstrap-responsive.min.css') ?>" rel="stylesheet" />
    <link href="<?php echo theme_url('css/global.css') ?>" rel="stylesheet" media="screen" />
    <link href="<?php echo theme_url('css/user.css') ?>" rel="stylesheet" media="screen" />
    <link href="<?php echo theme_url('js/code-prettify/prettify.css') ?>" media="all" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="<?php echo theme_url('js/jquery-1.8.2.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo theme_url('js/underscore-min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo theme_url('js/bootstrap.js') ?>"></script>
    <script type="text/javascript" src="<?php echo theme_url('js/code-prettify/prettify.js') ?>"></script>
    <script type="text/javascript" src="<?php echo theme_url('js/datepicker.js') ?>"></script>
    <script type='text/javascript' src="<?php echo theme_url('js/jquery.autocomplete.js') ?>"></script>

    <!-- Chart -->
    <script src="<?php echo theme_url('js/vendor/highcharts.js') ?>"></script>
    <script src="<?php echo theme_url('js/vendor/exporting.js') ?>"></script>

</head>

<?php $USER = $CI->auth->get_user() ?>

<body>

    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <i class="icon-th-list"></i>
                </a>
                <a class="brand" href="<?php echo site_url('site') ?>"><?php echo $CI->config->item('page_title') ?></a>
                <img src="<?php echo theme_url('img/logo.png') ?>" style="width: 30px;float: right">
                <div class="nav-collapse">
                    <?php echo $this->admin_panel->show() ?>


                    <ul class="menu nav pull-right">
                        <?php if (!empty($USER)): ?>
                        <li><?php // echo $this->notif->system->show() ?></li>
                        <?php endif ?>
                        <li><span class="system-time"><span class="xinix-date"></span> &#149; <span class="xinix-time"></span></span></li>
                        <?php if (!empty($USER)): ?>
                        <li class="user-sys has-children dropdown">
                            <a href="<?php echo site_url("profile"); ?>" data-toggle="dropdown">
                                <?php echo $USER['first_name'] . ' ' . $USER['last_name'] ?>
                                <i class="caret"></i>
                            </a>
                            <ul class="menu dropdown-menu">
                                <li><a href="<?php echo site_url("profile"); ?>"><?php echo l('Profil Pengguna') ?></a></li>
                                <li><a href="<?php echo $CI->auth->change_password_page() ?>"><?php echo l('Ganti Password') ?></a></li>
                                <li><a href="<?php echo site_url("user/logout"); ?>"><?php echo l('Keluar') ?></a></li>
                            </ul>
                        </li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="wrap">
        <div class="container-fluid" id="container">
            <?php echo xview_error() ?>
            <?php echo xview_info() ?>
            <?php echo $this->load->view($CI->_view, $CI->_data, true) ?>
        </div>
        <div id="push"></div>
    </div>

    <div id="footer">
        <p class="muted credit">
            <span class="big">
            Copyright &copy; 2013 <a href="http://dephub.go.id" target="blank">Kementerian Perhubungan, Direktorat Jenderal Perhubungan Laut</a>. All rights reserved. 
            </span>
            <span class="tiny">
                <a href="http://xinix.co.id" target="blank">Xinix Technology</a>
            </span>
            <?php if ($CI->config->item('enable_profiler')): ?>
            <span class="profiler">
                <a href="#" id="profiler_btn">
                    <span class="big">
                        ( time: {elapsed_time}, mem: {memory_usage} )
                    </span>
                    <span class="tiny">
                        (P)
                    </span>
                </a>
            </span>
            <?php endif ?>
        </p>
    </div>

    <script type="text/template" id="template-no-data-selected-error">
        <p>No selected record.</p>
    </script>

    <script type="text/template" id="template-modal">
        <div class="modal hide fade">
            <div class="modal-header btn-inverse">
                <button type="button" class="close btn-inverse" data-dismiss="modal" aria-hidden="true">x</button>
                <% if (title) { %>
                <h3><%= title %></h3>
                <% } %>
            </div>
            <div class="modal-body">
                <%= body || '' %>
            </div>
            <% if (footer) { %>
            <div class="modal-footer">
                <%= footer %>
            </div>
            <% } %>
        </div>
    </script>

    <script src="<?php echo theme_url('js/xn.js') ?>" data-base-url="<?php echo site_url('/') ?>"></script>
</body>
</html>
