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
        <h3 style="margin-top: 0">&nbsp;</h3>
    </div>
    <div class="pull-right mr20">
        <?= $this->Html->link(__('Register "NEW LANGUAGE APP"'), ['controller' => 'users', 'action' => 'registerAppLanguage'], ['class' => 'btn btn-success']); ?>
    </div>
</div>    
<?= $this->Flash->render('editAppLanguageDetail') ?>
<?= $this->Flash->render('registerAppLanguage') ?>
<?= $this->Flash->render('editAppLanguage') ?>
<div class="table-responsive mt20 ml20 mr20">
    <table class="top15 table table-striped table-bordered table-version">
        <thead>
            <tr>
                <th><?= __('No') ?></th>
                <th><?= __('Language') ?></th>
                <th><?= __('Translate Language') ?></th>
                <th><?= __('Publish') ?></th>
                <th><?= __('Language Edit') ?></th>
                <th><?= __('User Guide Edit') ?></th>
                <th><?= __('GPS Improvement Edit') ?></th>

            </tr>
        </thead>
        <tbody>                
            <?php foreach ($arrayLanguages as $key => $datum): ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    
                    <td><?= $this->Html->link(h($datum->language), ['controller' => 'Users', 'action' => 'editAppLanguage', $datum->id], ['class' => 'app_language']); ?></td>
                    <td style="width: 18%"><?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'translateLanguage', $datum->id], ['class' => 'app_language']); ?></td>
                    <td><?php
                        if ($datum->publish_flg)
                            echo __('NO');
                        else
                            echo __('YES');
                        ?></td>
                    <td style="width: 18%"><?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'editAppLanguageDetail', $datum->id], ['class' => 'app_language']); ?></td>
                    <td style="width: 18%"><?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'listGuide', 2, $datum->id], ['class' => 'app_language']); ?></td>
                    <td style="width: 18%"><?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'listGuide', 1, $datum->id], ['class' => 'app_language']); ?></td>
                </tr>
            <?php endforeach; ?>                              
        </tbody>                
    </table>
</div>
<div class="col-md-6">
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
    });
</script>