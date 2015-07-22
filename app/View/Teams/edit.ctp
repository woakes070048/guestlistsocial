<?echo $this->Html->script("http://code.jquery.com/jquery-1.9.1.min.js");
echo $this->Html->script("http://malsup.github.io/jquery.form.js");
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
<div id='refresh1'>

</div>
<script>
$(document).ready(function() {
    $('select').selectric();

    <?if (empty($currentAccount)) {
        $currentAccount = 'null';
    }
    if (empty($currentTeam)) {
        $currentTeam = 'null';
    }?>

    //$("#UsersEditrefreshForm").ajaxSubmit({url: '/teams/permissionSave1', type: 'post'});
    //$('#refresh1').load('/teams/editrefresh/' + <?echo $currentTeam;?> + '/' + <?echo $currentAccount;?>);
    //$('#UsersEditrefreshForm input[type=submit]').click(function () {
    //    $('#UsersEditrefreshForm').ajaxSubmit({url: '/teams/permissionSave1', type: 'post'});
    //}); 
    $('#loading').show();
    $('#refresh1').load('/teams/editrefresh/' + <?echo $currentTeam;?> + '/' + <?echo $currentAccount;?>, function() {
        $('#loading').hide();
    });
});
</script>