<div class="overflow">
    <div class="title-top">
        <?php
        echo $this->Html->image('icon-home05.png', ['class' => 'image-home',
            'url' => [
                'controller' => 'surveys',
                'action' => 'index',
        ]]);
        ?>
        <span><?=__('User Basic Information')?></span></div>
    <div class="col-md-2"></div>
    <div class="col-md-8 mt55">
        <?= $this->Form->create($result, ['id' => 'formEdit', 'autocomplete' => 'off', 'novalidate' => 'novalidate']); ?>
        <?= $this->Form->input('id', ['type' => 'hidden']); ?>
        <?= $this->Flash->render() ?>
        <div class = "table-responsive">
            <table class = "top15 table table-bordered">
                <!--<tr><th class = "active text-center" colspan = "2">&nbsp;</th></tr>-->
                <tr>
                    <th class = "active text-center w165" style="width:150px;"><?=__('User No')?></th>
                    <td class = "text-center"><?= $userIndex; ?></td>
                </tr>
                <tr>
                    <th class="active text-center"><?=__('Username')?></th>
                    <td><?= $this->Form->input('username', ['class' => 'form-control', 'id' => 'name']); ?></td>
                </tr>
                <tr id="trUserAdmin">
                    <th class="active text-center"><?=__('User Types')?></th>
                    <td>
                        <?=
                        $this->Form->input('admin_flg', ['type' => 'radio', 'options' => [
                                ['value' => '0', 'text' => __('User')],
                                ['value' => '1', 'text' => __('Admin')],
                            ], 'class' => 'form-control', 'id' => 'UserAdmin']);
                        ?>
                    </td>
                </tr>
                <tr id="trAppWeb">
                    <th class="active text-center"><?=__('Use Types')?></th>
                    <td>
                        <?=
                        $this->Form->input('app_web_flg', ['type' => 'radio', 'options' => [
                                ['value' => '1', 'text' => __('App-Web Operation Assistant')],
                                ['value' => '0', 'text' => __('Web Operation Assistant')],
                            ], 'class' => 'form-control', 'id' => 'AppWeb']);
                        ?>
                    </td>
                </tr>
                <tr>
                    <th class="active text-center"><?=__('Company Email')?></th>
                    <td><?= $this->Form->input('company_email', ['type' => 'text', 'class' => 'form-control']); ?></td>
                </tr>
                <tr>
                    <th class="active text-center" style="width:150px;"><?=__('Gmail')?></th>
                    <td><?= $this->Form->input('email', ['class' => 'form-control', 'id' => 'email']); ?></td>
                </tr>
                <tr>
                    <th class="active text-center"><?=__('Password')?></th>
                    <td><?= $this->Form->input('password', ['type' => 'text', 'class' => 'form-control nospace', 'id' => 'password', 'autocomplete' => 'new-password']); ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">
                        <?= $this->Form->input('del_flg', ['type' => 'hidden', 'id' => 'del_flg']); ?>
                        <button class="btn btn-danger" id="btn-delete"><?=__('Delete')?></button>
                        <?=
                        $this->Html->link(__('Cancel'), [
                            'controller' => 'Users',
                            'action' => 'index',
                                ], ['class' => 'btn btn-default']);
                        ?>
                        <button class="btn btn-primary" id="btn-submit"><?=__('OK')?></button>
                    </td>
                </tr>
            </table>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="col-md-6">
    </div>
</div>

<script type="text/javascript">
    function checkUserAdmin(){
        var check = $('#trUserAdmin input[name="admin_flg"]:checked').val();
        if(check==1){
            $('#trAppWeb label').eq(1).hide();
            $('#trAppWeb #app-web-flg-1').prop('checked',true);
        }else{
            $('#trAppWeb label').eq(1).show();
        }
    };
    $(document).ready(function () {
        checkUserAdmin();
        $('#trUserAdmin input[name="admin_flg"]').change(function(){
            checkUserAdmin();
        });
        $('input.nospace').keydown(function (e) {
            if (e.keyCode == 32) {
                return false;
            }
        });
        $('#btn-delete').click(function (e) {
            e.preventDefault();
            var r = confirm("<?= __("Are you sure want to delete this language?")?>");
            if (r == true) {
                $('#del_flg').val(1);
                $('form#formEdit').submit();
            } else {

            }
        });
    });
</script>