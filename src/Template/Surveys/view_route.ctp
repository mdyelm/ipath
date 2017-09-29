<div id="container">
    <div id="content-left">
        <div class="fix-menu" data-toggle="collapse" data-target="#menuDropdown">
            <span class="glyphicon glyphicon-menu-hamburger buttom-menu" aria-hidden="true"></span>
            <div class="image-version">
                <?php echo $this->Html->image('icon-menu.png', ['class' => 'fix-img-index']); ?>
                <p class="version-index"><?= h('Version' . ' ' . $versionWeb); ?> </p>
            </div>
        </div>
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
                    ]);
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
            <a href="<?php echo $this->Url->build(['controller' => 'Surveys', 'action' => 'viewRoute', $id, "?" => ["filter" => "user", 'type' => $typeUser]]); ?>">
                <div class="filterUser">
                    <span>
                        <?= __('USER') ?>
                    </span>
                    <?php echo $this->Html->image($imageUser, ['class' => 'fix-img-dataGps']); ?>
                </div>
            </a>
            <a href="<?php echo $this->Url->build(['controller' => 'Surveys', 'action' => 'viewRoute', $id, "?" => ["filter" => "id", 'type' => $typeId]]); ?>">
                <div class="filterId">
                    <span>
                        <?= __('SURVEY ID') ?>
                    </span>
                    <?php echo $this->Html->image($imageId, ['class' => 'fix-img-dataGps']); ?>
                </div>
            </a>
            <a href="<?php echo $this->Url->build(['controller' => 'Surveys', 'action' => 'viewRoute', $id, "?" => ["filter" => "date", 'type' => $typeDate]]); ?>">
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
                <div class="item mb10 ov" id="<?= $arrayIndexReverse[$route['id']] ?>">
                    <div class="col-lg-8 showMap" data-bind="<?= $arrayIndexReverse[$route['id']] ?>">
                        <?php if ($route['Users']['del_flg']) : ?>
                            <span><?= __('DELETED USER') ?>: </span><?php echo $route['Users']['username']; ?><br>
                        <?php else : ?>
                            <span><?= __('USER') ?>: </span><?php echo $route['Users']['username']; ?><br>
                        <?php endif; ?>
                        <span><?= __('Device ID') ?>: </span><?= $route['Devices']['name'] ?><br>
                        <span><?= __('SURVEY ID') ?>: </span><?php echo $arrayIndexReverse[$route['id']]; ?><br>
                        <span><?= __('DATE') ?>: </span><?= date('Y/m/d', strtotime($route['time_start'])) ?><br>
                        <span><?= __('LOCATION') ?>: </span><?= $route['country'] ?>
                    </div>
                    <div class="col-lg-4 showData">
                        <?php if ($route['id'] == $id): ?>
                            <a class="targetOnly" rel="<?= $this->Url->build(["controller" => "surveys", "action" => "viewRoute", $arrayIndexReverse[$route['id']]]) ?>">
                                <?= $this->App->getImageFirst($route['id']); ?>
                            </a>
                        <?php else: ?>
                            <?php
                            $url = $this->Url->build(["controller" => "surveys", "action" => "viewRoute", $arrayIndexReverse[$route['id']]]);
                            if (!empty($filter) && !empty($type_filter)) {
                                $url .= '?filter=' . $filter . '&type=' . $type_filter;
                            }
                            ?>
                            <a class="target targetOnly" rel="<?= $url ?>">
                                <?= $this->App->getImageFirst($route['id']); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="clear-fix"></div>
            <?php endforeach; ?>
        </div>
        <?php if ($routes->count() > 10): ?>
            <button class="btn-primary pull-right" id="load-more" data-id="<?= $offset ?>"><?= __('Load More') ?></button>
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
            <div class="title-top">
                <?php
                echo $this->Html->image('icon-home05.png', ['class' => 'image-home',
                    'url' => [
                        'controller' => 'surveys',
                        'action' => 'index',
                ]]);
                ?>
                <span><?= __('Survey Details') ?></span></div>

            <div class="table-responsive">
                <?= $this->Flash->render() ?>
                <table class="table table-striped table-bordered fix-table" id="detail_route" align="center">
                    <tbody>
                        <tr>
                            <td rowspan="9" class="col-lg-2">
                                <?= __('Survey Content') ?>
                            </td>
                            <td class="col-lg-3" > 
                                <?php if ($route_rd['Users']['del_flg']) : ?>
                                    <?= __('DELETED USER') ?>
                                <?php else : ?>
                                    <?= __('USER') ?>
                                <?php endif; ?>
                            </td>
                            <td class="col-lg-4" > 
                                <?php if ($route_rd['Users']['del_flg']) : ?>
                                    <?php echo $route_rd['Users']['username']; ?>
                                <?php else : ?>
                                    <?php echo $route_rd['Users']['username']; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td> 
                                <?= __('Device ID') ?>
                            </td>
                            <td>
                                <?= $route_rd['Devices']['name'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td > 
                                <?= __('SURVEY') ?>
                            </td>
                            <td >
                                <?= $this->App->getRouteIndex($arrayIndex, $route_rd['id']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td > 
                                <?= __('DATE') ?>
                            </td>
                            <td >
                                <?php
                                if (!empty($route_rd['time_start'])) {
                                    ?>
                                    <?= $route_rd['time_start']->i18nFormat('yyyy/MM/dd HH:mm:ss') . ' - ' . $route_rd['time_end']->i18nFormat('yyyy/MM/dd HH:mm:ss'); ?>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td > 
                                <?= __('START POSITION') ?>
                            </td>
                            <td >
                                <?= $this->App->convertDecimalToSexagesimal($route_rd['LocationsMin']['longitude']) . ' / ' . $this->App->convertDecimalToSexagesimal($route_rd['LocationsMin']['latitude']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td > 
                                <?= __('START LOCATION') ?>
                            </td>
                            <td > 
                                <?= $route_rd['country'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td > 
                                <?= __('END POSITION') ?>
                            </td>
                            <td >                                
                                <?= $this->App->convertDecimalToSexagesimal($route_rd['Locations']['longitude']) . ' / ' . $this->App->convertDecimalToSexagesimal($route_rd['Locations']['latitude']); ?>                                   
                            </td>
                        </tr>
                        <tr>
                            <td > 
                                <?= __('END LOCATION') ?>
                            </td>
                            <td > 
                                <?= $route_rd['last_address'] ?>
                            </td>
                        </tr>
                        <tr>
                            <td > 
                                <?= __('NUMBER OF PHOTOS') ?>
                            </td>
                            <td > 
                                <?= $route_rd['cnt'] . ' Pic' ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php if (isset($authUser['admin_flg']) && $authUser['admin_flg'] == 1) { ?>
                <div class="ov mt10 mb20 text-center div-btn-route">
                    <button class="btn-route btn" id="btn-editGps"><?= __('Correct GPS data') ?></button>
                </div>
            <?php } ?>
            <div class="viewImage ov">
                <?php foreach ($images as $key => $img) : ?>
                    <?php
                    if ($img['Locations']['catch_time'] != '') {
                        $time = str_replace('-', '/', $img['Locations']['catch_time']);
                    } else {
                        $time = $img['created']->i18nFormat('yyyy/MM/dd HH:mm:ss');
                    }
                    ?>
                    <div class="col-lg-6 fix-pd">
                        <table class="table table-striped table-bordered icon-table">
                            <tr>
                                <td colspan="2" align="center">
                                    <div class="viewRouteLoadImageDiv">
                                        <?php echo $this->Html->image('/files/image/' . $route_rd['id'] . '/' . $img['name'], array('data-width' => $img['width'], 'data-height' => $img['height'], 'style' => 'opacity:0')) ?>
                                        <input type="checkbox" name="" class="checkbox-download" data-id="<?= $img['id'] ?>" data-url="<?= '/files/image/' . $route_rd['id'] . '/' . $img['name']; ?>" data-name="<?= $img['name']; ?>"/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><?= __('DATE') ?></td>
                                <td><?= $time; ?></td>
                            </tr>
                            <tr>
                                <td><?= __('LATITUDE') ?></td>
                                <td>
                                    <div class="coordinate">
                                        <?= $this->App->convertDecimalToSexagesimal($img['Locations']['latitude']) ?>
                                    </div>    
                                </td>
                            </tr>
                            <tr>
                                <td><?= __('LONGTITUDE') ?></td>
                                <td>
                                    <div class="coordinate">
                                        <?= $this->App->convertDecimalToSexagesimal($img['Locations']['longitude']) ?>
                                    </div>    
                                </td>
                            </tr>
                            <tr>
                                <td><?= __('DIRECTION') ?></td>
                                <td><?= $this->App->changeDirectionByRotation($img['rotation']) ?></td>
                            </tr>
                            <tr>
                                <td><?= __('IMAGE SIZE') ?></td>
                                <td><?= number_format($img['width']) . ' X ' . number_format($img['height']) . ' px' ?></td>
                            </tr>
                            <tr>
                                <td><?= __('FILE SIZE') ?></td>
                                <td><?= number_format($img['size'] / (1024 * 1024), 1) . ' MB' ?></td>
                            </tr>
                            <tr>
                                <td><?= __('COMMENT') ?></td>
                                <td class="comment"><?= $img['comment']; ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="ov mt10 mb20 text-center">
                <button class="btn-primary btn btn-hidden" id="btn-removeall"><?= __('Remove All') ?></button>
                <button class="btn-primary btn" id="btn-checkall"><?= __('Check All') ?></button>
                <button class="btn-primary btn" id="btn-download"><?= __('Download photos') ?></button>
                <button class="btn-primary btn" id="btn-create-album"><?= __('Create a photo book') ?></button>
                <button class="btn-primary btn" id="btn-dowload-pdf"><?= __('Download PDF') ?></button>
            </div>
        </div>
    </div>
</div>

<div tabindex="-1" class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <!--            <div class="modal-header">
                            <button class="close" type="button" data-dismiss="modal">×</button>
                            <h3 class="modal-title"></h3>
                        </div>-->
            <div class="modal-body">
                <img src="" class="img-preview img-responsive"/>
            </div>
            <div class="modal-footer text-left">
                <!--<button class="btn btn-default" data-dismiss="modal">Close</button>-->
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.3/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.3/jquery-confirm.min.js"></script>
<script>
    $('#btn-editGps').confirm({
        title: "<?=__('Confirm')?>",
        content: "<?=__('Are you sure to correct the GPS data?')?>",
        buttons: {
            ok: {
                text: "<?=__('YES')?>",
                action: function () {
                    window.location.href = '<?php echo $this->Url->build(['controller' => 'Users', 'action' => 'editgps', $id]); ?>';
                }
            },
            cancel: {text: "<?=__('NO')?>"}
        }
    });
    var links = [];
    var scrollId = '<?= $arrayIndexReverse[$route_rd['id']]; ?>';
    var heightViewRouteLoadImageDiv = 300, widthViewRouteLoadImageDiv = 400;
    var blankWinA = new Array();
    var blankUrl = new Array();
    var blankWin = null;

    /**
     * Scroll to current survey 
     * @returns {undefined}
     */
    function scrollAtLeftSideBar() {
        if (scrollId) {
            $('#list_records').animate({scrollTop: $('#' + scrollId).offset().top - 50});
        }
    }

    /**
     * Calculate width coordinate for fix width
     * @returns {undefined}
     */
    function calculateWidthCoordinate() {
        var check = false;
        $('.coordinate').each(function () {
            if ($(this).height() > 40) {
                check = true;
                return false;
            }
        })
        if (check) {
            var width = parseInt($('.coordinate').css('width')) + 5;
            $('.coordinate').css('width', width + 'px');
            calculateWidthCoordinate();
        }
    }

    /**
     * Download image by javascript
     * @param {type} urls
     * @returns {undefined}
     */
    function downloadAll(urls) {
        $.ajax({
            type: "POST",
            dataType: "json",
            data: {link: urls, route: <?= $route_rd['id']; ?>},
            url: '<?= $this->Url->build(["controller" => "api", "action" => "saveImageDownload"]); ?>',
            success: function (msg) {
                if (msg.status == 1) {
                    var link = document.createElement('a');
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.setAttribute('download', msg.name);
                    link.setAttribute('href', msg.link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
//        var link = document.createElement('a');
//        link.style.display = 'none';
//        document.body.appendChild(link);
//        for (var i = 0; i < urls.length; i++) {
//            link.setAttribute('download', urls[i].name);
//            link.setAttribute('href', urls[i].url);
//            link.click();
//        }
//
//        document.body.removeChild(link);
        });
    }

    $(document).ready(function () {
        calculateWidthCoordinate();
        //Download button
        $('#btn-download').click(function () {
            var links = [];
            var first = false;
            $('.checkbox-download').each(function () {
                if ($(this).is(":checked")) {
//                    var item = [];
//                    item.name = $(this).attr('data-name');
//                    item.url = $(this).attr('data-url');
//                    links.push(item);
                    first = true;
                    links.push($(this).attr('data-name'));
                }
            });
            if (first) {
                downloadAll(links);
            } else {
                alert('<?php echo __('Please check the photos you want to download or create a photo book'); ?>');
            }
        });
        //Popupmodal 
        $('.imageRoute').click(function () {
            $('.modal-body .img-preview').attr('src', $(this).attr('src'));
            $('.modal-footer').html($(this).parent().parent().parent().parent().find('.comment').html());
            $('#myModal').modal({show: true});
        });

        //load image
        widthViewRouteLoadImageDiv = $('.viewRouteLoadImageDiv').width();
        changeLoadImageSize(widthViewRouteLoadImageDiv, heightViewRouteLoadImageDiv, '.viewRouteLoadImageDiv img')
    });

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

        $('#btn-create-album').on('click', function () {
            var param_string = '';
            var first = true;
            $('.checkbox-download').each(function () {
                if ($(this).is(":checked")) {
                    if (first) {
                        first = false;
                        param_string += '?id_array[]=' + $(this).attr('data-id');
                    } else {
                        param_string += '&id_array[]=' + $(this).attr('data-id');
                    }
                }
            });
            console.log(param_string);
            if (param_string == '') {
                alert('<?php echo __('Please check the photos you want to download or create a photo book'); ?>');
            } else {
                window.location = '<?= $this->request->webroot ?>surveys/createExcel/' + id + param_string;

            }
//            window.location = '<?= $this->request->webroot ?>surveys/createExcel/' + id;
        });
        $('#btn-dowload-pdf').on('click', function () {
            var param_string = '';
            var first = true;
            $('.checkbox-download').each(function () {
                if ($(this).is(":checked")) {
                    if (first) {
                        first = false;
                        param_string += '?id_array[]=' + $(this).attr('data-id');
                    } else {
                        param_string += '&id_array[]=' + $(this).attr('data-id');
                    }
                }
            });
            if (param_string == '') {
                alert('<?php echo __('Please check the photos you want to download or create a photo book'); ?>');
            } else {
                var urlPdf = '<?php echo $this->Url->build(['controller' => 'Pdfs', 'action' => 'download']) . '/' . $id . '.pdf'; ?>' + param_string;
                //console.log(urlPdf);
                window.location = urlPdf;
            }
//            window.location = '<?= $this->request->webroot ?>surveys/createExcel/' + id;
        });

        $('#btn-checkall, #btn-removeall').on('click', function () {
            var id = this.id;
            $(this).addClass('btn-hidden');
            $('.checkbox-download').click();
            if (id == 'btn-checkall') {
                $('.viewRouteLoadImageDiv input').prop('checked', true);
                $('#btn-removeall').removeClass('btn-hidden');
            } else {
                $('.viewRouteLoadImageDiv input').prop('checked', false);
                $('#btn-checkall').removeClass('btn-hidden');
            }
        });

        /* fix image to center*/
        fixHeightShowDataDiv();

        /*scroll to element survey at left side bar */
        scrollAtLeftSideBar();
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
</script>

<style>
    .modal-dialog {width:800px;}

</style>