<div></div>
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
?><hr>
<?
echo $this->Form->create('currentmonth', array('url' => array('controller' => 'twitter', 'action' => 'admin'), 'id' => 'monthForm'));
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
    'onchange' => 'this.form.submit()'
    ));
echo $this->Form->end();

?>
<? echo $this->Form->button('Shorten all URLs', array('id' => 'shortIt1', 'class' => 'urlSubmit1', 'type' => 'button'));
echo $this->Form->create('Tweet', array('url' => '/editorial_calendars/editcalendartweet', 'id' => 'submitTweets'));
?>

<?php if (!empty($calendar)) { ?>
<table id='refresh'>
<thead class="mainheader">
    <th>Scheduled</th>
    <th>Writer</th>
    <th>Topic</th>
    <th>Tweet</th>
    <th>Status</th>
</thead>
<?
$testid = 1;
foreach ($days as $key => $value) { ?>
<tr class='divider'><td style="border:none"></td></tr>
<thead>
    <th class='day first'><b> <? echo $value; ?></b></th>
    <th class='day'></th>
    <th class='day'></th>
    <th class='day'></th>
    <th class='day last'></th>
</thead>
<?php
foreach ($calendar as $key1) {
    $testid = $testid + 1;
    echo '<tr>';

    foreach ($key1['Tweet'] as $key2) {
        if ($key2['time'] === date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time']))) {
            $value2 = $key2['body'];
            $value1 = $testid;
            $id = $key2['id'];
            $body = $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing'));
            $firstName = $key2['first_name'];
            $verified = $key2['verified'];
            $verified_by = $key2['verified_by'];
            break;
        } else {
            $value2 = '';
            $value1 = $testid;
            $id = '';
            $body = $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing')); 
            $firstName = '';
            $verified = 3;
            $verified_by = "";
        }
    }

    if ($key1['Tweet'] == false) {
        $value2 = '';
        $value1 = $testid;
        $id = '';
        $body = $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'editing')); 
        $firstName = '';
        $verified = 3;
            $verified_by = "";
    }


        if ($this->Session->read('Auth.User.group_id') == 2) {
            $disabled = 'disabled';
        } else {
            $disabled = '';
        }?>
    <td class="scheduled"><? echo date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time']));?> </td>
    <td class="writtenBy"><? echo $firstName; ?> </td>
    <td class="topic"><b><? echo $key1['EditorialCalendar'][strtolower($value) . '_topic']; ?></b></td>
    <td class="nopadding">
    <?echo $body;?>
        <div class="tweetButtons">
            <? echo $this->Form->button('Save', array('type' => 'submit', 'class' => 'smallSaveButton'));?>
            <? echo $this->Form->button('Shorten URLs', array('class' => 'urlSubmit1 shortsingle', 'type' => 'button')); ?>
            <? //echo $this->Form->input('Tweet.img_url', array('type' => 'file')); ?>
            <? //add a button, onclick show dialog with input, upload?>
        </div>
    </td>
    <td class="verified"><? echo $this->Form->input('verified', array('type' => 'radio', 'options' => array(1 => 'APPROVED', 0 => 'AWAITING APPROVAL', 2 => 'IMPROVE'), 'legend' => false, 'name' => 'data[Tweet]['.$value1.'][verified]', 'class' => 'TwitterVerified1', 'id' => $id, 'default' => $verified, $disabled));?> 
        <? if ($verified == 1) {?>
        <i><small>-<? echo $verified_by;?></small></i>
        <?}?></td>
    <?
    echo $this->Form->input('timestamp', array('type' => 'hidden', 'value' => date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])), 'name' => 'data[Tweet]['.$value1.'][timestamp]'));
    echo $this->Form->input('id', array('type' => 'hidden', 'value' => $id, 'name' => 'data[Tweet]['.$value1.'][id]'));
    echo $this->Form->input('calendar_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['id'], 'name' => 'data[Tweet]['.$value1.'][calendar_id]'));
    echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['team_id'], 'name' => 'data[Tweet]['.$value1.'][team_id]'));
    echo $this->Form->input('verfied_by', array(
    'type' => 'hidden', 
    'value' => $this->Session->read('Auth.User.first_name'), 
    'name' => 'data[Tweet]['.$value1.'][verified_by]', 
    'class' => 'verifiedby', 
    'id' => $id . '_' . $this->Session->read('Auth.User.first_name')));
    echo '</tr>';
}
}
?>
</table>
<? echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save', 'class' => 'longbutton')); }?>



<script> 
        // wait for the DOM to be loaded 
        $(document).ready(function () {
            $(".TwitterVerified1:checked").each( function() {
                if ($(this).attr('value') == 0) {
                    color = '#ffcc00';
                } else if ($(this).attr('value') == 1) {
                    color = '#21a750';
                } else if ($(this).attr('value') == 2) {
                    color = '#ff0000';
                }
                $(this).closest( "tr" ).find('#TweetBody').css("border", "1px solid" + color);
            });

            $(".verifiedby").prop('disabled', true);

            $('#table1').on('click', '#tweetsubmit', function() {
                $('#submitTweets').submit();
            });

            $('#table1').on('change', '#monthSelector', function() {
                $('#monthForm').submit(); 
            });

            $('#table1').on('change', '.TwitterVerified1', function() {
                if ($(this).prop('checked') == true) {
                    id = $(this).attr('id');
                    id = id.slice(0, -1);
                    $("#" + id + "_" + "<? echo $this->Session->read('Auth.User.first_name'); ?>").prop('disabled', false);


                    if ($(this).attr('value') == 0) {
                        color = '#ffcc00';
                    } else if ($(this).attr('value') == 1) {
                        color = '#21a750';
                    } else if ($(this).attr('value') == 2) {
                        color = '#ff0000';
                    }
                    $(this).closest( "tr" ).find('#TweetBody').css("border", "1px solid" + color);
                }
            });

            //shorten all URLs
            $("#shortIt1").click(function () {
                //$("#shortUrlInfo").html("<img src='images/loading.gif'/>");
                regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
                $(".editing").each(function() {
                    var longUrlLink = $(this).val().match(regex);
                    //split = longUrlLink.split(",");
                    //alert(split[1]);
                    var $this = $(this);
                    jQuery.urlShortener({
                        longUrl: longUrlLink,
                        success: function (shortUrl) {
                            $this.val($this.val().replace(longUrlLink, shortUrl));
                        },
                        error: function(err) {
                            $("#shortUrlInfo").html(JSON.stringify(err));
                        }
                    });

                });
            });

            //shorten single URL
            $(".shortsingle").click(function () {
                regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
                textbox = $(this).closest('.nopadding').children('.editing');
                var longUrlLink = textbox.val().match(regex);
                    jQuery.urlShortener({
                        longUrl: longUrlLink,
                        success: function (shortUrl) {
                            textbox.val(textbox.val().replace(longUrlLink, shortUrl));
                        },
                        error: function(err) {
                            $("#shortUrlInfo").html(JSON.stringify(err));
                        }
                    });
            });

            $('.editing').charCount({css: 'counter counter1'});

            warnMessage = "You have unsaved changes on this page, if you leave your changes will be lost.";
            $(".editing").on('change', function () {
                window.onbeforeunload = function () {
                    if (warnMessage != null) return warnMessage;
                }
            });

            $('input:submit, button:submit').on('click', function() {
                warnMessage = null;
            });
        });

</script>