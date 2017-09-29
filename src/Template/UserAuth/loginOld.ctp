<h1 class="mt55 mb20">Survey Manager</h1>
<?= $this->Form->create(null, ['class' => 'form-signin mb15', 'novalidate', 'autocomplete' => 'off']) ?>
<div class="form-group">
    <label>ユーザー名*</label>
    <?= $this->Form->input('username', [ 'class' => 'form-control', 'value' => $usernameDefault]); ?>
</div>
<div class="form-group">
    <label>パスワード*</label>
    <?= $this->Form->input('password', ['type' => 'password', 'value' => $passwordDefault, 'class' => 'form-control', 'autocomplete' => 'new-password']); ?>
</div>
<div class="form-group">
    <label>次回ログイン時にユーザーIDを表示する。</label>
    <?= $this->Form->checkbox('remember_me', ['class' => 'pull-right']) ?>
</div>
<div class="form-group text-center">
    <?= $this->Form->button('ログイン', ['class' => 'btn btn-lg btn-success']); ?>
</div>
<?= $this->Form->end(); ?>
<?= $this->Flash->render() ?>