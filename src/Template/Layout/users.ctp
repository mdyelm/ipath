<!doctype html>
<html amp lang="en">
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=no">
        <title>
            <?=__('Survey Manager')?>
        </title>
        <?= $this->Html->css('base.css') ?>
        <?= $this->Html->css('style.css') ?>
        <?= $this->Html->css('bootstrap.min.css');?>
        <?= $this->Html->css('common.css');?>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBcr156gD5J8cKKNovpIwlC_TePdXChJYE&v=3.11&sensor=false" type="text/javascript"></script>
        <?= $this->Html->script(['jquery-2.1.1', 'rotate', 'custom','bootstrap.min']) ?>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
    </head>
    <body>
        <?= $this->fetch('content') ?>
    </body>
</html>