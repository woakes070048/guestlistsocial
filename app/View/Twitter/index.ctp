<? 
echo $this->Html->script("https://code.jquery.com/jquery-1.9.1.min.js");
echo $this->Html->script("https://malsup.github.io/jquery.form.js");
echo $this->Html->script("//js.pusher.com/2.2/pusher.min.js");
echo $this->Html->script('jquery-ui-1.10.3.custom');
echo $this->Html->script('jquery-ui-timepicker-addon');
echo $this->Html->script('charCount');
echo $this->Html->script('jquery.infinitescroll');
echo $this->Html->script('jquery.qtip.min');
echo $this->Html->script('jquery.selectric.min');
echo $this->Html->script('jquery.timeago');
echo $this->Html->script('toastr.min');
echo $this->Html->script('jquery.scrollbar');
echo $this->Html->script('jquery.hideseek');
echo $this->Html->script('slick.min');
echo $this->Html->script('jquery.selectBoxIt');
echo $this->Html->script('jquery.lazyload.min');
echo $this->Html->css('jquery.qtip.min');
echo $this->Html->css('calendar');
echo $this->Html->css('toastr.min');
echo $this->Html->css('jquery.scrollbar');
echo $this->Html->css('slick');
echo $this->Html->css('jquery.selectBoxIt');?>
<?php
$flash = $this->Session->flash('auth');
if ($flash != 'You are not authorized to access that location.') {
    echo $this->Session->flash('auth');
}
?>
<link href="//cdn.rawgit.com/noelboss/featherlight/1.3.3/release/featherlight.min.css" type="text/css" rel="stylesheet" />
<script src="//cdn.rawgit.com/noelboss/featherlight/1.3.3/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>
<script src="https://checkout.stripe.com/checkout.js"></script>
<?
/*
echo $this->Form->create('filterAccount');
echo $this->Html->image('twitter19px.png', array('class' => 'selectimage'));
echo $this->Form->input('account', array(
    'label' => false,
    'onchange' => 'this.form.submit()',
    'options' => array('empty' => 'Select by Twitter Account', array_combine($dropdownaccounts,$dropdownaccounts)),
    'selected' => $this->Session->read('filterAccount')));
echo $this->Form->end();

if ($this->Session->read('access_token.account_id')) {
    echo $this->Html->image('calendar.png', array('url' => '/twitter/calendar/0', 'title' => 'Editorial Calendar', 'style' => 'margin: 10px 50px 10px 10px'));
}

echo $this->Html->image('user_purple.png', array('class' => 'selectimage'));
echo $this->Form->create('filterUser');
echo $this->Form->input('user', array(
    'label' => false,
    'onchange' => 'this.form.submit()',
    'options' => array('empty' => 'Select by User', $dropdownusers),
    'selected' => $this->Session->read('filterUser')));
echo $this->Form->end();
?>

<h2>Manage Your Team's Tweets</h2>

<div id="progress">
    <h2>PROGRESS</h2>
    <table>
        <tr class='progress firstline'>
            <td class='queuedCount'><? echo $queuedCount;?></td>
            <td class='awaitingProofCount mid'><? echo $awaitingProofCount;?></td>
            <td class='needImprovingCount'><? echo $needImprovingCount;?></td>
        </tr>
        <tr class='progress secondline'>
            <td>QUEUED</td>
            <td class='mid'>AWAITING PROOF</td>
            <td>NEED IMPROVING</td>
        </tr>
    </table>
</div>

<div id="filterLinks">
<? echo $this->Html->link('Awaiting Proof', array('controller'=>'twitter','action'=>'index'), array('class' => (empty($this->params['named']['h']))?'awaitingProof active' :'awaitingProof inactive'));
echo $this->Html->link('Queued', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'queued')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='queued') )?'queued active' :'queued inactive'));
echo $this->Html->link('Published', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'published')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='published') )?'published active' :'published inactive'));
echo $this->Html->link('Need Improving', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'improving')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='improving') )?'needImproving active' :'needImproving inactive'));
echo $this->Html->link('Day-by-Day', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'daybyday')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='daybyday') )?'daybyday active' :'daybyday inactive'));
echo $this->Html->Link('Not Published', array('controller'=>'twitter','action'=>'index','?'=>array('h'=>'notpublished')), array('class' => (!empty($this->params['named']['h']) && ($this->params['named']['h']=='notpublished') )?'notPublished active' :'notPublished inactive'));
?>
</div>
*/
?>
<div class='left'>
<div class='selectTeam selectedTeam'>
<div class='selectTeamText'>
    <?
    if ($team == 0) {
        $team = array_keys($myteam)[0];
    }
    echo $this->Form->input('team', array(
        'type' => 'radio',
        'legend' => false,
        'onchange' => 'this.form.submit()',
        'options' => array($team => $myteam[$team]),
        'default' => $team,
        'class' => 'filterTeam',
        'separator' => '</div><div>',
        'div' => false,
        'disabled'
    ));?>
