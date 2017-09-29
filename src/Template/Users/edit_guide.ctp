<?php

use Cake\Routing\Router; ?>
<?php if ($type == 1): ?>
    <?php
    $nameEdit = __('GPS Improvement Edit');
    ?>
<?php elseif ($type == 2): ?>
    <?php
    $nameEdit = __('User Guide Edit');
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
    <div class="col-md-4 ml20">
        <h3 style="margin-top: 0"><?= $nameEdit ?></h3>
    </div>
    <div class="col-md-2"></div>
    <div class="col-md-10 col-md-push-1 mt55">
        <?= $this->Form->create($editGuide, ['autocomplete' => 'off', 'type' => 'file']); ?>
        <?= $this->Form->input('id', ['type' => 'hidden', 'value' => $id]); ?>
        <div class = "table-responsive">
            <table class="top15 table table-striped table-bordered table-register-guide">
                <thead>
                    <tr>
                        <th><?= __('Title') ?></th>
                        <th><?= __('Image') ?></th>
                    </tr>
                </thead>
                <tbody>                
                    <tr>
                        <td class="w50p vertical-guide">
                            <?= $this->Form->input('guide_text', array('type' => 'text', 'class' => 'title-guide form-control nospace')) ?>
                        </td>
                        <td class="w50p upload-guide">
                            <?php if (!empty($editGuide['image'])): ?>
                                <?php
                                echo $this->Html->image('/files/slide-app/' . $editGuide['image'], ['class' => 'img image-guide', 'style' => 'margin-bottom:10px']);
                                ?>
                            <?php else: ?>
                                <?php
                                echo $this->Html->image('register', ['class' => 'img image-guide', 'style' => 'display:none']);
                                ?>
                            <?php endif; ?>
                            <?= $this->Form->input('image', array('type' => 'file', 'style' => 'display:none', 'class' => 'file_upload')) ?>
                            <?php echo $this->Form->button(__('Upload Image'), ['type' => 'button', 'class' => 'btn btn-info btn-sm', 'id' => 'openImgUpload']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-center">
                            <?=
                            $this->Html->link(__('Cancel'), [
                                'controller' => 'Users',
                                'action' => 'listGuide', $type, $editGuide['language_id']
                                    ], ['class' => 'btn btn-default']);
                            ?>
                            <button class="btn btn-primary" id="btn-manga-edit"><?= __('OK') ?></button>
                        </td>
                    </tr>
                </tbody>                
            </table>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="col-md-6">
    <!--        <table class="top15 table table-bordered">
            <tr>
                <td class="text-center">
    
                </td>
            </tr>
        </table>-->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        linkImg = $('.img').attr('src');
        console.log(linkImg);
        //upload img 
        $('#openImgUpload').click(function (e) {
            $(".file_upload").click();
            e.preventDefault();
        });
        $('.file_upload').change(function () {
            var id = $(this).attr('data-id');
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.img').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                $('.img').attr('src', linkImg);
            }
            $('.image-guide').css({'display': 'block', 'margin-bottom': '10px'});
        });
    });

</script>