    <?if (!empty($currentTeam)) {?>
        <div id='editTeamRow'>
            <ul id='editTeam'>
                <div class='teamsContainerHeader'>
                    Twitter Accounts
                </div>
            <? echo $this->Form->create('Accounts', array('url' => array('controller' => 'teams', 'action' => 'permissionSave1')));?>
            <? foreach ($accounts as $key) {
                if (in_array($key['TwitterAccount']['account_id'], $accountPermissions)) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }?>
                <li>
                    <?echo $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', $checked, 'label' => "<span class='screenName'>" . $key['TwitterAccount']['screen_name'] . "</span>", 'name' => 'data[Accounts]['.$key['TwitterAccount']['account_id'].'][permissions]['.$key['TwitterAccount']['account_id'].']', 'id' => 'AccountsTwitterPermissions' . $key['TwitterAccount']['account_id']));
                    echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $currentTeam, 'name' => 'data[Accounts]['.$key['TwitterAccount']['account_id'].'][team_id]'));?>
                </li>
            <?}?>
            <? echo $this->Form->submit('Save');?>
            <? echo $this->Form->end();?>
            </ul>
            <ul id='editTeam'>
                <div class='teamsContainerHeader'>
                    Users
                </div>
            <? echo $this->Form->create('Users', array('url' => array('controller' => 'teams', 'action' => 'permissionSave1')));?>
            <? foreach ($users as $key) {?>
                <? if ($this->Session->read('Auth.User.id') == $key['User']['id']) {
                    $disabled = 'disabled';
                } else {
                    $disabled = '';
                }?>
                <li>
                    <?echo $this->Form->input('user_permissions', array('type' => 'select', 'class' => 'aCheckbox', 'label' => "<img src='" . $key['User']['profile_pic'] . "' width=25px style='margin: 0 10px 0 0; vertical-align:middle '>" . $key['User']['first_name'] . ' ' . $key['User']['last_name'], 'name' => 'data[Users]['.$usersPermissions[$key['User']['id']]['TeamsUser']['id'].'][permissions]['.$key['User']['id'].']', 'options' => array(1 => 'Admin', 2 => 'Team Member', 7 => 'Proofer'), 'selected' => $usersPermissions[$key['User']['id']]['TeamsUser']['group_id'], 'id' => 'UsersUserPermissions' . $key['User']['id'], $disabled));
                    echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $currentTeam, 'name' => 'data[Users]['.$usersPermissions[$key['User']['id']]['TeamsUser']['id'].'][team_id]', $disabled));
                    echo $this->Html->image('false1.png', array('url' => array('action' => 'removeFromTeam', $key['User']['id'], $currentTeam), 'style' => 'float: right; margin-left: 5px', 'title' => 'Remove from team', 'onclick' => 'confirm("Are you sure you want to remove this person from your team?");'));?>

                </li>
            <?}?>
            <? echo $this->Form->submit('Save');?>
            <? echo $this->Form->end();?>
            </ul>
        </div>
    <?} elseif (!empty($currentAccount)) {?>
        <div id='editTeamRow'>
            <ul id='editTeam'>
                <div class='teamsContainerHeader'>
                    Teams
                </div>
                <?echo $this->Form->create('Teams', array('url' => array('controller' => 'teams', 'action' => 'permissionSave1')));?>
                <? foreach ($allTeams as $key => $value) {
                    if (in_array($value, $teams)) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }?>
                    <li>
                        <? echo $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', 'label' => $teamsName[$value]['Team']['name'], 'name' => 'data[Teams][' . $value . '][permissions][' . $currentAccount . ']', $checked));?>
                        <? echo $this->Form->input('account_id', array('type' => 'hidden', 'value' => $currentAccount, 'name' => 'data[Teams][' . $value . '][account_id]', 'id' => 'TeamsTwitterPermissions' . $value));?>
                    </li>
                <?}?>
            <? echo $this->Form->submit('Save');?>
            <? echo $this->Form->end();?>
            </ul>
        </div>
       <? } else {?>
        <div id='noaccount' style='display:block; margin: 30px 0 0 5%'>
        Please select an account or team from above
        </div>
    <?}?>
<script>
    $("select").selectric();

    <?if (empty($currentAccount)) {
        $currentAccount = 'null';
    }
    if (empty($currentTeam)) {
        $currentTeam = 'null';
    }?>

    $('#UsersEditrefreshForm').submit(function () {
        $('#refresh1').css('opacity', 0.4);
        $('#loading').show();
        $(this).ajaxSubmit({success: function() {
            $('#refresh1').load('/teams/editrefresh/' + <?echo $currentTeam;?> + '/' + <?echo $currentAccount;?>, function() {        
                $('#refresh1').css('opacity', 1);
                $('#loading').hide();
            });
        }});
        return false;
    });
    $('#AccountsEditrefreshForm').submit(function () {
        $('#refresh1').css('opacity', 0.4);
        $('#loading').show();
        $(this).ajaxSubmit({success: function() {
            $('#refresh1').load('/teams/editrefresh/' + <?echo $currentTeam;?> + '/' + <?echo $currentAccount;?>, function() {        
                $('#refresh1').css('opacity', 1);
                $('#loading').hide();
            });
        }});
        return false;
    });
    $('#TeamsEditrefreshForm').submit(function () {
        $('#refresh1').css('opacity', 0.4);
        $('#loading').show();
        $(this).ajaxSubmit({success: function() {
            $('#refresh1').load('/teams/editrefresh/' + <?echo $currentTeam;?> + '/' + <?echo $currentAccount;?>, function() {        
                $('#refresh1').css('opacity', 1);
                $('#loading').hide();
            });
        }});
        return false;
    });
</script>