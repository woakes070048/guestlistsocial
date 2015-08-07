    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
    <script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
    <?php echo $this->Html->script('charCount');
    echo $this->Html->script('jquery-ui-1.10.3.custom'); 
    echo $this->Html->script('jquery-ui-timepicker-addon');
    echo $this->Html->script('jquery.urlshortener');
    echo $this->Html->script('toastr.min'); 
    echo $this->Html->css('calendar');
    echo $this->Html->css('toastr.min'); ?>

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
        <td><b><? echo $this->Form->input('Time', array('value' => $value, 'name' => '', 'class' => 'CalendarTimeMain')); ?></b>
        <?php echo $this->Html->Link($this->Html->image('/img/trash.png'), array('controller' => 'editorial_calendars', 'action' => 'deletecalendar', $this->Session->read('access_token.account_id'), str_replace(":", "", $value)), array('escape' => false, 'class' => 'deleteimage')); ?></td>
        <??>

        <?
        foreach ($days as $key1 => $value1) {?>
        <td><? echo  $this->Form->input('category', array('type' => 'select', 'options' => $bank_categories, 'selected' => $key[$value1]['EditorialCalendar']['bank_category_id'], 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][bank_category_id]', 'class' => 'CalendarCategory')); ?>
        <? echo  $this->Form->input('category', array('name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][bank_category_manual]', 'label' => false, 'placeholder' => 'Enter New Category', 'style' => 'display: none;', 'id' => 'HiddenCategory', 'disabled')); ?>
        <? echo $this->Form->input('time', array('type' => 'hidden', 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][time]', 'value' => $value, 'class' => 'CalendarTime'));?>
        <? echo $this->Form->input('changed', array('type' => 'hidden', 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][changed]', 'value' => false, 'class' => 'changed'));?>
        <?}?></td>
    </tr>
<?
}
?>
<tr id='copy' style='display: none'>
    <td><b><? echo $this->Form->input('Time', array('value' => '00:00', 'name' => '', 'class' => 'CalendarTimeMain')); ?></b>
    <?php echo $this->Html->Link($this->Html->image('/img/trash.png'), "",array('escape' => false, 'class' => 'deleteimagenew')); ?></td>
    <?foreach ($days as $key1 => $value1) {?>
    <td><? echo  $this->Form->input('category', array('type' => 'select', 'options' => $bank_categories, 'name' => '', 'class' => 'CalendarCategory')); ?>
        <? echo  $this->Form->input('category', array('name' => '', 'label' => false, 'placeholder' => 'Enter New Category', 'style' => 'display: none;', 'id' => 'HiddenCategory', 'disabled')); ?>
        <? echo $this->Form->input('time', array('type' => 'hidden', 'name' => '', 'value' => '00:00', 'class' => 'CalendarTime'));?>
        <? echo $this->Form->input('day', array('type' => 'hidden', 'name' => '', 'value' => $value1));?>
        <? echo $this->Form->input('changed', array('type' => 'hidden', 'name' => '', 'value' => true, 'class' => 'changed'));?>
        </td>
    <?}?>
</tr>
</table>
<?php 
echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save'));
echo $this->Form->button('Add +', array('style' => 'font-weight:bold', 'class' => 'urlSubmit add')); }?>


<!-- SCRIPTS -->
<script> 
// wait for the DOM to be loaded 
$(document).ready(function () { 
    $('.CalendarTimeMain').change(function () {
        var time = $(this).val();
        $(this).closest("tr").find(".CalendarTime").val(time);
    });

    /*$('.add').click(function () {
        $('tr:last-child').after("<tr><td><? echo $this->Form->input('hello');?></td></tr>");
    });*/

    $('.add').click(function () {
        $('tr#copy').clone().removeAttr('id').appendTo('#table1 tbody');
        $('tr:last-child').attr('style', 'display:table row');
        $('tr:last-child td').each(function () {
            random = (Math.random() + 1).toString(36).substring(7);;
            $(this).find("#CalendarCategory").attr('name', 'data[EditorialCalendar][' + random + '][bank_category_id]');
            $(this).find("#CalendarDay").attr('name', 'data[EditorialCalendar][' + random + '][day]');
            $(this).find("#CalendarTime").attr('name', 'data[EditorialCalendar][' + random + '][time]');
            $(this).find("#CalendarChanged").attr('name', 'data[EditorialCalendar][' + random + '][changed]');
            $(this).find("#HiddenCategory").attr('name', 'data[EditorialCalendar][' + random + '][bank_category_manual]');
            $(this).find('.deleteimagenew').click(function (e) {
                e.preventDefault();
                $(this).closest('tr').find('input, select').prop('disabled', true);
                $(this).closest('tr').hide();
            });
        });
        $('.CalendarTimeMain').change(function () {
        var time = $(this).val();
        $(this).closest("tr").find(".CalendarTime").val(time);
        });
        $('tr:last-child').find('.CalendarCategory').change(function () {
            if ($(this).val() == 'New') {
                $(this).closest('td').find('#HiddenCategory').show();
                $(this).closest('td').find('#HiddenCategory').prop('disabled', false);
            } else {
                $(this).closest('td').find('#HiddenCategory').hide();
                $(this).closest('td').find('#HiddenCategory').prop('disabled', true);
            }
        });
    });

    $('.deleteimage').click(function (e) {
        e.preventDefault();
        url = $(this).attr('href');
        var thisx = $(this);
        $.ajax({
            url: url,
            success: function(data) {
                toastr.success('Deleted successfully.');
                thisx.closest('tr').find('input, select').prop('disabled', true);
                thisx.closest('tr').hide();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                toastr.error('An error occurred. Please try again.');
            }   
        });
    });

    /*$('#tweetsubmit').click(function (e) {
        e.preventDefault();
        $('#edit').ajaxSubmit({
            success: function() {
                toastr.success('Saved successfully.');
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });*/

    $('.CalendarCategory').change(function () {
        if ($(this).val() == 'New') {
            $(this).closest('td').find('#HiddenCategory').show();
            $(this).closest('td').find('#HiddenCategory').prop('disabled', false);
        } else {
            $(this).closest('td').find('#HiddenCategory').hide();
            $(this).closest('td').find('#HiddenCategory').prop('disabled', true);
        }
    });

    $('.CalendarTimeMain, .CalendarCategory').change(function () {
        $(this).closest('tr').find('.changed').attr('value', true);
    });
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