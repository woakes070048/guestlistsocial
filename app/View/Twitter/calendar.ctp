    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
    <script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
    <?php echo $this->Html->script('charCount');
    echo $this->Html->script('jquery-ui-1.10.3.custom'); 
    echo $this->Html->script('jquery-ui-timepicker-addon');
    echo $this->Html->script('jquery.urlshortener');
    echo $this->Html->script('toastr.min'); 
    echo $this->Html->script('jquery.qtip.min');
    echo $this->Html->script('masonry.pkgd.min');
    echo $this->Html->css('jquery.qtip.min');
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
//Table
echo $this->Form->create('Calendar', array('url'=>$this->Html->url(array('controller'=>'editorial_calendars', 'action'=>'calendarsave')), 'id' => 'edit'));?>
<table id="table1">
<th class="tableheader"></th>
<th class="tableheader monday"><p class='dayheading'>Mon</p></th>
<th class="tableheader tuesday"><p class='dayheading'>Tue</p></th>
<th class="tableheader wednesday"><p class='dayheading'>Wed</p></th>
<th class="tableheader thursday"><p class='dayheading'>Thu</p></th>
<th class="tableheader friday"><p class='dayheading'>Fri</p></th>
<th class="tableheader saturday"><p class='dayheading'>Sat</p></th>
<th class="tableheader sunday"><p class='dayheading'>Sun</p></th>


