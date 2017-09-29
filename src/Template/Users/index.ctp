<div class="overflow">
    <div class="title-top mb20">
        <?php
        echo $this->Html->image('icon-home05.png', ['class' => 'image-home',
            'url' => [
                'controller' => 'surveys',
                'action' => 'index',
        ]]);
        ?>
        <span><?= __('User List') ?></span></div>
    <?= $this->Form->create(null, ['type' => 'get']); ?>
    <div class="col-md-4">
        <?= $this->Form->input('name', [ 'value' => $name, 'class' => 'form-control', 'id' => 'inputName', 'placeholder' => __('Please input username or email')]); ?>
    </div>
    <div class="col-md-1">
        <?= $this->Form->button(__('Search'), ['class' => 'btn btn-default']); ?>
    </div>
    <?= $this->Form->end(); ?>
    <div class="pull-right mr20">
        <?= $this->Html->link(__('Register "NEW USER"'), ['controller' => 'users', 'action' => 'add'], ['class' => 'btn btn-success']); ?>
    </div>
</div>
<div class="table-responsive mt20 ml20 mr20">
    <table class="top15 table table-striped table-bordered">
        <thead>
            <tr>
                <th class="text-center"><?= __('User No') ?></th>
                <th class="text-center"><?= __('Registered Date') ?></th>
                <th class="text-center"><?= __('Username') ?></th>
                <th class="text-center"><?= __('Company Email') ?></th>
                <th class="text-center"><?= __('Gmail') ?></th>
                <th class="text-center"><?= __('App-Web') ?></th>
                <th class="text-center"><?= __('Types') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($checkNo)) {
                $cnt = $cnt - ($checkNo * 20 - 20);
            }
            ?>
            <?php foreach ($results as $key => $result) : ?>
                <tr>
                    <th scope="row" class="text-center"><?= h($cnt); ?></th>
                    <td class="text-center"><?= $result['created']->i18nFormat('yyyy/MM/dd'); ?><br><?= $result['created']->i18nFormat('HH:mm'); ?></td>
                    <td class="text-center">
                        <?=
                        $this->Html->link(($result['username']), [
                            'controller' => 'users',
                            'action' => 'edit',
                            $result['id']
                        ]);
                        ?>
                    </td>
                    <td class="text-center">
                        <a href="mailto:<?= $result['company_email']; ?>"><?= $result['company_email']; ?></a>
                    </td>
                    <td class="text-center">
                        <a href="mailto:<?= $result['email']; ?>"><?= $result['email']; ?></a>
                    </td>
                    <td class="text-center">
                        <?php
                        if (!empty($result['app_web_flg']))
                            echo __('App+Web');
                        else
                            echo __('Web')
                            ?>
                    </td>
                    <td class="text-center"><?php
                        if ($result['admin_flg'])
                            echo __('Admin');
                        else
                            echo __('User')
                            ?></td>
                </tr>
                <?php $cnt--; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="text-center">
    <ul class="pagination">
        <?php echo $this->Paginator->first('«'); ?>
        <?php echo $this->Paginator->numbers(['modulus' => 5]); ?>
        <?php echo $this->Paginator->last('»'); ?>
    </ul>
</div>