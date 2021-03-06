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
//$year = date('Y', strtotime('+' . $months . ' month', $base));
$year = date('Y');

$count = $daysinmonth - $day;
for ($i=$day; $i<=$daysinmonth; $i++) {
    $days[date('d-m-Y',mktime(0,0,0,$month,$i,$year))] = date('l',mktime(0,0,0,$month,$i,$year));
}
?>
<div id='calendarbuttons'>
<?
echo $this->Form->create('currentmonth', array('url' => array('controller' => 'twitter', 'action' => '../tweets'), 'id' => 'monthForm'));
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
echo $this->Form->button('Approve All', array('class' => 'urlSubmit1 approveAll', 'type' => 'button'));
?>
</div>
<?
echo $this->Form->create('Tweet', array('url' => '/editorial_calendars/editcalendartweet1', 'id' => 'submitTweets', 'type' => 'file'));
?>

<?php if (!empty($calendar)) { ?>
<table id='refresh'>
<thead class="mainheader">
    <th>Scheduled</th>
    <th>Topic</th>
    <th>Tweet</th>
    <th>Status</th>
</thead>
<?
$testid = 1;
$allApproved = array();
foreach ($days as $key => $value) {
$allApproved[date('jS', strtotime($key))] = 0; ?>
<tr class='divider'><td style="border:none"></td></tr>
<thead>
    <th class='day first'></th>
    <th class='day' style='text-align: center'>tweet<b style='color:#4ec3ff; margin: 0;'>PROOF</b> TweetBank</th>
    <th class='day'><b> <? echo strtoupper($value); ?></b></th>
    <th class='day last'></th>
</thead>
<?php
foreach ($calendar as $time => $key1) {
    $testid = $testid + 1;
    echo '<tr>';

    $key1 = $key1[strtolower($value)];
    //debug($key1);
    /*foreach ($key1['Tweet'] as $key2) {
        if ($key2['time'] === date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time']))) {
            $value2 = $key2['body'];
            $value1 = $testid;
            $id = $key2['id'];
            $img = $key2['img_url'];
            $body = $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'calendar editing'));
            $firstName = $key2['first_name'];
            $verified = $key2['verified'];
            $verified_by = $key2['verified_by'];
            $published = $key2['published'];
            break;
        } else {
            $value2 = '';
            $value1 = $testid;
            $id = '';
            $img = '';
            $body = $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'calendar editing')); 
            $firstName = '';
            $verified = 0;
            $verified_by = "";
            $published = false;
        }
    }*/

    //if (empty($tweets[$key1['EditorialCalendar']['id']])) {
    if (!$key1['Tweet']) {
        $value2 = '';
        $value1 = $testid;
        $id = '';
        $idForPusher = md5($this->Session->read('access_token.account_id') . 'x' . $value1);
        $img = '';
        $body = $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'calendar editing withoutImage')); 
        $firstName = '';
        $verified = 0;
        $allApproved[date('jS', strtotime($key))] -= 1000;
        $published = false;
        $commentCount = 0;
        $present = '';
        $editors = false;
    } else {
        //foreach ($tweets[$key1['EditorialCalendar']['id']] as $item => $key2) {
        foreach ($key1['Tweet'] as $item => $key2) {
            if ($key2['time'] === date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time']))) {
                $value2 = $key2['body'];
                $value1 = $testid;
                $id = $key2['id'];
                $idForPusher = $key2['id'];
                $img = $key2['img_url'];
                if (!empty($img)) {
                    $txtareaClass = 'withImage';
                } else {
                    $txtareaClass = 'withoutImage';
                }
                $body = $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'calendar editing ' . $txtareaClass));
                $firstName = $key2['first_name'];
                $verified = $key2['verified'];
                if ($verified == 1) {
                    $allApproved[date('jS', strtotime($key))] += 1;
                } elseif ($verified == 0) {
                    $allApproved[date('jS', strtotime($key))] -= 0.01;
                } elseif ($verified == 2) {
                    $allApproved[date('jS', strtotime($key))] -= 1000000;
                }
                $published = $key2['published'];
                $commentCount = count($key2['Comment']);
                $present = 'present';
                $empty = false;
                if (!empty($key2['Editor'])) {
                    $editors = $key2['Editor'];
                } else {
                    $editors = false;
                }
                //unset($tweets[$key1['EditorialCalendar']['id']][$item]);
                unset($key1['Tweet'][$item]);
                break;
            } else {
                $value2 = '';
                $value1 = $testid;
                $id = '';
                $idForPusher = md5($this->Session->read('access_token.account_id') . 'x' . $value1);
                $img = '';
                $body = $this->Form->textarea('body', array('label' => false, 'value' => $value2, 'name' => 'data[Tweet]['.$value1.'][body]', 'class' => 'calendar editing withoutImage')); 
                $firstName = '';
                $verified = 0;
                //$allApproved[date('jS', strtotime($key))] -= 1000;
                $published = false;
                $commentCount = 0;
                $present = '';
                $editors = false;
                $empty = true;
            }
        }   
        if (!empty($empty)) {
            $allApproved[date('jS', strtotime($key))] -= 1000;
        }
    }

        if ($this->Session->read('Auth.User.group_id') == 2) {
            $disabled = 'disabled';
        } else {
            $disabled = '';
        }?>
    <td class="calendar scheduled <?echo date('jS', strtotime($key . $key1['EditorialCalendar']['time']));?>">
            <? if($published == 1) {
                echo date('d.m.Y', strtotime($key . $key1['EditorialCalendar']['time'])) . '<small>[Published]</small>' . '<br />';
            } else {
                echo date('d.m.Y', strtotime($key . $key1['EditorialCalendar']['time'])) . '<br />';
            }
            echo '<b class="' .date('l', strtotime($key . $key1['EditorialCalendar']['time'])) . '">' . strtoupper(date('l', strtotime($key . $key1['EditorialCalendar']['time']))) . '</b>' . '<br />';

            echo date('H:i', strtotime($key . $key1['EditorialCalendar']['time']));?></td>
    <td class="topic">
    <div class='calendar_topic'><? if (!empty($key1['BankCategory']['category'])) {echo $key1['BankCategory']['category'];} ?></div>

    <!--<div class='calendar_content_type'><? echo $key1['EditorialCalendar'][strtolower($value) . '_content_type']; ?></div>

    <div class='calendar_notes'><? echo $key1['EditorialCalendar'][strtolower($value) . '_notes']; ?></div>--></td>
    <td class="calendar nopadding">
    <?echo $body;?>
        <div class='isTyping'></div>
        <div class="tweetButtons">
        <?if ($commentCount > 9) {
            $commentCount = '9plus';
        }?>
            <div class="empty comments <?echo $present;?>" id="<? echo $id; ?>" style="background-image: url('/img/comment<?echo $commentCount;?>.png')">COMMENTS</div>
            <? echo $this->Form->button('SAVE', array('type' => 'submit', 'class' => 'smallSaveButton', 'type' => 'button'));?>
            <? echo $this->Form->button('SHORTEN URLS', array('class' => 'urlSubmit1 shortsingle', 'type' => 'button')); ?>
            <? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$value1.'][img_url1]', 'label' => false, 'class' => 'imgupload')); ?>
            <? if ($img) { ?>
                    <div class='imagecontainer'>
                        <? echo $this->Html->image($img, array('style' => 'max-width:500px')); ?>
                        <? echo $this->Html->link("<div class='deleteimage'>Delete image</div>", array('controller' => 'twitter', 'action' => 'deleteImage', $id), array('escape' => false));?>
                    </div>
            <?  } else {?>
                <div id="imagePreview<?echo$idForPusher;?>" class='imagecontainer'>
                    <img src='' style='max-width:500px'>
                </div>
            </div>
            <?  }  ?>
    </td>
    <td class="calendar verified"><? echo $this->Form->input('verified', array('type' => 'radio', 'options' => array(1 => 'APPROVED', 0 => 'AWAITING APPROVAL', 2 => 'IMPROVE'), 'legend' => false, 'name' => 'data[Tweet]['.$value1.'][verified]', 'class' => 'calendar TwitterVerified1', 'id' => $id, 'default' => $verified, $disabled));?>

        <ul style='list-style: none; font-size: 8pt; margin: 0; text-align: right'>
        <?if (!empty($editors)) {
            foreach ($editors as $keyx) {
                if ($keyx['type'] == 'written') {
                    $x = 'Written By';
                } elseif ($keyx['type'] == 'edited') {
                    $x = 'Edited By';
                } elseif ($keyx['type'] == 'proofed') {
                    $x = 'Approved By';
                } elseif ($keyx['type'] == 'improve') {
                    $x = 'Set to Improve';
                }?>
            <li style='margin: 0;'><b style='float: left'><? echo $x . ': ' ?></b><? echo $keyx['User']['first_name'];?></li>
            <?}
        }?>
        </ul></td>
    <?
    echo $this->Form->input('timestamp', array('type' => 'hidden', 'value' => date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])), 'name' => 'data[Tweet]['.$value1.'][timestamp]'));
    echo $this->Form->input('id', array('type' => 'hidden', 'value' => $id, 'name' => 'data[Tweet]['.$value1.'][id]', 'data-id' => $idForPusher));
    echo $this->Form->input('calendar_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['id'], 'name' => 'data[Tweet]['.$value1.'][calendar_id]'));
    echo $this->Form->input('img_url', array('type' => 'hidden', 'value' => false, 'name' => 'data[Tweet]['.$value1.'][img_url]'));
    echo $this->Form->input('forceVerified', array('type' => 'hidden', 'value' => false, 'name' => 'data[Tweet]['.$value1.'][forceVerified]'));
    echo $this->Form->input('tosubmit', array('type' => 'hidden', 'value' => false, 'name' => 'tosubmit'));
    //echo $this->Form->input('team_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['team_id'], 'name' => 'data[Tweet]['.$value1.'][team_id]'));
    /*echo $this->Form->input('verfied_by', array(
    'type' => 'hidden', 
    'value' => $this->Session->read('Auth.User.first_name'), 
    'name' => 'data[Tweet]['.$value1.'][verified_by]', 
    'class' => 'verifiedby', 
    'id' => $id . '_' . $this->Session->read('Auth.User.first_name')));*/
    //echo $this->Form->input('img_url', array('type' => 'hidden', 'value' => $img, 'name' => 'data[Tweet]['.$value1.'][img_url]'));
    echo '</tr>';
}
}
?>
</table>
<? echo $this->Form->end(array('id' => 'tweetsubmit', 'label' => 'SAVE', 'value' => 'Save', 'class' => 'longbutton')); }?>

