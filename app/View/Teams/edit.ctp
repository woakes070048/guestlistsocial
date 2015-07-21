<?echo $this->Html->script("http://code.jquery.com/jquery-1.9.1.min.js");
echo $this->Html->script('jquery.selectric.min');?>
<div class='filter editTeamFilter'>
    <?
    echo $this->Form->create('filterTeam');
    echo $this->Form->input('team', array(
        'label' => false,
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select by Team', $dropdownteams)));
    echo $this->Form->end();

    echo $this->Form->create('filterAccount');
    echo $this->Form->input('account', array(
        'label' => false,
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select by Twitter Account', array_combine($dropdownaccounts,$dropdownaccounts))));
    echo $this->Form->end();
    ?>
    <div id='filterButtons' style='display: block; height: 30px'>
        <?echo $this->Html->link('Create a New Team', '/teams/manage', array('class' => 'urlSubmit', 'style' => 'float:left;margin:10px'));?>
        <?if (!empty($currentTeam)) {?>
            <?echo $this->Html->link('Add Twitter Account', '/twitter/connect', array('class' => 'urlSubmit', 'style' => 'margin:10px'));?>
            <?echo $this->Html->link('Invite User', '/teams/invite', array('class' => 'urlSubmit', 'style' => 'margin:10px'));?>
        <?}?>
    </div>
</div>
<?if (!empty($currentTeam)) {?>
    <div id='editTeamRow'>
        <ul id='editTeam'>
            <div class='teamsContainerHeader'>
                Twitter Accounts
            </div>
        <? echo $this->Form->create('Accounts', array('url' => array('controller' => 'teams', 'action' => 'permissionSave1')));?>
        <? foreach ($accounts as $key) {
            if (in_array($key['TwitterAccount']['account_id'], $permissions)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }?>
            <li>
                <?echo $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', $checked, 'label' => "<span class='screenName'>" . $key['TwitterAccount']['screen_name'] . "</span>", 'name' => 'data[Accounts]['.$key['TwitterAccount']['account_id'].'][permissions]['.$key['TwitterAccount']['account_id'].']', 'value' => ''));
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
            <li>
                <?echo $this->Form->input('user_permissions', array('type' => 'select', 'class' => 'aCheckbox', 'label' => $key['User']['first_name'] . ' ' . $key['User']['last_name'], 'name' => 'data[Users]['.$usersPermissions[$key['User']['id']]['TeamsUser']['id'].'][permissions]['.$key['User']['id'].']', 'options' => array(2 => 'Team Member', 7 => 'Proofer', 1 => 'Admin'), 'selected' => $usersPermissions[$key['User']['id']]['TeamsUser']['group_id']));
                echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $currentTeam, 'name' => 'data[Users]['.$usersPermissions[$key['User']['id']]['TeamsUser']['id'].'][team_id]'));
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
            <? foreach ($teams as $key => $value) {
                if (in_array($value, $allTeams)) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }?>
                <li>
                    <? echo $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', 'label' => $teamsName[$value]['Team']['name'], 'name' => 'data[Teams][' . $value . '][permissions][' . $currentAccount . ']', $checked));?>
                    <? echo $this->Form->input('account_id', array('type' => 'hidden', 'value' => $currentAccount, 'name' => 'data[Teams][' . $value . '][account_id]'));?>
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
$(document).ready(function() {
    $('select').selectric();
});
</script>