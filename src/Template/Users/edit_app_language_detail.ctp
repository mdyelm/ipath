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
        <h3 style="margin-top: 0"><?= __('App Language Edit') ?></h3>
    </div>
    <!--    <div class="col-md-1">
            <h3 style="margin-top: 0"><?= $version ?></h3>
        </div>  -->
</div>
<?= $this->Form->create(null, ['id' => 'formEdit', 'class' => 'formEditScroll', 'novalidate' => 'novalidate']); ?>
<div class="table-responsive mt20 ml20 mr20" id="div-scroll-table-language">
    <table class="top15 table table-striped table-bordered" id="scroll-table-language">
        <thead>
            <tr>
                <th width="5%"><?= __('No') ?></th>
                <th width="15%"><?= __('Key') ?></th>
                <?php if ($id == 1): ?>
                    <th width="80%"><?= h($result->language) ?></th>
                <?php else: ?>
                    <th width="40%"><?= __('English') ?></th>
                    <th width="40%"><?= h($result->language) ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>                
            <?php foreach ($arrayKey as $key => $value): ?>
                <tr>
                    <td><?= $key ?></td>
                    <td><?= h($value) ?></td>
                    <?php if ($id == 1): ?>
                        <td>
                            <?php if (isset($arrayValue[$key])): ?>
                                <?= $this->Form->input('data[' . $key . '][id]', ['class' => 'form-control', 'default' => $arrayValue[$key][0], 'type' => 'hidden']); ?>
                                <?= $this->Form->input('data[' . $key . '][value]', ['class' => 'form-control', 'default' => $arrayValue[$key][1]]); ?>
                            <?php else: ?>
                                <?= $this->Form->input('data[' . $key . '][value]', ['class' => 'form-control']); ?>
                            <?php endif; ?>
                        </td>
                    <?php else: ?>
                        <td>
                            <?php if (isset($arrayEnglish[$key])): ?>
                                <?= h($arrayEnglish[$key][1]); ?>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <?php if (isset($arrayValue[$key])): ?>
                            <td>
                                <?= $this->Form->input('data[' . $key . '][id]', ['class' => 'form-control', 'default' => $arrayValue[$key][0], 'type' => 'hidden']); ?>
                                <?= $this->Form->input('data[' . $key . '][value]', ['class' => 'form-control', 'default' => $arrayValue[$key][1]]); ?>                                    
                            </td>
                        <?php else: ?>
                            <td><?= $this->Form->input('data[' . $key . '][value]', ['class' => 'form-control']); ?></td>
                        <?php endif; ?>
                    <?php endif; ?>                        
                </tr>
            <?php endforeach; ?>                   
        </tbody>                
    </table>
</div>
<table class="top15 table table-w-button">
    <tr>
        <td colspan="3" class="text-center">
            <?=
            $this->Html->link(__('Cancel'), [
                'controller' => 'Users',
                'action' => 'appLanguageVersion',
                    ], ['class' => 'btn btn-default']);
            ?>
            <button class="btn btn-primary" id="btn-submit"><?= __('OK') ?></button>
        </td>
    </tr>             
</table>
<?php echo $this->Form->end(); ?>
<div class="col-md-6">
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
    });
</script>