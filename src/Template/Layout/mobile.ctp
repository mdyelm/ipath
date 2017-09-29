<!DOCTYPE html>
<html amp lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="canonical" href="http://example.ampproject.org/article-metadata.html" />
        <script async src="https://cdn.ampproject.org/v0.js"></script>
        <meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=no">
        <meta name="format-detection" content="telephone=no" />
        <title>
            <?= __('Survey Manager'); ?>
        </title>
        <?= $this->Html->css('base.css'); ?>
        <?= $this->Html->css('style.css'); ?>
        <?= $this->Html->css('bootstrap.min.css'); ?>
        <?= $this->Html->css('common.css'); ?>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBcr156gD5J8cKKNovpIwlC_TePdXChJYE&v=3.11&sensor=false" type="text/javascript"></script>
        <?= $this->Html->script(['jquery-2.1.1', 'rotate', 'custom', 'bootstrap.min'], array('type' => 'text/javascript')) ?>
        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
        <script type="application/ld+json">
            {
            "@context": "http://schema.org",
            "@type": "NewsArticle",
            "headline": "Open-source framework for publishing content",
            "datePublished": "2017-02-27T12:02:41Z",
            "image": [
            "logo.jpg"
            ]
            }
        </script>
        <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
        <style amp-custom>
            /*Mobile*/
            .mobile #content-left{width: 100%}
            .mobile #content-right{width: 100%}
            .mobile .fix-menu{text-align: center;  padding: 10px 0 0 0;}
            .mobile .fix-table{width: 100% !important;}
            .mobile .table-responsive{border:0;}
            .mobile #detail_route{margin-top: 0;}
            .mobile .btn{margin-bottom: 10px;}
            @media screen and (min-width : 320px) and (max-width : 579px) { 
                .mobile .title-top > span{
                    margin-right: 10px;
                    font-size: 16px;
                }
            }
            @media screen and (min-width : 1200px) and (max-width : 1440px) {
                #div-scroll-table-language {height: 82.3%;}
            }
        </style>
    </head>
    <body class="mobile">
        <?= $this->fetch('content') ?>
    </body>
</html>
