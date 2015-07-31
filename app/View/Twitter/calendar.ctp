    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
    <script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
    <?php echo $this->Html->script('charCount');
    echo $this->Html->script('jquery-ui-1.10.3.custom'); 
    echo $this->Html->script('jquery-ui-timepicker-addon');
    echo $this->Html->script('jquery.urlshortener');
    echo $this->Html->script('mindmup-editabletable');
    echo $this->Html->css('calendar'); ?>

<style>
#AddTweet:disabled {opacity: .4}
</style>

<?php
echo $this->Session->flash('auth');
$days = array(
    0 => 'monday',
    1 => 'tuesday',
    2 => 'wednesday',
    3 => 'thursday',
    4 => 'friday',
    5 => 'saturday',
    6 => 'sunday'
);
//Select Twitter Account
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
        )); 
echo $this->Form->end();
if (isset($info[0]['TwitterAccount']['infolink'])) {
echo $this->Html->link('Info', $info[0]['TwitterAccount']['infolink'], array('target' => '_blank'));
} ?>

<br/>
<br/>

<?php
if ($this->Session->read('access_token.account_id') !== null) {
echo $this->Form->create('calendar_activated');
echo $this->Form->input('calendar_activated', array(
    'type' => 'select',
    'label' => 'Activate Editorial Calendars for your team?',
    'options' => array(
        0 => 'No',
        1 => 'Yes'),
    'onchange' => 'this.form.submit()',
    'selected' => $this->Session->read('Auth.User.calendar_activated')));
echo $this->Form->end();
//Table
echo $this->Form->create('Calendar', array('url'=>$this->Html->url(array('controller'=>'editorial_calendars', 'action'=>'calendarsave')), 'id' => 'edit'));?>
<table id="table1">
<th class="tableheader"></th>
<th class="tableheader monday"><p class='dayheading'>Monday</p></th>
<th class="tableheader tuesday"><p class='dayheading'>Tueday</p></th>
<th class="tableheader wednesday"><p class='dayheading'>Wednesday</p></th>
<th class="tableheader thursday"><p class='dayheading'>Thursday</p></th>
<th class="tableheader friday"><p class='dayheading'>Friday</p></th>
<th class="tableheader saturday"><p class='dayheading'>Saturday</p></th>
<th class="tableheader sunday"><p class='dayheading'>Sunday</p></th>


<?
foreach ($calendar as $value => $key) {?>
    <tr class="topic">
    <!-- AJAX HIDDEN INPUT FOR EACH CALENDAR WITH TIME name used to be 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .']['. $key1 .']'-->
        <td><b><? echo $this->Form->input('Time', array('value' => $value, 'name' => '', 'class' => 'CalendarTimeMain')); ?></b></td>
        <?
        /*foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'topic')) {
                    echo '<td>' . $this->Form->textarea($value1, array(
                        'name' => 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .']['. $key1 .']',
                        'value' => $value1,
                        'label' => false)) . '</td>';
                }
            }
        }*/?>

        <?
        foreach ($days as $key1 => $value1) {?>
        <td><? echo  $this->Form->input('category', array('type' => 'select', 'options' => $bank_categories, 'selected' => $key[$value1]['EditorialCalendar']['bank_category_id'], 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][bank_category_id]')); ?></td>
        <? echo $this->Form->input('time', array('type' => 'hidden', 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][time]', 'value' => $value, 'class' => 'CalendarTime'));?>
        <?}?>
    </tr>
    <!--<tr class="content-type">
        <td>Content type</td>
        <?
        foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'content')) {
                    echo '<td>' . $this->Form->textarea($value1, array(
                        'name' => 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .']['. $key1 .']',
                        'value' => $value1,
                        'label' => false)) . '</td>';
                }
            }
        }?>
    </tr>
    <tr class="notes">
        <td>Notes</td>
        <?
        foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'notes')) {
                    echo '<td>' . $this->Form->textarea($value1, array(
                        'name' => 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .']['. $key1 .']',
                        'value' => $value1,
                        'label' => false)) . '</td>';
                }
            }
        }?>
    </tr>-->
    <tr style="height:20px"><td><div class='deleteimage'><?php echo $this->Html->Link('Delete', array('controller' => 'editorial_calendars', 'action' => 'deletecalendar', $this->Session->read('access_token.account_id'), str_replace(":", "", $value))); ?></div></td></tr>
