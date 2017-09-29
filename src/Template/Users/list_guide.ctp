<?php if ($type == 1): ?>
    <?php
    $nameLanguage = __('GPS Improvement Edit');
    ?>
<?php elseif ($type == 2): ?>
    <?php
    $nameLanguage = __('User Guide Edit');
    ?>
<?php endif; ?>
<div class="overflow">
    <div class="title-top mb20">
        <?php
        echo $this->Html->image('icon-home05.png', ['class' => 'image-home',
            'url' => [
                'controller' => 'surveys',
                'action' => 'index',
        ]]);
        ?>
        <span><?= __('App Setting') ?></span>
    </div>
    <div class="col-md-4">
        <h3 style="margin-top: 0"><?= h($nameLanguage); ?></h3>
    </div> 
    <div class="col-md-4 ml20">
        <h3 style="margin-top: 0">&nbsp;</h3>
    </div>
    <div class="pull-right mr20">
        <a target="_blank" href="<?php echo $this->Url->build(['controller' => 'Pages', 'action' => 'slideView', $language->language, $type]); ?>">
            <button class="btn btn-primary" id="btn-manga-edit"><?= __('SHOW') ?></button>
        </a>

    </div>
    <div class="pull-right mr20">
        <?= $this->Html->link(__('Add New Index'), ['controller' => 'users', 'action' => 'registerAppGuide', $type, $id], ['class' => 'btn btn-success']); ?>
    </div>
</div>  

<div class="table-responsive mt20 ml20 mr20">
    <table class="top15 table table-striped table-bordered table-list-guide">
        <thead>
            <tr>
                <th><?= __('No') ?></th>
                <th><?= __('Title') ?></th>
                <th><?= __('Image') ?></th>
                <th><?= __('Edit / Delete') ?></th>
            </tr>
        </thead>
        <tbody>         
            <?php foreach ($allGuide as $key => $value): ?>
                <tr>
                    <td style="width: 5%;text-align: center" data-bind="<?= h($value['id']); ?>"><?= h($key + 1); ?></td>
                    <td style="width: 45%"><?= h($value['guide_text']); ?></td>
                    <td style="width: 25%; text-align: center" class="img-list-guide" data-bind="<?php echo $type ?>">
                        <?php if (!$value['image']): ?>
                            <?= $this->Html->image('/files/slide-app/' . $value['image'], ['style' => 'display:none', 'class' => 'img-list']); ?>
                        <?php else: ?>
                            <?= $this->Html->image('/files/slide-app/' . $value['image'], ['class' => 'img-list']); ?>
                            <button type="button" class="btn btn-danger btn-default delete-image-guide" style="margin-top: 10px" data-toggle="modal" data-target="#myModal"><?= __('Delete Image') ?></button>
                        <?php endif; ?>
                    </td>

                    <td style="width: 25%" class="upload-guide">
                        <a href="<?php echo $this->Url->build(['controller' => 'users', 'action' => 'editGuide', $type, $value['id']]); ?>"><button class="btn btn-info edit-guide"><?= __('Edit') ?></button></a>
                        <!-- Trigger the modal with a button -->
                        <button type="button" class="btn btn-danger btn-default delete-guide" data-toggle="modal" data-target="#myModal"><?= __('Delete') ?></button>


                        <!-- Modal -->
                        <div id="myModal" class="modal fade" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title"><?= __('Delete Guide') ?></h4>
                                    </div>
                                    <div class="modal-body" style="height: 500px;">
                                        <?= $this->Html->image('/files/slide-app/' . $value['image'], ['class' => 'img-modal', 'style' => 'max-height:100%']); ?>
                                    </div>
                                    <div class="modal-footer" style="text-align: center">
                                        <button class="btn btn-default cancel-delete-guide"><?= __('Cancel') ?></button>
                                        <a href="<?php echo $this->Url->build(['controller' => 'users', 'action' => 'deleteGuide', $type, $value['id']]); ?>"><button class="btn btn-danger"><?= __('Delete') ?></button></a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>                
    </table>
</div>
<script>
    var link = "<?php echo $this->Url->build(['controller' => 'users', 'action' => 'deleteGuide', $type]); ?>/";
    var linkimg = "<?php echo $this->Url->build(['controller' => 'users', 'action' => 'deleteImageGuide', $type]); ?>/";

    $(document).ready(function () {
        $('.cancel-delete-guide').click(function (e) {
            e.preventDefault();
            $('.close').trigger("click");
        });
        $('.delete-guide').click(function () {
            var elementImg = $(this).parents('.upload-guide').prev();
            var id = elementImg.prev().prev().attr('data-bind');
            var type = elementImg.attr('data-bind');
            var src = elementImg.find('.img-list').attr('src');
            $('.modal-footer a').attr('href', link + id);
            if (type == 1) {
                $('.modal-title').text('<?= __('Delete GPS Improvement') ?>');
            } else {
                $('.modal-title').text('<?= __('Delete Guide') ?>');
            }
            $('.img-modal').attr('src', src);
        });
        // delete image 
        $('.delete-image-guide').click(function () {
            var elementImg = $(this).parents('.img-list-guide');
            var id = elementImg.prev().prev().attr('data-bind');
            var type = elementImg.attr('data-bind');
            var src = elementImg.find('.img-list').attr('src');
            $('.modal-footer a').attr('href', linkimg + id);
            if (type == 1) {
                $('.modal-title').text('<?= __('Delete Image GPS Improvement') ?>');
            } else {
                $('.modal-title').text('<?= __('Delete Image Guide') ?>');
            }
            $('.img-modal').attr('src', src);
        });
    });
</script>