</div>
<i class="fa fa-chevron-down"></i>
</div>
<div class='filter scrollbar-macosx filterTeamScroll'>
<div class='searchContainer'>
<input id="search" name="search" placeholder="Search..." type="alt" data-list=".input.radio.filter1" class="inputFilter">
<i class="fa fa-search"></i>
</div>
    <?echo $this->Form->create('filter');?>
    <div class="input radio filter1">
    <?
    foreach ($myteam as $key => $value) {?>
        <?if ($key == $team) {
            $class = 'checked';
        } else {
            $class = '';
        }?>
        <div class="selectTeam <?echo $class;?>" alt="<?echo$value;?>">
            
            <div class='selectTeamText'>
                <?
                echo $this->Form->input('team', array(
                    'type' => 'radio',
                    'legend' => false,
                    'onchange' => 'this.form.submit()',
                    'options' => array($key => $value),
                    'default' => $team,
                    'class' => 'filterTeam',
                    'separator' => '</div><div>',
                    'div' => false));?>
            </div>
        </div>
    <?
    }
    ?>
    </div><?
    echo $this->Form->end();
    ?>
</div>
<div class="filter-buttons">
    <i class="fa fa-pencil fa-fw" id="pencilIcon" data-label="Edit Teams"></i>
    <i class="fa fa-bar-chart fa-fw" id="chartIcon" data-label="Team Progress"></i>
    <?
    if (!empty($team)) {
        if ($session_teams[$team]['TeamsUser']['group_id'] == 1) {?>
            <i class="fa fa-users fa-fw" id="teamIcon" data-label="Manage Users"></i>
        <?}
    }?>
    <i class="fa fa-twitter fa-fw" id="twitterIcon" data-label="Manage Twitter Accounts"></i>
</div>
<?if (!empty($team)) {?>
<div id="manageTeam">
<ul id='editTeam'>
                <!--<div class='teamsContainerHeader'>
                    Users
                </div>-->
            <? echo $this->Form->create('Users', array('url' => array('controller' => 'teams', 'action' => 'permissionSave1')));?>
            <? foreach ($manageTeamUsers as $key) {?>
                <? if ($this->Session->read('Auth.User.id') == $key['User']['id']) {
                    $disabled = 'disabled';
                } else {
                    $disabled = '';
                }?>
                <li>
                    <?echo $this->Form->input('user_permissions', array('type' => 'select', 'class' => 'aCheckbox', 'label' => "<img src='" . $key['User']['profile_pic'] . "' width=25px style='margin: 0 10px 0 0; vertical-align:middle '>" . $key['User']['first_name'] . ' ' . $key['User']['last_name'], 'name' => 'data[Users]['.$usersPermissions[$key['User']['id']]['TeamsUser']['id'].'][permissions]['.$key['User']['id'].']', 'options' => array(1 => 'Admin', 2 => 'Team Member', 7 => 'Proofer'), 'selected' => $usersPermissions[$key['User']['id']]['TeamsUser']['group_id'], 'id' => 'UsersUserPermissions' . $key['User']['id'], $disabled));
                    echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $team, 'name' => 'data[Users]['.$usersPermissions[$key['User']['id']]['TeamsUser']['id'].'][team_id]', $disabled));?>
                    <a href="/teams/removeFromTeam/<?echo $key['User']['id'];?>/<?echo $team;?>">
                    <i class="fa fa-trash removeFromTeam" style="margin-left: 5px; vertical-align: middle; line-height: 32px;" title="Remove from Team" onclick='confirm("Are you sure you want to remove this person from your team?");'></i>
                    </a>

                </li>
            <?}?>
            <? echo $this->Form->submit('Save');?>
            <? echo $this->Form->end();?>
            </ul>
            <div class="manageTeamInvite">
                <? echo $this->Form->create('invite', array('url' => array('controller' => 'teams', 'action' => 'invite')));?>
                <? echo $this->Form->input('team', array('type' => 'hidden', 'value' => $team));?>
                <? echo $this->Form->input('email', array('label' => false, 'class' => 'inputBox', 'type' => 'text', 'placeholder' => 'email'));?>
                <? echo $this->Form->input('group', array('label' => false, 'options' => array(2 => 'Team Member', 7 => 'Proofer', 1 => 'Admin'), 'selected' => 2));?>
                <!--<span class="inputBoxButton">Invite</span>--><?echo $this->Form->submit('Invite', array('class' => 'inputBoxButton'));?>
                <? echo $this->Form->end();?>
            </div>
</div>
<?}?>
<div id="twitterManage">
            <? echo $this->Form->create('Accounts', array('url' => array('controller' => 'teams', 'action' => 'permissionSave1')));?>