<?
}
?><tr>
    <td><b><? echo $this->Form->input('Time', array('value' => '00:00', 'name' => '', 'class' => 'CalendarTimeMain')); ?></b></td>
    <?foreach ($days as $key1 => $value1) {?>
    <td><? echo  $this->Form->input('category', array('type' => 'select', 'options' => $bank_categories, 'name' => 'data[EditorialCalendar][' . uniqid() . '][bank_category_id]')); ?></td>
        <? echo $this->Form->input('time', array('type' => 'hidden', 'name' => 'data[EditorialCalendar][' . uniqid() . '][time]', 'value' => '00:00', 'class' => 'CalendarTime'));?>
        <? echo $this->Form->input('day', array('type' => 'hidden', 'name' => 'data[EditorialCalendar][' . uniqid() . '][day]', 'value' => $value1));?>
    <?}?>
</tr>
</table>
<?php 
//echo $this->Html->Link('Add +', array('controller' => 'editorial_calendars', 'action' => 'addCalendar'), array('style' => 'font-weight:bold', 'class' => 'UrlSubmit add'));
echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save'));
echo $this->Form->button('Add +', array('style' => 'font-weight:bold', 'class' => 'urlSubmit add')); ?>

<? 
$base = strtotime(date('Y-m',time()) . '-01 00:00:01');
if (!isset($months)) {
    $months = 0;
}
$daysinmonth = (int)date('t', strtotime('+' . $months . ' month', $base));
$days = array();
$month = date('m', strtotime('+' . $months . ' month', $base));
if ($months == 0) {
    $day = date('d');
} elseif ($months !== 0) {
    $day = 1;
} 
$year = date('Y');

$count = $daysinmonth - $day;
for ($i=$day; $i<=$daysinmonth; $i++) {
    $days[date('d-m-Y',mktime(0,0,0,$month,$i,$year))] = date('l',mktime(0,0,0,$month,$i,$year));
}

?><!--
echo $this->Form->input('Select Month', array(
    'options' => array(
        0 => date('F Y', strtotime('+0 month', $base)),
        1 => date('F Y', strtotime('+1 month', $base)),
        2 => date('F Y', strtotime('+2 month', $base)),
        3 => date('F Y', strtotime('+3 month', $base)),
        4 => date('F Y', strtotime('+4 month', $base)),
        5 => date('F Y', strtotime('+5 month', $base))
        ),
    'selected' => $months,
    'id' => 'monthSelector',
    'onchange' => 'window.location.replace("/twitter/calendar/" + this.value)'
    ));

echo $this->Form->create('Tweet', array('url' => array('controller' => 'editorial_Calendars', 'action' => 'editcalendartweet'), 'id' => 'submitTweets'));
?>

