<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
$cakeDescription = __('Survey Manager');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>
            <?= $cakeDescription ?>
        </title>
        <?= $this->Html->meta('icon') ?>
        <!-- Bootstrap -->
        <?= $this->Html->css('bootstrap.min.css') ?>
        <?= $this->Html->css('common.css') ?>
        <?= $this->Html->css('login.css') ?>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row top30 fix-content bg-login" style="opacity: 0;">
                <?= $this->fetch('content') ?>
            </div>
        </div><!-- /.container -->
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <?= $this->Html->script('bootstrap.min'); ?>
        <script type="text/javascript">
            function verticalSize(){
                var heightLogin = $('.bg-login').css('height');
                var heightWindow = $(window).height();
                var top = (parseInt(heightWindow) - parseInt(heightLogin))/2;
                $('.bg-login').css('margin-top', top + 'px').css('opacity', 1);
            }
            $(document).ready(function () {
               verticalSize(); 
            });
        </script>
    </body>
</html>