<ul id='editTeam' class="scrollbar-macosx">
            <? foreach ($allaccounts as $key) {
                if (in_array($key['TwitterAccount']['account_id'], $teamPermissions)) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }?>
                <li>
                    <?echo $this->Html->image($allaccounts[$key['TwitterAccount']['account_id']]['TwitterAccount']['profile_pic'], array('width' => '30px'));?>
                    <?echo $this->Form->input('twitter_permissions', array('type' => 'checkbox', 'class' => 'aCheckbox', $checked, 'label' => "@" . $key['TwitterAccount']['screen_name'], 'name' => 'data[Accounts]['.$key['TwitterAccount']['account_id'].'][permissions]['.$key['TwitterAccount']['account_id'].']', 'id' => 'AccountsTwitterPermissions' . $key['TwitterAccount']['account_id']));
                    echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $team, 'name' => 'data[Accounts]['.$key['TwitterAccount']['account_id'].'][team_id]'));?>
                </li>
            <?}?>
            </ul>
            <? echo $this->Form->submit('Save');?>
            <? echo $this->Form->end();?>
</div>
<div id="createTeam">
    <?
    echo $this->Form->create('editTeam', array('url' => array('controller' => 'teams', 'action' => 'editTeam')));
    foreach ($myteam as $key => $value) {
        if (!empty($adminTeams)) {
            if (!empty($adminTeams[$key])) {?>
            <div style="margin: 0">
                <?
                echo $this->Form->input('name', array('label' => false, 'name' => 'data[' . $key . '][Team][name]', 'value' => $value, 'class' => 'inputBox'));?>
                <i class="fa fa-pencil fa-fw"></i>
                <a href="/teams/deleteTeam/<?echo $key;?>"><i class="fa fa-trash fa-fw" style='margin-left: 40px' onclick='confirm("Are you sure you want to delete this team?");'></i></a>
            </div>
        <?  }
        }
    }
    echo $this->Form->end('Submit Changes');?>
    <hr style="border: none; background-color: #ccc; width: 220px; height: 1px; margin: 25px 0">
    <?
        echo $this->Form->create('createTeam', array('url' => array('controller' => 'teams', 'action' => 'manage')));
        echo $this->Form->input('Team name', array('name' => 'name', 'label' => false, 'placeholder' => 'team name', 'class' => 'inputBox fullBox'));
        echo $this->Form->end('Create Team');
    ?>
</div>


<div class='selectTwitterAccount selectedAccount'>
<? if ($account == 0) {?>
    <?echo $this->Html->image('/img/allaccounts.jpg', array('width' => '50px'));?>
    <?$allaccounts[0]['TwitterAccount']['screen_name'] = 'All Accounts';
    $class = 'dn';?>
<?} else {?>
    <?echo $this->Html->image($allaccounts[$account]['TwitterAccount']['profile_pic'], array('width' => '50px'));
    $class = '';}?>
<div class='selectTwitterAccountText <?echo $class;?>'>
    <?echo $this->Form->input('account', array(
        'type' => 'radio',
        'legend' => false,
        'onchange' => 'this.form.submit()',
        'options' => array($account => $allaccounts[$account]['TwitterAccount']['screen_name']),
        'default' => $account,
        'class' => 'filterAccount',
        'separator' => '</div><div>',
        'div' => false,
        'disabled'
    ));?>
    <?if (empty($stats[$account]['Statistic']['followers_count'])) {
        $stats[$account]['Statistic']['followers_count'] = 0;
    }
    if (empty($stats[$account]['Statistic']['following_count'])) {
        $stats[$account]['Statistic']['following_count'] = 0;
    }?>
    <? if ($account != 0) {?>
        <div><span class='followersCount'><? echo $stats[$account]['Statistic']['followers_count'];?></span> followers</div>
    <?}?>
