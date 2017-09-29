<p> <?=__('Inquiry to the Administrator & Developer')?></p>
<span><?=__('Copyright © CHODAI CO.,LTD. , 2016 All Rights Reserved.')?></span>

<div class="col-xs-5 login-left">
    <?php echo $this->Html->image('materials.png', ['class' => 'img-materials']); ?>
</div>
<div class="headMenu">
    <p class="languageChoice">
        <?= $this->Html->link('English', ['controller' => 'pages', 'action' => 'language', '?' => ['language' => 'en_US']]) ?> ｜
        <?= $this->Html->link('日本語', ['controller' => 'pages', 'action' => 'language', '?' => ['language' => 'ja_JP']]) ?>
    </p>
</div>
<div class="col-xs-7">
    <div class="fix-login">
        <h2>"<?=__('Web Operation Assistant')?>"</h2>
        <?= $this->Form->create(null, ['class' => 'form-signin mb15', 'novalidate', 'autocomplete' => 'off']) ?>
        <div class="form-group form-group-fix">
            <label><?=__('User')?></label>
            <?= $this->Form->input('username', [ 'class' => 'form-control', 'value' => $usernameDefault]); ?>
        </div>
        <div class="form-group form-group-fix">
            <label><?=__('Password')?></label>
            <?= $this->Form->input('password', ['type' => 'password', 'value' => $passwordDefault, 'class' => 'form-control', 'autocomplete' => 'new-password']); ?>
        </div>
        <div class="remember">
            <label><?=__('Remember USE ID & Password')?></label>
            <?= $this->Form->checkbox('remember_me', ['class' => 'checkbox-login']) ?>
            <?= $this->Flash->render() ?>

        </div>
        <div class="form-group text-center">
            <?= $this->Form->button(__('LOGIN'), ['class' => 'button-submit-login']); ?>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>