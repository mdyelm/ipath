<!doctype html>
<html amp lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=no">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>
            <?= __('Survey Manager') ?>
        </title>
        <?= $this->Html->meta('icon') ?>
        <!-- Bootstrap -->
        <?= $this->Html->css('style.css') ?>
        <?= $this->Html->css('bootstrap.min.css'); ?>
        <?= $this->Html->css('common.css'); ?>
        <?= $this->Html->css('admin.css') ?>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
        <?= $this->fetch('script') ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <?= $this->Html->script(['custom']) ?>
    </head>
    <body>
        <div id="container">
            <div id="content-left">
                <?php //if ($this->request->params['action'] === 'version') : ?>
                <div class="fix-menu" data-toggle="collapse" data-target="#menuDropdown">
                    <span class="glyphicon glyphicon-menu-hamburger buttom-menu" aria-hidden="true"></span>
                    <div class="image-version">
                        <?php echo $this->Html->image('icon-menu.png', ['class' => 'fix-img-index']); ?>
                        <p class="version-index"><?= h('Version' . ' ' . $versionWeb); ?> </p>

                    </div>
                </div>
                <?php //else : ?>
<!--                <div class="fix-menu fix-menu-user" data-toggle="collapse" data-target="#menuDropdown"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span> ユーザー</div>-->
                <?php //endif ?>
                <div id="menuDropdown" class="collapse">
                    <ul>
                        <li>
                            <?= $this->Html->link('English', ['controller' => 'pages', 'action' => 'language', '?' => ['language' => 'en_US']]) ?> ｜
                            <?= $this->Html->link('日本語', ['controller' => 'pages', 'action' => 'language', '?' => ['language' => 'ja_JP']]) ?>
                        </li>
                        <li<?php echo (($this->request->params['controller'] === 'Surveys')) ? ' class="active"' : '' ?>>
                            <?=
                            $this->Html->link(__('Survey Summary'), [
                                'controller' => 'Surveys',
                                'action' => 'index',
                                    ], ['target' => '_blank']);
                            ?>
                        </li>
                        <?php if ($authUser['admin_flg']) : ?>
                            <li<?php echo (($this->request->params['controller'] === 'Users' && $this->request->params['action'] === 'index')) ? ' class="active"' : '' ?>>
                                <?=
                                $this->Html->link(__('User Management'), [
                                    'controller' => 'Users',
                                    'action' => 'index'
                                ]);
                                ?>
                            </li>
                        <?php endif; ?>
                            <li<?php echo (($this->request->params['controller'] === 'Users' && $this->request->params['action'] === 'datagps')) ? ' class="active"' : '' ?>>
                                <?=
                                $this->Html->link(__('Download GPS data'), [
                                    'controller' => 'Users',
                                    'action' => 'datagps'
                                ]);
                                ?>
                            </li>
                        <?php if ($authUser['admin_flg']) : ?>
                            <li<?php echo (($this->request->params['controller'] === 'Users' && $this->request->params['action'] === 'appLanguageVersion')) ? ' class="active"' : '' ?>>
                                <?=
                                $this->Html->link(__('App Setting'), [
                                    'controller' => 'Users',
                                    'action' => 'appLanguageVersion'
                                ]);
                                ?>
                            </li>
                        <?php endif; ?>
                        <li<?php echo (($this->request->params['controller'] === 'Users' && $this->request->params['action'] === 'version')) ? ' class="active"' : '' ?>>
                            <?=
                            $this->Html->link(__('Version Information'), [
                                'controller' => 'Users',
                                'action' => 'version'
                            ]);
                            ?>                        
                        </li>                            
                        <li>
                            <?=
                            $this->Html->link(__('Logout'), [
                                'controller' => 'UserAuth',
                                'action' => 'logout'
                            ]);
                            ?>
                        </li>
                    </ul>
                </div>
                <?php
                $imageDate = 'filter-up-down.png';
                $typeDate = "asc";
                $imageId = 'filter-up-down.png';
                $typeId = "asc";
                $imageUser = 'filter-up-down.png';
                $typeUser = "asc";
                if (!empty($filter) && !empty($type_filter)) {
                    if ($type_filter == "asc") {
                        if ($filter == "date") {
                            $imageDate = "filter-up.png";
                            $typeDate = "desc";
                        } elseif ($filter == "id") {
                            $imageId = "filter-up.png";
                            $typeId = "desc";
                        } elseif ($filter == "user") {
                            $imageUser = "filter-up.png";
                            $typeUser = "desc";
                        }
                    } else {
                        if ($filter == "date") {
                            $imageDate = "filter-down.png";
                        } elseif ($filter == "id") {
                            $imageId = "filter-down.png";
                        } elseif ($filter == "user") {
                            $imageUser = "filter-down.png";
                        }
                    }
                }
                ?>
                <div id="filterLeft">
                    <a href="<?php echo $this->request->here . '?filter=user&type=' . $typeUser; ?>">
                        <div class="filterUser">
                            <span>
                                <?= __('USER') ?>
                            </span>
                            <?php echo $this->Html->image($imageUser, ['class' => 'fix-img-dataGps']); ?>
                        </div>
                    </a>
                    <a href="<?php echo $this->request->here . '?filter=id&type=' . $typeId; ?>">
                        <div class="filterId">
                            <span>
                                <?= __('SURVEY ID') ?>
                            </span>
                            <?php echo $this->Html->image($imageId, ['class' => 'fix-img-dataGps']); ?>
                        </div>
                    </a>
                    <a href="<?php echo $this->request->here . '?filter=date&type=' . $typeDate; ?>">
                        <div class="filterDate">
                            <span>
                                <?= __('DATE') ?>
                            </span>
                            <?php echo $this->Html->image($imageDate, ['class' => 'fix-img-dataGps']); ?>
                        </div>
                    </a>
                </div>
                <div id="list_records">
                    <?php $routeArrays = $routes->toArray(); ?>
                    <?php foreach ($routeArrays as $key => $route) : ?>
                        <div class="item ov mb10" id="<?= $route['id'] ?>">
                            <div class="col-lg-8 showMap" data-bind="<?= $route['id'] ?>">
                                <?php if ($route['Users']['del_flg']) : ?>
                                    <span><?= __('DELETED USER') ?>: </span><?php echo $route['Users']['username']; ?><br>
                                <?php else : ?>
                                    <span><?= __('USER') ?>: </span><?php echo $route['Users']['username']; ?><br>
                                <?php endif; ?>
                                <span><?= __('Device ID') ?>: </span><?= $route['Devices']['name'] ?><br>
                                <span><?= __('SURVEY ID') ?>: </span><?php echo $this->App->getRouteIndex($arrayIndex, $route['id']); ?><br>
                                <span><?= __('DATE') ?>: </span><?= date('Y/m/d', strtotime($route['time_start'])) ?><br>
                                <span><?= __('LOCATION') ?>: </span><?= $route['country'] ?>
                            </div>
                            <div class="col-lg-4 showData">
                                <a class="target targetOnly" rel="<?= $this->Url->build(["controller" => "surveys", "action" => "viewRoute", $route['id']]) ?>">
                                    <?php echo $this->App->getImageFirst($route['id']); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($routes->count() > 10): ?>
                    <button class="btn-primary pull-right" id="load-more" data-id="0" style="border: none;
                            padding: 10px 20px;
                            line-height: 20px;">
                            <?= __('Load More') ?>
                    </button>
                    <input id="checkFilterLoad" value="<?php
                    if (!empty($filter)) {
                        echo $filter;
                    }
                    ?>" type="hidden">
                    <input id="checkTypeFilterLoad" value="<?php
                    if (!empty($type_filter)) {
                        echo $type_filter;
                    }
                    ?>" type="hidden">
                       <?php endif; ?>
            </div>
            <div id="content-right" class="ov">
                <div id="content-right-inner">
                    <?= $this->fetch('content') ?>
                </div>
            </div>
        </div>



        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <?= $this->Html->script('bootstrap.min'); ?>
    </body>

    <script>

        var links = [];
        var blankWinA = new Array();
        var blankUrl = new Array();
        var blankWin = null;

        function downloadAll(urls) {
            console.log(urls);
            var link = document.createElement('a');
            link.style.display = 'none';
            document.body.appendChild(link);
            for (var i = 0; i < urls.length; i++) {
                link.setAttribute('download', urls[i].name);
                link.setAttribute('href', urls[i].url);
                link.click();
            }

            document.body.removeChild(link);
        }

        $(document).ready(function () {

            //Download button
            $('#btn-download').click(function () {
                $('.checkbox-download').each(function () {
                    if ($(this).is(":checked")) {
                        var item = [];
                        item.name = $(this).attr('data-name');
                        item.url = $(this).attr('data-url');
                        links.push(item);
                    }
                });
                downloadAll(links);
            });
            //Popupmodal 
            $('.imageRoute').click(function () {
                $('.modal-body .img-preview').attr('src', $(this).attr('src'));
                $('.modal-footer').html($(this).parent().parent().parent().parent().find('.comment').html());
                $('#myModal').modal({show: true});
            });

            /* open new windows Survey */
            $(document).on("click", ".target", function (e) {
                e.preventDefault();
                var sTarget = $(this).attr('rel');
                console.log(sTarget);
                if ($(this).hasClass('targetOnly')) {
                    showTarget(sTarget);
                }
            });

        });
        /* open new windows Survey */

        function showTarget(sTarget) {
            var checkUrl = true;
            $.each(blankUrl, function (indexUrl, valueUrl) {
                if (sTarget === valueUrl) {
                    $.each(blankWinA, function (index, value) {
                        if (indexUrl === index) {
                            /* check exist windows */
                            if (value && !value.closed) {
                                value.focus();
                                checkUrl = false;
                            } else {
                                blankWin = window.open(sTarget, "_blank");
                                blankWinA.push(blankWin);
                                blankUrl.push(sTarget);
                                blankUrl.splice(indexUrl, 1);
                                blankWinA.splice(index, 1);
                                checkUrl = false;
                            }
                        }
                    });
                }
            });
            if (checkUrl == true) {
                blankWin = window.open(sTarget, "_blank");
                blankWinA.push(blankWin);
                blankUrl.push(sTarget);
            }
        }
        function load_more(offset) {
            $.ajax({
                url: '<?= $this->Url->build(["controller" => "surveys", "action" => "loadMore"]); ?>',
                dataType: "json",
                data: {
                    offset: offset,
                    filter: $('#checkFilterLoad').val(),
                    type_filter: $('#checkTypeFilterLoad').val()
                },
                type: "post",
                async: false,
                beforeSend: function () {

                },
                success: function (result) {
                    if (result.status) {
                        $('#list_records').append(result.html);
<?php if (!empty($id)) : ?>
                            var id = <?= $id ?>;
                            $('#' + id).css('background-color', '#f1f1f1');
<?php endif; ?>
                        $('.showMap').on('click', function (event) {
                            event.preventDefault();
                            var id = $(this).attr('data-bind');
                            window.location = '<?= $this->request->webroot ?>surveys/index/' + id;
                        });
                    } else {
                        $('#load-more').hide();
                    }
                }
            });
        }

        $(function () {
            $("#load-more").click(function () {
                var offset = $(this).attr('data-id');
                offset = parseInt(offset) + 10;
                load_more(offset);
                $(this).attr('data-id', offset);

                /* fix image to center*/
                fixHeightShowDataDiv();
            });
<?php if (!empty($id)) : ?>
                var id = <?= $id ?>;
                $('#' + id).css('background-color', '#f1f1f1');
<?php endif; ?>
            $('.showMap').on('click', function (event) {
                event.preventDefault();
                var id = $(this).attr('data-bind');
                window.location = '<?= $this->request->webroot ?>surveys/index/' + id;
            });

            /* fix image to center*/
            fixHeightShowDataDiv();
        })
        /* open new windows Survey */

        function showTarget(sTarget) {
            var checkUrl = true;
            $.each(blankUrl, function (indexUrl, valueUrl) {
                if (sTarget === valueUrl) {
                    $.each(blankWinA, function (index, value) {
                        if (indexUrl === index) {
                            /* check exist windows */
                            if (value && !value.closed) {
                                value.focus();
                                checkUrl = false;
                            } else {
                                blankWin = window.open(sTarget, "_blank");
                                blankWinA.push(blankWin);
                                blankUrl.push(sTarget);
                                blankUrl.splice(indexUrl, 1);
                                blankWinA.splice(index, 1);
                                checkUrl = false;
                            }
                        }
                    });
                }
            });
            if (checkUrl == true) {
                blankWin = window.open(sTarget, "_blank");
                blankWinA.push(blankWin);
                blankUrl.push(sTarget);
            }
        }
    </script>

    <style>
        .modal-dialog {width:800px;}

    </style>

</html>