</div>
<i class="fa fa-chevron-down"></i>
</div>
<div class='filter scrollbar-macosx filterAccountScroll'>
<div class='searchContainer'>
<input id="search" name="search" placeholder="Search..." type="alt" data-list=".input.radio.filter2" class="inputFilter">
<i class="fa fa-search"></i>
</div>
    <?
    echo $this->Form->create('filter');?>
    <div class="input radio filter2">
        <!--<div>
            <?
            echo $this->Form->input('account', array(
                        'type' => 'radio',
                        'legend' => false,
                        'onchange' => 'this.form.submit()',
                        'options' => array(0 => 'All Accounts'),
                        'default' => $account,
                        'class' => 'filterAccount',
                        'separator' => '</div><div>',
                        'div' => false));?>
        </div>-->
    <?
    foreach ($dropdownaccounts as $key => $value) {?>
        <?if ($key == $account) {
            $class = 'checked';
        } else {
            $class = '';
        }?>
        <div class="selectTwitterAccount <?echo $class;?>" alt="<?echo$value;?>">
            <?echo $this->Html->image($allaccounts[$key]['TwitterAccount']['profile_pic'], array('width' => '50px'));?>
            
            <div class='selectTwitterAccountText'>
                <?
                echo $this->Form->input('account', array(
                    'type' => 'radio',
                    'legend' => false,
                    'onchange' => 'this.form.submit()',
                    'options' => array($key => $value),
                    'default' => $account,
                    'class' => 'filterAccount',
                    'separator' => '</div><div>',
                    'div' => false));?>
                <?if (empty($stats[$key]['Statistic']['followers_count'])) {
                    $stats[$key]['Statistic']['followers_count'] = 0;
                }
                if (empty($stats[$key]['Statistic']['following_count'])) {
                    $stats[$key]['Statistic']['following_count'] = 0;
                }?>
                <div><span class='followersCount'><? echo $stats[$key]['Statistic']['followers_count'];?></span> followers</div>
                <!--<div><span class='followingCount'><? echo $stats[$key]['Statistic']['following_count'];?></span> following</div>-->
            </div>
        </div>
    <?
    }
    ?>
    </div>

    <? /*if ($this->Session->read('access_token.account_id')) {
        echo $this->Html->image('calendar.png', array('url' => '/twitter/calendar/0', 'title' => 'Editorial Calendar', 'style' => 'margin: 10px 50px 10px 10px'));
    }*/

    /*echo $this->Form->input('user', array(
        'label' => false,
        'onchange' => 'this.form.submit()',
        'options' => array('' => 'Select by User', $dropdownusers),
        'selected' => $user,
        'class' => 'filterUser'));

    echo $this->Form->input('status', array(
        'onchange' => 'this.form.submit()',
        'label' => false,
        'options' => array(
            '' => 'Select by Status',
            'All Statuses' => 'All Statuses',
            'queued' => 'Queued',
            'awaitingproof' => 'Awaiting Proof',
            'improving' => 'Need Improving',
            'published' => 'Published',
            'notpublished' => 'Not Published'),
        'selected' => $status,
        'class' => 'filterStatus'));

    echo $this->Form->input('team', array(
        'label' => false,
        'onchange' => 'this.form.submit()',
        'options' => array(
            '' => 'Select by Team',
            $myteam),
        'selected' => $team,
        'class' => 'filterTeam'));*/
    echo $this->Form->end();
    ?>
</div>
<div class="filter-buttons">
    <!--<i class="fa fa-twitter"></i>-->
    <?
    if (!empty($noTeam)) {

    } else {
        if (empty($allowed_more_accounts)) {
            echo $this->Html->link($this->Form->button('Add', array('class' => 'inputBoxButton addTwitterAccount feather')), "#", array('escape' => false,
        "data-featherlight" => "/twitter/moreaccounts"));
        } else {
            echo $this->Html->link($this->Form->button('Add', array('class' => 'inputBoxButton addTwitterAccount')), "/twitter/connect", array('escape' => false));
        }
    }
    ?>
    <a href="/twitter/calendar/0"><i class="fa fa-calendar fa-fw" data-label="Editorial Calendar"></i></a>
</div>
    <footer>
        <ul>
            <li>Support</li>
            <li>FAQ</li>
            <li>Upgrade</li>
            <li>More</li>
        </ul>
        <div>
            <? echo $this->Html->image('/img/logogrey.png', array('height' => '20px'));?>
            <i class='fa fa-copyright'></i> 2015
        </div>
    </footer>
</div>