<table>
<tr>
<th></th>
<th>Tweet</th>
<th>Written By</th>
<th>Verified</th>
<th>Scheduled</th>
</tr>
<?
$testid = 1;
foreach ($days as $key => $value) { ?>
<tr>
<th class='day'><b> <? echo $value; ?></b></th>
<th class='day'></th>
<th class='day'></th>
<th class='day'></th>
<th class='day'></th>
</tr>
<?php
foreach ($calendar as $key1) {
    $testid = $testid + 1;
    echo '<tr>';
    echo '<td class="topic"><b>' . $key1['EditorialCalendar'][strtolower($value) . '_topic'] . '</b></td>';

    foreach ($key1['Tweet'] as $key2) {
        if ($key2['time'] === date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time']))) {
            $value2 = $key2['body'];
            $value1 = $testid;
            $id = $key2['id'];
            echo '<td class="nopadding">' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing')) . '</td>';
            $body = '';
            $firstName = $key2['first_name'];
            $verified = $key2['verified'];
            break;
        } else {
            $value2 = '';
            $value1 = $testid;
            $id = '';
            $body = '<td class="nopadding">' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing')) . '</td>'; 
            $firstName = '';
            $verified = 0;
        }
    }

    if ($key1['Tweet'] == false) {
        $value2 = '';
        $value1 = $testid;
        $id = '';
        $body = '<td class="nopadding">' . $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing')) . '</td>'; 
        $firstName = '';
        $verified = 0;
    }


        if ($verified == 1) {
            $checked = 'checked';
        } else {
            $checked = '';
        }

    echo $body;
    echo '<td class="writtenBy">' . $firstName . '</td>';
    echo '<td class="verified">' . $this->Form->input('verified', array('type' => 'checkbox', 'label' => false, 'name' => 'data[Tweet]['.$value1.'][verified]', $checked)) . '</td>'; 
    echo '<td class="scheduled">' . date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])) . '</td>';
    echo $this->Form->input('timestamp', array('type' => 'hidden', 'value' => date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])), 'name' => 'data[Tweet]['.$value1.'][timestamp]'));
    echo $this->Form->input('id', array('type' => 'hidden', 'value' => $id, 'name' => 'data[Tweet]['.$value1.'][id]'));
    echo $this->Form->input('calendar_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['id'], 'name' => 'data[Tweet]['.$value1.'][calendar_id]'));
    echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['team_id'], 'name' => 'data[Tweet]['.$value1.'][team_id]'));
    echo '</tr>';
}
}
?>
</table>
<? echo $this->Form->end('Save');}?>

<?php
//Select Twitter Account
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
        )); 
echo $this->Form->end();?>

<?php echo $this->Html->link('Add Twitter Account', '/twitter/connect');?> <br />
<?php echo $this->Html->link('Logout', '/users/logout');?>
-->

<!-- SCRIPTS -->
<script> 
// wait for the DOM to be loaded 
$(document).ready(function () { 
    $('.CalendarTimeMain').change(function () {
        var time = $(this).val();
        $(this).closest("tr").find(".CalendarTime").val(time);
    });

    $('.add').click(function () {
        $('tr:last-child').after("<tr><td><? echo $this->Form->input('hello');?></td></tr>");
    });

    /*"<tr>
    <td><b><? echo $this->Form->input('Time', array('value' => '00:00', 'name' => '', 'class' => 'CalendarTimeMain')); ?></b></td>
    <?foreach ($days as $key1 => $value1) {?>
    <td><? echo  $this->Form->input('category', array('type' => 'select', 'options' => $bank_categories, 'name' => 'data[EditorialCalendar][' . uniqid() . '][bank_category_id]')); ?></td>
        <? echo $this->Form->input('time', array('type' => 'hidden', 'name' => 'data[EditorialCalendar][' . uniqid() . '][time]', 'value' => '00:00', 'class' => 'CalendarTime'));?>
        <? echo $this->Form->input('day', array('type' => 'hidden', 'name' => 'data[EditorialCalendar][' . uniqid() . '][day]', 'value' => $value1));?>
    <?}?>
</tr>"*/
});

</script>
<style>
#table1 {
    font-size: 100%;
    border-spacing: 3px;
    color: black;
}
.tableheader {
    background: #EBEBEB;
    width: 7%;
}
.monday {
    background: #ABEADF;
    width: 10%;
}
.tuesday {
    background: #F7DFFF;
    width: 10%;
}
.wednesday {
    background: #AEE27E;
    width: 10%;
}
.thursday {
    background: #EDBF84;
    width: 10%;
}
.friday {
    background: #FCDAD9;
    width: 10%;
}
.saturday {
    background: #F5EDA4;
    width: 10%;
}
.sunday {
    background: #A2EDF3;
    width: 10%;
}
.dayheading {
    margin: 0 auto;
    padding: 5px;
    display: table;
}
.input.text {
    font-size: 65%!important;
}
#TweetBody {
    font-size: 100%;
}

td {
    border: 1px solid #e4e4e4;
    text-align: center;
    height: 48px
}

textarea {
    height: 100%;
    width: 100%;
    font-size: 100%;
}
</style>