<?
foreach ($calendar as $value => $key) {?>
    <tr class="topic">
    <!-- AJAX HIDDEN INPUT FOR EACH CALENDAR WITH TIME name used to be 'data[EditorialCalendar]['. $key['EditorialCalendar']['id'] .']['. $key1 .']'-->
        <td><b><? echo $this->Form->input('Time', array('value' => $value, 'name' => '', 'class' => 'CalendarTimeMain', 'label' => false)); ?></b>
        <?php echo $this->Html->Link($this->Html->image('/img/trash.png'), array('controller' => 'editorial_calendars', 'action' => 'deletecalendar', $this->Session->read('access_token.account_id'), str_replace(":", "", $value)), array('escape' => false, 'class' => 'deleteimage1')); ?></td>
        <??>

        <?
        foreach ($days as $key1 => $value1) {?>
        <td>
        <i class='fa fa-pencil editCategory'></i>
        <div class='BankCategory'><span data-id="<?echo $key[$value1]['BankCategory']['id'];?>" style="background-color:<?echo $key[$value1]['BankCategory']['color'];?>"><?echo (!empty($key[$value1]['BankCategory']['category']))? $key[$value1]['BankCategory']['category'] : 'Select Category...';?></span></div>
        <? //echo  $this->Form->input('category', array('type' => 'select', 'options' => $bank_categories, 'selected' => $key[$value1]['EditorialCalendar']['bank_category_id'], 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][bank_category_id]', 'class' => 'CalendarCategory', 'label' => false)); ?>
        <? echo  $this->Form->input('category', array('type' => 'hidden', 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][bank_category_id]', 'class' => 'CalendarCategory')); ?>
        <? echo  $this->Form->input('category', array('name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][bank_category_manual]', 'label' => false, 'placeholder' => 'Enter New Category', 'style' => 'display: none;', 'id' => 'HiddenCategory', 'disabled')); ?>
        <? echo $this->Form->input('time', array('type' => 'hidden', 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][time]', 'value' => $value, 'class' => 'CalendarTime'));?>
        <? echo $this->Form->input('changed', array('type' => 'hidden', 'name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][changed]', 'value' => false, 'class' => 'changed'));?>
        <?}?></td>
    </tr>
<?
}
?>
<tr id='copy' class='topic' style='display: none'>
    <td><b><? echo $this->Form->input('Time', array('value' => '00:00', 'name' => '', 'class' => 'CalendarTimeMain', 'label' => false)); ?></b>
    <?php echo $this->Html->Link($this->Html->image('/img/trash.png'), "",array('escape' => false, 'class' => 'deleteimagenew')); ?></td>
    <?foreach ($days as $key1 => $value1) {?>
    <td>
        <i class='fa fa-pencil editCategory'></i>
        <? echo  $this->Form->input('category', array('name' => '', 'label' => false, 'placeholder' => 'Enter New Category', 'style' => 'display: none;', 'id' => 'HiddenCategory', 'disabled')); ?>
        <div class='BankCategory'><span data-id="new"><?echo 'Select Category...';?></span></div>
        <? echo  $this->Form->input('category', array('type' => 'hidden', 'name' => 'data[EditorialCalendar][new][bank_category_id]', 'class' => 'CalendarCategory')); ?>
        <? echo  $this->Form->input('category', array('name' => 'data[EditorialCalendar]['. $key[$value1]['EditorialCalendar']['id'] .'][bank_category_manual]', 'label' => false, 'placeholder' => 'Enter New Category', 'style' => 'display: none;', 'id' => 'HiddenCategory', 'disabled')); ?>
        <? echo $this->Form->input('time', array('type' => 'hidden', 'name' => '', 'value' => '00:00', 'class' => 'CalendarTime'));?>
        <? echo $this->Form->input('day', array('type' => 'hidden', 'name' => '', 'value' => $value1));?>
        <? echo $this->Form->input('changed', array('type' => 'hidden', 'name' => '', 'value' => true, 'class' => 'changed'));?>
        </td>
    <?}?>
</tr>
</table>
<?php
echo $this->Form->button('Add', array('class' => 'addCalendar')); 
echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save')); }?>

<div id="refresh">
</div>


<!-- SCRIPTS -->
<script> 
// wait for the DOM to be loaded 
$(document).ready(function () { 
    $('.CalendarTimeMain').timepicker();
    $('.CalendarTimeMain').change(function () {
        var time = $(this).val();
        $(this).closest("tr").find(".CalendarTime").val(time);
    });

    /*$('.add').click(function () {
        $('tr:last-child').after("<tr><td><? echo $this->Form->input('hello');?></td></tr>");
    });*/

    $('.addCalendar').click(function (e) {
        e.preventDefault();
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
        $('tr:last-child .editCategory').qtip({
            content: {
                text: "<?foreach ($bank_categories as $value => $key) {?><span style='background-color:<?if($value != 0 || $value != 'New'){echo$bank_category_colors[$value];}?>' data-id=<?echo$value;?> class='categorySelection'><?echo $key;?></span><?}?>", 
                button: true
            },
            hide: {
                event: false
            },
            position: {
                my: 'left top',
                at: 'right center', 
                target: 'event'
            },
            show: 'click'
        });
        $('.topic td').hover(function () {
            $(this).find('.editCategory').css('opacity', 1);
        }, function () {
            $(this).find('.editCategory').css('opacity', 0);
        });
    });

    $('.deleteimage1').click(function (e) {
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

    $('#tweetsubmit').click(function (e) {
        e.preventDefault();
        $('#edit').ajaxSubmit({
            success: function() {
                toastr.success('Saved successfully.');
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

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

    $('.topic .editCategory').qtip({
        content: {
            text: "<?foreach ($bank_categories as $value => $key) {?><span style='background-color:<?if($value != 0 || $value != 'New'){echo$bank_category_colors[$value];}?>' data-id=<?echo$value;?> class='categorySelection'><?echo $key;?></span><?}?>", 
            button: true
        },
        hide: {
            event: false
        },
        position: {
            my: 'left top',
            at: 'right center', 
            target: 'event'
        },
        show: 'click'
    });

    $('body').on('click', '.categorySelection', function () {
        text = $(this).text();
        id = $(this).attr('data-id');
        color = $(this).css('background-color');
        qtip_id = $(this).closest('.qtip').attr('id');
        qtip_id = qtip_id.substr(5);
        $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('td').find('.BankCategory span').text(text);
        $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('td').find('.BankCategory span').css('background-color', color);
        $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('td').find('.BankCategory span').attr('data-id', id);
        $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('td').find('.CalendarCategory').val(id);
        $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('tr').find('.changed').attr('value', true);

        if (id == 'New') {
            $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('td').find('#HiddenCategory').show();
            $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('td').find('#HiddenCategory').prop('disabled', false);
        } else {
            $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('td').find('#HiddenCategory').hide();
            $(".editCategory[data-hasqtip=" + qtip_id + "]").closest('td').find('#HiddenCategory').prop('disabled', true);
            $('#refresh').load('/tweetBank/calendarrefresh/' + id);
            $('td').css('background-color', '#fff');
            $(this).closest('td').css('background-color', '#fffca1');
        }
    });

    $('.BankCategory').click(function () {
        id = $(this).find('span').attr('data-id');
        $('#refresh').load('/tweetBank/calendarrefresh/' + id);
        $('td').css('background-color', '#fff');
        $(this).closest('td').css('background-color', '#fffca1');
    });

    $('.topic td').hover(function () {
        $(this).find('.editCategory').css('opacity', 1);
    }, function () {
        $(this).find('.editCategory').css('opacity', 0);
    });
});

</script>
<style>
#table1 {
    font-size: 25px;
    border-spacing: 0px;
    color: black;
}
.tableheader {
    background: #fff;
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

td, th {
    border: 1px solid #afafaf;
    border-width: 1px 1px 0 0;
    text-align: left;
    font-weight: normal;
}

td {
    padding: 5px;
    cursor: pointer;
}

td:first-child .input.text {
    display: inline-block;
}

td:first-child,
th:first-child {
    border: none;
}

td:nth-child(2),
th:nth-child(2) {
    border-left: 1px solid #afafaf;
}

th:nth-child(2) {
   border-top-left-radius: 5px; 
}

th:last-child {
    border-top-right-radius: 5px;
}

tr:nth-last-child(2) td:not(:first-child),
tr:last-child td:not(:first-child) {
    border-bottom: 1px solid #afafaf;
}

tr.topic {
    height: 70px;
}

textarea {
    height: 100%;
    width: 100%;
    font-size: 100%;
}

.submit {
    display: inline-block;
}
</style>