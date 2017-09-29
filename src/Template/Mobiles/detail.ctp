<div id="container" class="mobile-route">
    <div id="content-right" style="display:block;height: auto" class="ov">
        <div id="content-right-inner">
            <div class="title-top">
                <?php
                echo $this->Html->image('icon-home05.png', ['class' => 'image-home',
                    'url' => [
                        'controller' => 'mobiles',
                        'action' => 'index' . '?token=' . $token
                ]]);
                ?>
                <span><?= __('Survey Details') ?></span></div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered fix-table" id="detail_route">
                    <tbody>
                        <tr>
                            <td class="col-xs-3 col-sm-3 col-md-3 col-lg-3" > 
                                <?php if ($route_rd['Users']['del_flg']) : ?>
                                    <?= __('DELETED USER') ?>
                                <?php else : ?>
                                    <?= __('USER') ?>
                                <?php endif; ?>
                            </td>
                            <td class="col-xs-4 col-sm-4 col-md-4 col-lg-4" > 
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
                                <?= $route_rd['time_start']->i18nFormat('yyyy/MM/dd HH:mm:ss') . ' - ' . $route_rd['time_end']->i18nFormat('yyyy/MM/dd HH:mm:ss'); ?>
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
                        <tr class="trLastDetail">
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
            <div class="viewImage ov">
                <?php foreach ($images as $key => $img) : ?>
                    <?php
                    if ($img['Locations']['catch_time'] != '') {
                        $time = str_replace('-', '/', $img['Locations']['catch_time']);
                    } else {
                        $time = $img['created']->i18nFormat('yyyy/MM/dd HH:mm:ss');
                    }
                    ?>
                    <div class="col-lg-6 fix-pd col-lg-offset-3">
                        <table class="table table-striped table-bordered icon-table">
                            <tr>
                                <td colspan="2" align="center">
                                    <div class="viewRouteLoadImageDivMobile">
                                        <?php echo $this->Html->image('/files/image/' . $id . '/' . $img['name'], array('data-width' => $img['width'], 'data-height' => $img['height'])) ?>
                                        <!--<input type="checkbox" name="" class="checkbox-download" data-id="<?= $img['id'] ?>" data-url="<?= '/files/image/' . $id . '/' . $img['name']; ?>" data-name="<?= $img['name']; ?>"/>-->
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

<script>

    var links = [];
    var heightViewRouteLoadImageDiv = 300, widthViewRouteLoadImageDiv = 400;

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
        //console.log(urls);
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
        calculateWidthCoordinate();
        //Download button
        $('#btn-download').click(function () {
            var first = false;
            $('.checkbox-download').each(function () {
                if ($(this).is(":checked")) {
                    var item = [];
                    item.name = $(this).attr('data-name');
                    item.url = $(this).attr('data-url');
                    links.push(item);
                    first = true;
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
    $(function () {
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
            //console.log(param_string);
            if (param_string == '') {
                alert('<?php echo __('Please check the photos you want to download or create a photo book'); ?>');
            } else {
                window.location = '<?= $this->request->webroot ?>surveys/createExcel/' + id + param_string;
            }
//            window.location = '<?= $this->request->webroot ?>surveys/createExcel/' + id;
        });

        $('#btn-checkall, #btn-removeall').on('click', function () {
            var id = this.id;
            $(this).addClass('btn-hidden');
            $('.checkbox-download').click();
            if (id == 'btn-checkall') {
                $('#btn-removeall').removeClass('btn-hidden');
            } else {
                $('#btn-checkall').removeClass('btn-hidden');
            }
        });

        /* fix image to center*/
        fixHeightShowDataDiv();

    })
</script>

<style>
    .modal-dialog {width:800px;}

</style>