<div class='fixedTwitterAccount' style='display: none;'><span class='screenName'>@<?echo $this->Session->read('access_token.screen_name');?></span></div>
<div class='fixedScroller'>
    <table>
            <tr>
                <td class='gototop'><img src='/img/arrow-up-down.png'></td>
            </tr>
        <?foreach ($days as $key => $value) {
            if ($allApproved[date('jS', strtotime($key))] == count($calendar)) {
                $class = 'allApproved';
            } elseif ($allApproved[date('jS', strtotime($key))] > -1000 && is_float($allApproved[date('jS', strtotime($key))])) {
                $class = 'notAllApproved';
            } elseif ($allApproved[date('jS', strtotime($key))] < -900000) {
                $class = 'improveApproved';
            } elseif ($allApproved[date('jS', strtotime($key))] < 0) {
                $class = '';
            } else {
                $class = '';
            }?>
            <tr>
            <td class='<? echo $class;?>'><?echo date('jS', strtotime($key));?></td>
            </tr>
        <?}?>
    </table>
</div>


<script> 
        // wait for the DOM to be loaded 
        $(document).ready(function () {
            $('.editing.withoutImage').charCount({css: 'counter counter1', allowed: 140});
            $('.editing.withImage').charCount({css: 'counter counter2', allowed: 117});


            var pusher = new Pusher('67904c5b4e0608620f41', { authEndpoint: '/pusher/pusher/auth.json' });

            $(".TwitterVerified1:checked").each( function() {
                if ($(this).val() == 0) {
                    color = '#ffcc00';
                } else if ($(this).val() == 1) {
                    color = '#21a750';
                } else if ($(this).val() == 2) {
                    color = '#ff0000';
                }
                $(this).closest("tr").find('#TweetBody').css("border", "1px solid" + color);
                $(this).closest("tr").find('#TweetBody').css("border-bottom", "none");
                $(this).closest("tr").find('.counter1, .counter2').css("border", "1px solid" + color);
                $(this).closest("tr").find('.counter1, .counter2').css("border-top", "none");
            });

            $(".TwitterVerified1:checked").click(function() {
                $(this).closest("tr").find('input[name=tosubmit]').val(true);
                $(this).closest("tr").find('#TweetForceVerified').val(true);
                    $("#table").css('opacity', '.4');
                    $('#loading').show();
                    var dat = new FormData();
                    $('input[name=tosubmit][value=true]').each(function () {
                        //dat = dat + '&' + $.param($(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked'));
                        $(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
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
                            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                                warnMessage = null;
                                $("#table").css('opacity', '1');
                                $('#loading').hide();
                                toastr.success('Saved successfully');
                                pusher.disconnect();
                            });
                        }
                    });
            });

            $("#table").on("change", ".TwitterVerified1", function() {
                pusher.disconnect();
            });

            //$(".verifiedby").prop('disabled', true);

            $('#table1').on('click', '#tweetsubmit', function() {
                $('#submitTweets').submit();
            });

            $('#table1').on('change', '#monthSelector', function() {
                $('#monthForm').submit(); 
            });

            
            //shorten all URLs
            $("#shortIt1").click(function () {
                regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
                $(".editing").each(function() {
                    var longUrlLink = $(this).val().match(regex);
                    textbox = $(this).closest('tr').find('.editing');
                    $(this).closest('tr').find('input[name=tosubmit]').val(true);
                    var $this = $(this);
                    $.ajax({
                        url: 'https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyC27e05Qg5Tyghi1dk5U7-nNDC0_wift08',
                        type: 'POST',
                        contentType: 'application/json; charset=utf-8',
                        data: '{ longUrl: "' + longUrlLink + '"}',
                        dataType: 'json',
                        success: function(response) {
                             $this.val($this.val().replace(longUrlLink, response.id));
                             toastr.success("URLs Shortened");
                        }
                     });

                });
            });

            $(".shortsingle").click(function () {
            regex = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/g ;
            textbox = $(this).closest('tr').find('.editing');
            $(this).closest('tr').find('input[name=tosubmit]').val(true);
            var longUrlLink = textbox.val().match(regex);
            $.ajax({
                url: 'https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyC27e05Qg5Tyghi1dk5U7-nNDC0_wift08',
                type: 'POST',
                contentType: 'application/json; charset=utf-8',
                data: '{ longUrl: "' + longUrlLink + '"}',
                dataType: 'json',
                success: function(response) {
                     textbox.val(textbox.val().replace(longUrlLink, response.id));
                     toastr.success("URL successfully shortened: <br/>" + longUrlLink + " -> " + response.id);
                },
                error: function(response) {
                    taostr.warning("Error while trying to shorten URL. Please try again");
                }
             });
        });

            var channel1 = pusher.subscribe('private-body_channel_<?echo $this->Session->read("access_token.account_id");?>');
            warnMessage = null;
            window.onbeforeunload = function () {
                if (warnMessage != null) return warnMessage;
            }

            $(".editing").on('change', function () {
                warnMessage = "You have unsaved changes on this page, if you leave your changes will be lost."
                $(this).closest("tr").find('input[name=tosubmit]').val(true);
                text = $(this).val();
                id = $(this).closest("tr").find('#TweetId').attr('data-id');
                /*channel1.bind('body_update',
                    function(data) {
                        alert('data');
                    }
                );*/
                var triggered = channel1.trigger('client-body_update', { 'tweet_id' : id, 'body': text });

            });

            $('.editing').keyup(userTyping);

            var typingTimeout = null;
            function userTyping() {
                first_name =  '<?echo $this->Session->read("Auth.User.first_name");?>';
                last_name = '<?echo $this->Session->read("Auth.User.last_name");?>';
                tweet_id = $(this).closest("tr").find('#TweetId').attr('data-id');
                if (!typingTimeout) {
                    channel1.trigger('client-body_typing', {'typing' : true, 'tweet_id' : tweet_id, 'first_name' : first_name, 'last_name' : last_name});
                } else {
                    window.clearTimeout(typingTimeout);
                    typingTimeout = null;
                }

                typingTimeout = window.setTimeout(function () {
                    channel1.trigger('client-body_typing', {'typing' : false, 'tweet_id' : tweet_id, 'first_name' : first_name, 'last_name' : last_name});
                    typingTimeout = null;
                }, 3000);

            }

            channel1.bind('client-body_update',
                function(data) {
                    $('#TweetId[data-id="' + data['tweet_id'] + '"]').closest('tr').find('.editing').text(data['body']);
                }
            );

            channel1.bind('client-body_typing',
                function(data) {
                    if (data['typing'] == true) {  
                        string = data['first_name'] + ' ' + data['last_name'] + ' is typing...'; 
                        $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('tr').find('.isTyping').text(string).slideDown();
                        $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('tr').find('.editing').attr('disabled', 'disabled');
                    } else {
                        $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('tr').find('.isTyping').slideUp();
                        $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('tr').find('.editing').attr('disabled', false);
                    }
                }
            );

            /*channel1.bind('client-body_file',
                function(data) {
                    $('#TweetId[data-id="' + data['tweet_id'] + '"]').closest('tr').find('.input.file').after("<div class='imagecontainer'>
                        <img src='" + data['img_url'] +"' style='max-width:500px'></div>");
                }
            );*/

            $('input:submit, button:submit').on('click', function() {
                warnMessage = null;
            });

            $('.input.file input').on('change', function() {
                $(this).parent().css('background', "url(/img/upload_image_green.png) left center no-repeat");
                $(this).closest("tr").find('input[name=tosubmit]').val(true);
                $(this).closest("tr").find('.editing').addClass('withImage').removeClass('withoutImage');
                $(this).closest("tr").find('.counter1').hide();
                val = $(this).closest("tr").find('.TwitterVerified1:checked').val();
                if (val == 0) {
                    color = '#ffcc00';
                } else if (val == 1) {
                    color = '#21a750';
                } else if (val == 2) {
                    color = '#ff0000';
                }
                $(this).closest("tr").find('#TweetBody').css("border", "1px solid" + color);
                $(this).closest("tr").find('#TweetBody').css("border-bottom", "none");
                $(this).closest("tr").find('.counter2').css("border", "1px solid" + color);
                $(this).closest("tr").find('.counter2').css("border-top", "none");

                //$('.editing.withImage').charCount({css: 'counter counter1', allowed: 117});
                $(this).closest("tr").find('.editing').charCount({css: 'counter counter2', allowed: 117});

                var files = !!this.files ? this.files : [];
                if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
         
                if (/^image/.test( files[0].type)){ // only image file
                    var reader = new FileReader(); // instance of the FileReader
                    reader.readAsDataURL(files[0]); // read the local file
                    var id = $(this).closest("tr").find('#TweetId').attr('data-id');
                    reader.onloadend = function(){ // set image data as background of div
                        $("#imagePreview" + id + " img").attr('src', this.result);
                    }
                }
            });

            $('select').selectric();

            /*$("#table").on("change", ".TwitterVerified1", function() {
                $(this).closest("tr").find('input[name=tosubmit]').val(true);
                $("#table").css('opacity', '.4');
                $('#loading').show();
                var dat = new FormData();
                $('input[name=tosubmit][value=true]').each(function () {
                    //dat = dat + '&' + $.param($(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked'));
                    $(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
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
                        $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                            $("#table").css('opacity', '1');
                            $('#loading').hide();
                        });
                    }
                });
            });*/

            /*$(".smallSaveButton").click(function () {
                $("#table").css('opacity', '.4');
                $('#loading').show();
                        $('#submitTweets').ajaxSubmit({success: function() {
                            refresh();
                        }});
                //setTimeout(refresh, 500);//delaying the table refresh so that the form can successfully submit into the databases
                function refresh() {
                            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                                $("#table").css('opacity', '1');
                                $('#loading').hide();
                            });
                    
                };

                $('#progress table').load('/twitter/progressrefresh');
            });*/

            $(".smallSaveButton").click(function () {
                $("#table").css('opacity', '.4');
                $('#loading').show();
                var dat = new FormData();
                $('input[name=tosubmit][value=true]').each(function () {
                    //dat = dat + '&' + $.param($(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked'));
                    $(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
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
                        $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                            warnMessage = null;
                            $("#table").css('opacity', '1');
                            $('#loading').hide();
                            toastr.success('Saved successfully');
                            pusher.disconnect();
                        });
                    }
                });
                $('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
            });

            $('.comments.present').qtip({ 
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

            /*$(".smallSaveButton").click(function () {
                $("#table").css('opacity', '.4');
                $('#loading').show();
                var dat = "";
                $('input[name=tosubmit][value=true]').each(function () {
                    dat = dat + '&' + $.param($(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked'));
                });
                alert(dat);
                $.ajax({
                    type: "POST",
                    url: "/editorial_calendars/editcalendartweet1",
                    data: dat,
                    success: function(data) {
                        alert('success');
                        $("#table").css('opacity', '1');
                        $('#loading').hide();
                    }
                });
            });*/

            $(".approveAll").click(function () {
            /*$(".verified").each(function () {
                $(this).find(".input.radio input:radio[value=1]").prop('checked', true);
                //$("#table").css('opacity', '.4');
                    id = $(this).find(".input.radio input:radio[value=1]").attr('id');
                    id = id.slice(0, -1);
                    $("#" + id + "_" + "<? echo $this->Session->read('Auth.User.first_name'); ?>").prop('disabled', false);

                    
            });*/
                r = confirm('Clicking this button will delete any unsaved changes that you have made. Please save your changes before you continue.');
                if (r == true) {
                    $("#table").css('opacity', '.4');
                    $('#loading').show();
                    $.ajax({
                        type: "POST",
                        url: "/twitter/approveall",
                        data: {'account_id': <?echo $this->Session->read('access_token.account_id');?>, 'month': <?echo $this->Session->read('Auth.User.monthSelector');?>},
                        success: function(data) {
                            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                                    $("#table").css('opacity', '1');
                                    $('#loading').hide();
                                    toastr.success('Saved successfully');
                                    pusher.disconnect();
                                });
                            }
                    });
                }
            });

        var channel = pusher.subscribe('private-comment_channel_<?echo $this->Session->read("access_token.account_id");?>');
        channel.bind('new_comment',
            function(data) {
                str = $("#notificationFrontImage").attr('src');
                str1 = str.substr(17);
                if (str1 != "9plus.png") {
                    str1 =  Number(str1.split('.')[0]) + 1;
                }
                $("#notificationFrontImage").attr('src', '/img/notification' + str1 + '.png');
            }
        );

        $('.calendar_topic').qtip({
            content: {
                    text: function(event, api) {
                        id = $(this).closest('tr').find('#TweetCalendarId').attr('value');
                        //return $('#' + id + '-comments').clone();
                        $.ajax({
                            url: '/editorial_calendars/recycle/' + id
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
                    event: 'unfocus'
                },
                position: {
                    my: 'bottom center',
                    at: 'top center', 
                    target: 'event'
                },
                show: 'click'
        });

        $(document).scroll(function() {
          var y = $(this).scrollTop();
          if (y > 350) {
            $('.fixedTwitterAccount').slideDown();
            $('.fixedScroller').show("slide", { direction: "right" }, 500);
          } else {
            $('.fixedTwitterAccount').slideUp();
            $('.fixedScroller').hide("slide", { direction: "right" }, 500);
          }
        });

        $(".fixedScroller td").click(function() {
            if ($(this).attr('class') == 'gototop') {
                $('html, body').animate({
                    scrollTop: 0
                }, 2000);
            } else {
                var text = $(this).text();
                $('html, body').animate({
                    scrollTop: $("." + text).offset().top - 30
                }, 2000);
            }
        });
        });

</script>