<?php
/*
<div id='addtweetprogress'>

<? if ($params == 'h:nocalendar' && $account) {?>
    <div id='addTweetWrapper'>
<?
//Add Tweet
echo $this->Form->create('Tweet', array('url' => array('controller' => 'twitter', 'action' => 'testing'), 'id' => 'submitTweet'));
        echo $this->Form->input('timestamp', array(
            'type' => 'text', 
            'label' => false, 
            'class' => 'schedule', 
            'id' => 'schedule',
            'placeholder' => 'Date & Time'
            ));
        echo $this->Form->textarea('body', array('label' => false, 'type' => 'post', 'class' => 'ttt', 'placeholder' => 'Body'));
        echo $this->Form->end(array('id' => 'tweetsubmit1', 'value' => 'AddTweet', 'label' => 'ADD A TWEET')); // add new form with hidden input fields to tweet now
?>
</div>
<?}?>

<div id="progress">
    <h2>PROGRESS</h2>
    <table>
        <tr class='progress firstline'>
            <td class='queuedCount'><? echo $queuedCount;?></td>
            <td class='awaitingProofCount mid'><? echo $awaitingProofCount;?></td>
            <td class='needImprovingCount'><? echo $needImprovingCount;?></td>
        </tr>
        <tr class='progress secondline'>
            <td>QUEUED</td>
            <td class='mid'>AWAITING PROOF</td>
            <td>NEED IMPROVING</td>
        </tr>
    </table>
</div>

<? if ($params != 'h:nocalendar') {
    $text = 'ACTIVE';
    $link = '/tweets?h=nocalendar';
} else {
    $text = 'NOT ACTIVE';
    $link = '/';
} ?>
<a href=<?echo $link;?>>
<div id='dbdbox'>
Day By Day <br /> View: <br />
<div class='calendarlarge'><b>
<?
echo $text;
?>
</b></div>
</div>
</a>

</div>
*/
?>
<div id="tableContainer">
    <div class='switchContainer'>
    Calendar View:
        <div class="onoffswitch">
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" <?echo ($params != 'h:nocalendar')? 'checked' : ''?>>
            <label class="onoffswitch-label" for="myonoffswitch">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div>
    </div>
    <div id="table" style="float: right;">

    </div>
    <div id='noaccount' style="display: none;">
    <?echo $this->Html->image('/img/logogrey.png', array('width' => '35px'));?>
    Please select an account from above to see the day-by-day view
    </div>
</div>

