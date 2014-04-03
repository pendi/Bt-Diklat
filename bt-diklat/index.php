<?php

/**
 * index.php
 *
 * @package     arch-php
 * @author      xinixman <hello@xinix.co.id>
 * @copyright   Copyright(c) 2011 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <hello@xinix.co.id>
 *
 *
 */

require_once 'arch/arch.php';

$app = new ARCH('development', __DIR__);

require_once $app->ROOTPATH.'/'.$app->SYSTEMPATH.'core/CodeIgniter.php';