<?php //echo $this->Html->link('Add Twitter Account', '/twitter/connect');?> <br />
<?php //echo $this->Html->link('Logout', '/users/logout');?>
<?php //echo $this->Paginator->next();?>
<script>
$(document).ready(function() {
        <? if ($params == 'h:nocalendar' && $this->Session->read('access_token.account_id')) {?>
            $('#table').css('opacity', '.4');
            $('#loading').show();
            $('#table').load('/twitter/indexrefresh/<?php echo $params; ?>', function () {
                $('#table').css('opacity', '1');
                $('#loading').hide();
                $('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
            });
        <? } elseif ($params != 'h:nocalendar' && !$this->Session->read('access_token.account_id')) {?>
            $('#table').hide();
            $('#noaccount').show();
        <?} elseif (!empty($manageTeamActive)) {?>
            $('#table').css('opacity', '.4');
            $('#loading').show();
            $('#table').load('/teams/manageteam/<?echo (!empty($manageTeamFilter))? $manageTeamFilter : "";?>', function () {
                $('#table').css('opacity', '1');
                $('#loading').hide();
                $('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
            });
        <?} else {?>
            $('#table').css('opacity', '.4');
            $('#loading').show();
            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function () {
                $('#table').css('opacity', '1');
                $('#loading').hide();
                $('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
                $('html, body').animate({
                        scrollTop: $("." + "<?echo $scroll;?>").offset().top - 30
                    }, 2000);
                window.history.pushState("object or string", "TweetProof", "/tweets");
            });
        <?}?>

        $('.editing').charCount({css: 'counter counter1'});

        $(".TwitterVerified1").each( function() {
            if ($(this).val() == 0) {
                color = '#f0ad4e';
            } else if ($(this).val() == 1) {
                color = '#5cb85c';
            } else if ($(this).val() == 2) {
                color = '#d9534f';
            }
            $(this).closest("#refresh").find('#TweetBody').css("border", "1px solid" + color);
            $(this).closest("#refresh").find('#TweetBody').css("border-bottom", "none");
            $(this).closest("#refresh").find('.counter1').css("border", "1px solid" + color);
            $(this).closest("#refresh").find('.counter1').css("border-top", "none");
        });

        <? if ($params == 'h:queued') {?>
            $(".verifiedby").prop('disabled', false);
        <? } else { ?>
            $(".verifiedby").prop('disabled', true);
        <? } ?>

        $("#table").on("click", ".deletetweet", function() {
            id = $(this).attr('id');
            $.ajax({url: "/twitter/delete/" + id, success: function() {
            window.location.reload(true);}});
        });

        /*$("#table").on("change", ".TwitterVerified1", function() {
            <? if ($params == 'h:nocalendar') {?>
                $("#table").css('opacity', '.4');
                $('#edit').ajaxSubmit({success: function() {
                    warnMessage = null;
                    refresh();
                }});
                //setTimeout(refresh, 500);//delaying the table refresh so that the form can successfully submit into the databases
                function refresh() {
                    $('#table').load('/twitter/indexrefresh/<?php echo $params; ?>', function() {
                        $("#table").css('opacity', '1');
                    });
                };

                $('#progress table').load('/twitter/progressrefresh');
                <? } else {?>
                    $(this).closest(".tweet").find('input[name=tosubmit]').val(true);
                    $("#table").css('opacity', '.4');
                    $('#loading').show();
                    var dat = new FormData();
                    $('input[name=tosubmit][value=true]').each(function () {
                        //dat = dat + '&' + $.param($(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked'));
                        $(this).closest(".tweet").find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
                            if ($(this).attr('type') == 'file') {
                                dat.append($(this).attr('name'), this.files[0]);
                            } else {
                                dat.append($(this).attr('name'), $(this).val());
                            }
                        });
                    });
                    
                    $.ajax({
                        type: "POST",
                        url: "/editorial_calendars/editcalendartweet1",
                        data: dat,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            warnMessage = null;
                            var month = $('.slick-current').attr('data-month');
                            $('#table').load('/editorial_calendars/calendarrefresh/' + month, function() {
                                $("#table").css('opacity', '1');
                                $('#loading').hide();
                            });
                        }
                    });
                    $('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
                <?} ?>
        });*/

        $(".editing").on('change', function () {
            warnMessage = "You have unsaved changes on this page, if you leave your changes will be lost.";
        });
            warnMessage = null;
            window.onbeforeunload = function () {
                if (warnMessage != null) return warnMessage;
            }

        $('input:submit, button:submit').on('click', function() {
            warnMessage = null;
        });

        $(".shortsingle").click(function () {
            //regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
            //textbox = $(this).closest('.nopadding').children('.editing');
            //var longUrlLink = textbox.val().match(regex);
                /*jQuery.urlShortener({
                    longUrl: longUrlLink,
                    success: function (shortUrl) {
                        textbox.val(textbox.val().replace(longUrlLink, shortUrl));
                    },
                    error: function(err) {
                        $("#shortUrlInfo").html(JSON.stringify(err));
                    }
                });*/
        });

        $('.input.file input').on('change', function() {
            $(this).parent().css('background', "url(/img/upload_image_green.png) left center no-repeat");
        });

        $(".approveAll").click(function () {
            $(".verified").each(function () {
                if ($(this).find(".input.radio input:radio[value=0]").prop('checked') || $(this).find(".input.radio input:radio[value=2]").prop('checked')) {
                    $(this).find(".input.radio input:radio[value=1]").prop('checked', true);
                }
                $("#table").css('opacity', '.4');
                    /*id = $(this).find(".input.radio input:radio[value=1]").attr('id');
                    id = id.slice(0, -1);
                    $("#" + id + "_" + "<? echo $this->Session->read('Auth.User.first_name'); ?>").prop('disabled', false);*/

                    
            });
            $('#submitTweets').ajaxSubmit({success: function() {
                refresh();
            }});

            function refresh() {
                $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                    $("#table").css('opacity', '1');
                });
            };
            $('#progress table').load('/twitter/progressrefresh');
        });

        $("#table").on("click", ".savetweet", function() {
            $("#table").css('opacity', '.4');
            <? if ($params == 'h:nocalendar') {  ?>
                        $('.qtip-content .empty').each(function () {
                            val = $(this).find('input').val();
                            id = $(this).attr('id');
                            id = id.slice(0, -9);
                            $('td#' + id).find('.tweetButtons').find('#' + id + '-comments').find('input').attr('value', val);
                        });
                        $('#edit').ajaxSubmit({success: function() {
                            warnMessage = null;
                            refresh();
                        }});
                <? } else {  ?>
                        $('#submitTweets').ajaxSubmit({success: function() {
                            warnMessage = null;
                            refresh();
                        }});
                <? }  ?>
                //setTimeout(refresh, 500);//delaying the table refresh so that the form can successfully submit into the databases
                function refresh() {
                    <? if ($params == 'h:nocalendar') {  ?>
                            $('#table').load('/twitter/indexrefresh/<?php echo $params; ?>', function() {
                                $("#table").css('opacity', '1');
                            });
                    <? } else {  ?>
                            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                                $("#table").css('opacity', '1');
                            });
                    <? }  ?>
                    
                };

                $('#progress table').load('/twitter/progressrefresh');
        });

        $('.schedule').each(function(){
            $(this).datetimepicker({
                dateFormat: 'dd-mm-yy',
                altFormat: '@',
            });
        });
       
        //$('.comments').hover( function() {
            //id = $(this).attr('id');
            $('.comments').qtip({ 
            content: {
                text: function(event, api) {
                    id = $(this).attr('id'); 
                    //return $('#' + id + '-comments').clone();
                    $.ajax({
                        url: '/comments/commentrefresh/' + id
                    })
                    .then(function(content) {
                    // Set the tooltip content upon successful retrieval
                    api.set('content.text', content);
                    }, function(xhr, status, error) {
                    // Upon failure... set the tooltip content to the status and error value
                    api.set('content.text', status + ': ' + error);
                    });

                    return 'Loading...'; // Set some initial text
                }, 
                button: true
            },
            hide: {
                event: false
            },
            position: {
                my: 'bottom center',
                at: 'top center', 
                target: 'event'
            }
        });
        //})

        /*$('select').selectric({
            optionsItemBuilder: function(itemData, element, index) {
                return element.val().length ? '<span class="ico ico-' + element.val() +  '"></span>' + itemData.text : itemData.text;
            }
        });

        $('.selectric .label:contains("AWAITING APPROVAL")').css({'background': 'url("../img/radioamber.png") no-repeat left center', 'padding-left': '18px', 'margin-left': '5px'});
        $('.selectric .label:contains("APPROVED")').css({'background': 'url("../img/radiogreen.png") no-repeat left center', 'padding-left': '18px', 'margin-left': '5px'});
        $('.selectric .label:contains("IMPROVE")').css({'background': 'url("../img/radiored.png") no-repeat left center', 'padding-left': '18px', 'margin-left': '5px'});*/

        /*$("#refresh").infinitescroll({
            navSelector  : '.next',    // selector for the paged navigation
            nextSelector : '.next a',  // selector for the NEXT link (to page 2)
            itemSelector : '.row',     // selector for all items you'll retrieve
        }, function() {
            $('.editing').charCount({css: 'counter counter1'});
        });*/

        $('.selectTwitterAccount:not(.selectedAccount)').click(function () {
            $(this).find('input').attr("checked", "checked");
            $(this).closest('#filterIndexForm').submit();
        });

        $('.scrollbar-macosx').scrollbar();

        /*$('input[name="onoffswitch"]').change(function() {
            if ($(this).attr('checked') == 'checked') {
                window.location.href = "/tweets";
            } else {
                window.location.href = "/tweets?h=nocalendar";
            }
            return false;
        });*/

        $('#myonoffswitch + label').click(function () {
            if ($('#myonoffswitch').attr('checked') == 'checked') {
                $('#myonoffswitch').removeAttr('checked');
                window.location.replace("/tweets?h=nocalendar");
            } else {
                $('#myonoffswitch').attr('checked', 'checked');
                window.location.replace("/tweets");
            }
            return false;
        });

        $('.selectedAccount').click(function () {
            $('.filter.filterAccountScroll').toggle();
            if ($('.filter.filterAccountScroll').css('display') == 'none') {
                $(this).find('i').css('transform', 'rotate(0deg)');
                $(this).removeClass('clicked');
            } else {
                $(this).find('i').css('transform', 'rotate(180deg)');
                $(this).addClass('clicked');
                $('.filter.filterTeamScroll').hide();
                $('.selectTeam.selectedTeam').removeClass('clicked');
                $('.selectTeam.selectedTeam').find('i').css('transform', 'rotate(0deg)');
            }
        });

        $('.selectedTeam').click(function () {
            $('.filter.filterTeamScroll').toggle();
            if ($('.filter.filterTeamScroll').css('display') == 'none') {
                $(this).find('i').css('transform', 'rotate(0deg)');
                $(this).removeClass('clicked');
            } else {
                $(this).find('i').css('transform', 'rotate(180deg)');
                $(this).addClass('clicked');
                $('.filter.filterAccountScroll').hide();
                $('.selectTwitterAccount.selectedAccount').removeClass('clicked');
                $('.selectTwitterAccount.selectedAccount').find('i').css('transform', 'rotate(0deg)');
            }
        });

        
        $('.inputFilter').hideseek({
        });

        $('select').selectBoxIt({
        });

        $('.filter-buttons i').qtip({
            content: {
                text: function(event, api) {
                    label = $(this).attr('data-label'); 
                    
                    return label; // Set some initial text
                }
            },
            position: {
                my: 'top center',
                at: 'bottom center', 
                target: 'event'
            }
        });

        $('.filter-buttons #teamIcon').click(function () {
            $('#manageTeam').toggle();
        });

        $('.filter-buttons #twitterIcon').click(function () {
            $('#twitterManage').toggle();
        });

        $('.filter-buttons #pencilIcon').click(function () {
            $('#createTeam').toggle();
        });

        $('.filter-buttons #chartIcon').click(function () {
            $('#table').css('opacity', '.4');
            $('#loading').show();
            $('#table').load('/teams/manageteam', function () {
                $('#table').css('opacity', '1');
                $('#loading').hide();
                $('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
            });
            window.history.pushState("object or string", "TweetProof", "/tweets?q=1");
        });

        $('.addTwitterAccount.feather').featherlight('/twitter/moreaccounts/accounts', {
            otherClose: ".stripe-button-el, .stripe-button"
        });

        <? if (empty($allowed_more_teams)) {?>

        $('#createTeamIndexForm').submit(function (e) {
            e.preventDefault();
            $.featherlight('/twitter/moreaccounts/teams', {
                otherClose: ".stripe-button-el, .stripe-button"
            });
        })

        <?}?>


        <? if (empty($allowed_more_users)) {?>
        $('#inviteIndexForm').submit(function (e) {
            e.preventDefault();
            $.featherlight('/twitter/moreaccounts/users', {
                otherClose: ".stripe-button-el, .stripe-button"
            });
        });
        <?}?>

        $(document).keydown(function(e) {
            switch(e.which) {
                case 37: // left
                break;

                case 38: // up
                    if ($('input[data-list=".input.radio.filter2"]').is(":focus") == true) {
                        if ($(".selectTwitterAccount.hover").length) {
                            alt = $(".selectTwitterAccount.hover").first().attr('alt');
                            $(".selectTwitterAccount.hover").first().removeClass('hover');
                            if ($('.selectTwitterAccount[alt="' + alt + '"]').prev(":not(.checked)").length) {
                                $('.selectTwitterAccount[alt="' + alt + '"]').prev(":not(.checked)").addClass('hover');
                            } else {
                                $('input[data-list=".input.radio.filter2"]').closest(".filter").find('.selectTwitterAccount:not(.checked)').last().addClass('hover');
                            }
                        } else {
                            $('input[data-list=".input.radio.filter2"]').closest(".filter").find('.selectTwitterAccount:not(.checked)').last().addClass('hover');
                        }

                        e.preventDefault(); // prevent the default action (scroll / move caret)
                    }

                    if ($('input[data-list=".input.radio.filter1"]').is(":focus") == true) {
                        if ($(".selectTeam.hover").length) {
                            alt = $(".selectTeam.hover").first().attr('alt');
                            $(".selectTeam.hover").first().removeClass('hover');
                            if ($('.selectTeam[alt="' + alt + '"]').prev(":not(.checked)").length) {
                                $('.selectTeam[alt="' + alt + '"]').prev(":not(.checked)").addClass('hover');
                            } else {
                                $('input[data-list=".input.radio.filter1"]').closest(".filter").find('.selectTeam:not(.checked)').last().addClass('hover');
                            }
                        } else {
                            $('input[data-list=".input.radio.filter1"]').closest(".filter").find('.selectTeam:not(.checked)').last().addClass('hover');
                        }

                        e.preventDefault(); // prevent the default action (scroll / move caret)
                    }
                break;

                case 39: // right
                break;

                case 40: // down
                    if ($('input[data-list=".input.radio.filter2"]').is(":focus") == true) {
                        if ($(".selectTwitterAccount.hover").length) {
                            alt = $(".selectTwitterAccount.hover").first().attr('alt');
                            $(".selectTwitterAccount.hover").first().removeClass('hover');
                            if ($('.selectTwitterAccount[alt="' + alt + '"]').next(":not(.checked)").length) {
                                $('.selectTwitterAccount[alt="' + alt + '"]').next(":not(.checked)").addClass('hover');
                            } else {
                                $('input[data-list=".input.radio.filter2"]').closest(".filter").find('.selectTwitterAccount:not(.checked)').first().addClass('hover');
                            }
                        } else {
                            $('input[data-list=".input.radio.filter2"]').closest(".filter").find('.selectTwitterAccount:not(.checked)').first().addClass('hover');
                        }

                        e.preventDefault(); // prevent the default action (scroll / move caret)
                    }

                    if ($('input[data-list=".input.radio.filter1"]').is(":focus") == true) {
                        if ($(".selectTeam.hover").length) {
                            alt = $(".selectTeam.hover").first().attr('alt');
                            $(".selectTeam.hover").first().removeClass('hover');
                            if ($('.selectTeam[alt="' + alt + '"]').next(":not(.checked)").length) {
                                $('.selectTeam[alt="' + alt + '"]').next(":not(.checked)").addClass('hover');
                            } else {
                                $('input[data-list=".input.radio.filter1"]').closest(".filter").find('.selectTeam:not(.checked)').first().addClass('hover');
                            }
                        } else {
                            $('input[data-list=".input.radio.filter1"]').closest(".filter").find('.selectTeam:not(.checked)').first().addClass('hover');
                        }

                        e.preventDefault(); // prevent the default action (scroll / move caret)
                    }
                break;

                case 13: // enter
                    if ($('input[data-list=".input.radio.filter2"]').is(":focus") == true) {
                        if ($(".selectTwitterAccount.hover").length) {
                            $(".selectTwitterAccount.hover").find('input').attr("checked", "checked");
                            $(".selectTwitterAccount.hover").closest('#filterIndexForm').submit();
                        }
                        e.preventDefault();
                    }

                    if ($('input[data-list=".input.radio.filter1"]').is(":focus") == true) {
                        if ($(".selectTeam.hover").length) {
                            $(".selectTeam.hover").find('input').attr("checked", "checked");
                            $(".selectTeam.hover").closest('#filterIndexForm').submit();
                        }
                        e.preventDefault();
                    }
                break;

                default: return; // exit this handler for other keys
            }
        });
});
</script>