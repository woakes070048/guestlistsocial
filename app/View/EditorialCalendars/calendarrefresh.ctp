<div class="shortenAllUrls" id="shortIt1"><i class="fa fa-code fa-fw"></i>Shorten All URLs</div>
<?if (!empty($isTeamAdmin)) {?>
    <div class="approveAll"><i class="fa fa-check fa-fw"></i>Approve All</div>
    <div class="autoPopulate"><i class="fa fa-refresh fa-fw"></i>Auto-Populate</div>
<?}?>
<div class='slick'>
    <?
    $base = strtotime(date('Y-m',time()) . '-01 00:00:01'); 
    $monthsarray = array(
        -5 => date('F Y', strtotime('-5 month', $base)),
        -4 => date('F Y', strtotime('-4 month', $base)),
        -3 => date('F Y', strtotime('-3 month', $base)),
        -2 => date('F Y', strtotime('-2 month', $base)),
        -1 => date('F Y', strtotime('-1 month', $base)),
        0 => date('F Y', strtotime('+0 month', $base)),
        1 => date('F Y', strtotime('+1 month', $base)),
        2 => date('F Y', strtotime('+2 month', $base)),
        3 => date('F Y', strtotime('+3 month', $base)),
        4 => date('F Y', strtotime('+4 month', $base)),
        5 => date('F Y', strtotime('+5 month', $base))
        );
    foreach ($monthsarray as $key => $value) {?>
        <div data-month=<?echo $key;?>>
            <?echo $value;?>
        </div>
    <?}?>
</div>
<?
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
$year = date('Y', strtotime('+' . $months . ' month', $base));
//$year = date('Y');

$count = $daysinmonth - $day;
for ($i=$day; $i<=$daysinmonth; $i++) {
    $days[date('d-m-Y',mktime(0,0,0,$month,$i,$year))] = date('l',mktime(0,0,0,$month,$i,$year));
}
?>
<!--<div id='calendarbuttons'>
<? echo $this->Form->button('Shorten all URLs', array('id' => 'shortIt1', 'class' => 'urlSubmit1', 'type' => 'button'));
echo $this->Form->button('Approve All', array('class' => 'urlSubmit1 approveAll', 'type' => 'button'));
?>
</div>-->
<?php if (!empty($calendar)) { ?>
<?
echo $this->Form->create('Tweet', array('url' => '/editorial_calendars/editcalendartweet1', 'id' => 'submitTweets', 'type' => 'file'));
?>
<?
$testid = 1;
$allApproved = array();
foreach ($days as $key => $value) {
$allApproved[date('jS', strtotime($key))] = 0; ?>
<div class='bigdate'><?echo date('jS ', strtotime($key)) . date('F Y', strtotime('+' . $months .' month', $base));?></div>
<?php
foreach ($calendar as $time => $key1) {
    $testid = $testid + 1;?>
    <div class='tweet'>
<?
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
    if (empty($key1['Tweet'])) {
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
        $tweet_bank_id = 0;
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
                $tweet_bank_id = $key2['tweet_bank_id'];
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
                $tweet_bank_id = 0;
            }
        }   
        if (!empty($empty)) {
            $allApproved[date('jS', strtotime($key))] -= 1000;
        }
    }
        if (!empty($team)) {
            if ($session_teams[$team]['TeamsUser']['group_id'] == 2) {
                $disabled = 'disabled';
            } else {
                $disabled = '';
            }
        }
        ?>
    <div class='tweetTop'>
        <div class="calendar scheduled <?echo date('jS', strtotime($key . $key1['EditorialCalendar']['time']));?>">
                <i class='fa fa-clock-o'></i>
                <?echo date('H:i ', strtotime($key . $key1['EditorialCalendar']['time'])) . '<b class="' .date('l', strtotime($key . $key1['EditorialCalendar']['time'])) . '">' . date('l ', strtotime($key . $key1['EditorialCalendar']['time'])) . '</b>' . date('jS F Y', strtotime($key . $key1['EditorialCalendar']['time']));?>
                <? if($published == 1) {
                    echo '<small>[Published]</small>';
                }?>
        </div>
        <div class='categoryContainer'>
            <div class='calendar_topic' data-category-id="<?echo $key1['BankCategory']['id'];?>"><? if (!empty($key1['BankCategory']['category'])) {echo $key1['BankCategory']['category'];} ?></div>
            <!--<div class='calendar_notes'><? //if (!empty($key1['BankCategory']['category'])) {echo $key1['BankCategory']['category'];} ?> Some comment</div>-->
        </div>
    </div>
    <div class="topic">
    

    <!--<div class='calendar_content_type'><? echo $key1['EditorialCalendar'][strtolower($value) . '_content_type']; ?></div>

    <div class='calendar_notes'><? echo $key1['EditorialCalendar'][strtolower($value) . '_notes']; ?></div>--></div>
    <div class="textBoxAndButtons">
        <?echo $body;?>
        <div class='isTyping' style="display: inline-block"></div>
        <div class="tweetButtons">
        <?if ($commentCount > 9) {
            $commentCount = '9plus';
        }?>
            <i class="empty comments <?echo $present;?> fa fa-comments" id="<? echo $id; ?>"></i>
            <i class="smallSaveButton fa fa-floppy-o"></i>
            <i class="urlSubmit1 shortsingle fa fa-code"></i>
            <? //echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$value1.'][img_url1]', 'label' => false, 'class' => 'imgupload')); ?>
            <i class="fa fa-camera"></i>
        </div>
    </div>
    <div class="imageUpload" style="display:none">
        <? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$value1.'][img_url1]', 'label' => "<span class='button'>Upload Image</span>", 'class' => 'button', 'id' => 'TweetImgUrl1' . $idForPusher)); ?>
        <span>OR</span>
        <? echo $this->Form->input('img_url2', array('name' => 'data[Tweet]['.$value1.'][img_url2]', 'label' => false, 'placeholder' => 'Paste Link...', 'class' => 'TweetImgUrl2'));?>
    </div>
    <div class="calendar verified">
    <? echo $this->Form->input('verified', 
            array(
                'type' => 'radio',
                'options' => array(
                    1 => 'Approved',
                    0 => 'Pending',
                    2 => 'Improve'
                ),
                'legend' => false,
                'name' => 'data[Tweet]['.$value1.'][verified]',
                'class' => 'calendar TwitterVerified1',
                'id' => $id,
                'default' => $verified,
                $disabled,
                'before' => '<div class="verifiedLabel">',
                'separator' => '</div><div class="verifiedLabel">',
                'after' => '</div>'
            )
    );?>
        
        <?if (!empty($editors)) {?>
        <ul style='list-style: none; font-size: 9px; margin: 5px 0 5px 5px;'><?
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
            <li style='margin: 0;'><b style='width: 110px; overflow: hidden; text-overflow: ellipsis;'><? echo $x . ': ' ?></b><? echo $keyx['User']['first_name'];?></li>
            <?}?>
        </ul>
        <?}?>
    </div>
    <? if (!empty($img)) {?>
        <div class='imagecontainer'>
            <? echo $this->Html->link("<i class='deleteimage fa fa-times'></i>", array('controller' => 'twitter', 'action' => 'deleteImage', $id), array('escape' => false));?>
            <? echo $this->Html->image($img, array('style' => 'max-width:496px')); ?>
        </div>
    <?}?>
        <div id="imagePreview<?echo$idForPusher;?>" class='imagecontainer' style="display: none">
            <img src='' style='max-width:496px'>
        </div>
    <?
    echo $this->Form->input('timestamp', array('type' => 'hidden', 'value' => date('d-m-Y H:i', strtotime($key . $key1['EditorialCalendar']['time'])), 'name' => 'data[Tweet]['.$value1.'][timestamp]'));
    echo $this->Form->input('id', array('type' => 'hidden', 'value' => $id, 'name' => 'data[Tweet]['.$value1.'][id]', 'data-id' => $idForPusher));
    echo $this->Form->input('calendar_id', array('type' => 'hidden', 'value' => $key1['EditorialCalendar']['id'], 'name' => 'data[Tweet]['.$value1.'][calendar_id]'));
    echo $this->Form->input('img_url', array('type' => 'hidden', 'value' => false, 'name' => 'data[Tweet]['.$value1.'][img_url]'));
    echo $this->Form->input('forceVerified', array('type' => 'hidden', 'value' => false, 'name' => 'data[Tweet]['.$value1.'][forceVerified]'));
    echo $this->Form->input('tosubmit', array('type' => 'hidden', 'value' => false, 'name' => 'tosubmit'));
    echo $this->Form->input('tweet_bank_id', array('type' => 'hidden', 'value' => $tweet_bank_id, 'name' => 'data[Tweet]['.$value1.'][tweet_bank_id]'));
    ?>
</div><?
}
}
?>
<? echo $this->Form->end(array('id' => 'tweetsubmit1', 'label' => 'SAVE', 'value' => 'Save', 'class' => 'longbutton')); ?>

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
<?} else {?>
<div id='noaccount' style="display: none;" class="calendarrefresh">
    <?echo $this->Html->image('/img/logogrey.png', array('width' => '35px'));?>
    You have not created an editorial calendar. Please click on the button to the left to create one or switch calendar view off.
</div>
<?}?>



<script> 
        // wait for the DOM to be loaded 
        $(document).ready(function () {
            $('.editing.withoutImage').charCount({css: 'counter counter1', allowed: 140});
            $('.editing.withImage').charCount({css: 'counter counter2', allowed: 117});

            var pusher = new Pusher('67904c5b4e0608620f41', { authEndpoint: '/pusher/pusher/auth.json' });

            $(".TwitterVerified1:checked").each( function() {
                if ($(this).val() == 0) {
                    color = '#f0ad4e';
                } else if ($(this).val() == 1) {
                    color = '#5cb85c';
                } else if ($(this).val() == 2) {
                    color = '#d9534f';
                }
                $(this).closest(".tweet").find('#TweetBody').css("border", "1px solid" + color);
            });

            $(".TwitterVerified1[value='1'] + label").click(function() {
                $(this).closest(".tweet").find('.TwitterVerified1[value="1"]').prop('checked', true);
                $(this).closest(".tweet").find('input[name=tosubmit]').val(true);
                $(this).closest(".tweet").find('#TweetForceVerified').val(true);
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
                            var month = $('.slick-current').attr('data-month');
                            $('#table').load('/editorial_calendars/calendarrefresh/' + month, function() {
                                warnMessage = null;
                                $("#table").css('opacity', '1');
                                $('#loading').hide();
                                toastr.success('Saved successfully');
                                pusher.disconnect();
                            });
                        },
                        error: function(data) {
                            $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function() {
                                warnMessage = null;
                                $("#table").css('opacity', '1');
                                $('#loading').hide();
                                toastr.error('Not saved successfully. Please try again');
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
                    textbox = $(this).closest('.tweet').find('.editing');
                    $(this).closest('.tweet').find('input[name=tosubmit]').val(true);
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
            textbox = $(this).closest('.tweet').find('.editing');
            $(this).closest('.tweet').find('input[name=tosubmit]').val(true);
            var longUrlLink = textbox.val().match(regex);
            $.ajax({
                url: 'https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyC27e05Qg5Tyghi1dk5U7-nNDC0_wift08',
                type: 'POST',
                contentType: 'application/json; charset=utf-8',
                data: '{ longUrl: "' + longUrlLink + '"}',
                dataType: 'json',
                success: function(response) {
                     textbox.val(textbox.val().replace(longUrlLink, response.id));
                     classs = $(textbox).closest(".tweet").find('.counter').first().attr('class');
                     $(textbox).closest(".tweet").find('.counter').first().hide();
                     if (classs == "counter counter2") {
                        $(textbox).closest(".tweet").find('.editing').charCount({css: 'counter counter2', allowed: 117});
                     } else if (classs == "counter counter1") {
                        $(textbox).closest(".tweet").find('.editing').charCount({css: 'counter counter1', allowed: 140});
                     }
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
                $(this).closest(".tweet").find('input[name=tosubmit]').val(true);
                text = $(this).val();
                id = $(this).closest(".tweet").find('#TweetId').attr('data-id');
                user_id = "<?echo $this->Session->read('Auth.User.id');?>";
                $(this).closest(".tweet").find('#TweetTweetBankId').val(0);
                /*channel1.bind('body_update',
                    function(data) {
                        alert('data');
                    }
                );*/
                var triggered = channel1.trigger('client-body_update', { 'tweet_id' : id, 'body': text, 'user_id' : user_id});

            });

            $('.editing').keyup(userTyping);

            var typingTimeout = null;
            function userTyping() {
                first_name =  '<?echo $this->Session->read("Auth.User.first_name");?>';
                last_name = '<?echo $this->Session->read("Auth.User.last_name");?>';
                user_id = "<?echo $this->Session->read('Auth.User.id');?>";
                tweet_id = $(this).closest(".tweet").find('#TweetId').attr('data-id');
                if (!typingTimeout) {
                    channel1.trigger('client-body_typing', {'typing' : true, 'tweet_id' : tweet_id, 'first_name' : first_name, 'last_name' : last_name, 'user_id' : user_id});
                } else {
                    window.clearTimeout(typingTimeout);
                    typingTimeout = null;
                }

                typingTimeout = window.setTimeout(function () {
                    channel1.trigger('client-body_typing', {'typing' : false, 'tweet_id' : tweet_id, 'first_name' : first_name, 'last_name' : last_name, 'user_id' : user_id});
                    typingTimeout = null;
                }, 3000);

            }

            channel1.bind('client-body_update',
                function(data) {
                    user_id = "<?echo $this->Session->read('Auth.User.id');?>";
                    if (user_id != data['user_id']) {
                        $('#TweetId[data-id="' + data['tweet_id'] + '"]').closest('.tweet').find('.editing').text(data['body']);
                    }
                }
            );

            channel1.bind('client-body_typing',
                function(data) {
                    user_id = "<?echo $this->Session->read('Auth.User.id');?>";
                    if (user_id != data['user_id']) {
                        if (data['typing'] == true) {  
                            string = data['first_name'] + ' ' + data['last_name'] + ' is typing...'; 
                            $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('.tweet').find('.isTyping').text(string).slideDown();
                            $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('.tweet').find('.editing').attr('disabled', 'disabled');
                        } else {
                            $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('.tweet').find('.isTyping').slideUp();
                            $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('.tweet').find('.editing').attr('disabled', false);
                        }
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
                $(this).parent().css('color', "#0788D3");
                $(this).closest(".tweet").find('input[name=tosubmit]').val(true);
                $(this).closest(".tweet").find('.editing').addClass('withImage').removeClass('withoutImage');
                $(this).closest(".tweet").find('.counter1').hide();
                $(this).closest(".tweet").find('.counter2').hide();
                val = $(this).closest(".tweet").find('.TwitterVerified1:checked').val();
                if (val == 0) {
                    color = '#ffcc00';
                } else if (val == 1) {
                    color = '#21a750';
                } else if (val == 2) {
                    color = '#ff0000';
                }
                $(this).closest(".tweet").find('#TweetBody').css("border", "1px solid" + color);
                $(this).closest(".tweet").find('#TweetTweetBankId').val(0);

                //$('.editing.withImage').charCount({css: 'counter counter1', allowed: 117});
                $(this).closest(".tweet").find('.editing').charCount({css: 'counter counter2', allowed: 117});

                var files = !!this.files ? this.files : [];
                if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
         
                if (/^image/.test( files[0].type)){ // only image file
                    var reader = new FileReader(); // instance of the FileReader
                    reader.readAsDataURL(files[0]); // read the local file
                    var id = $(this).closest(".tweet").find('#TweetId').attr('data-id');
                    reader.onloadend = function(){ // set image data as background of div
                        $("#imagePreview" + id + " img").closest('.tweet').find('.imagecontainer').hide();
                        $("#imagePreview" + id + " img").attr('src', this.result);
                        $("#imagePreview" + id).show();
                    }
                }
            });

            $('.TweetImgUrl2').on('change', function() {
                $(this).closest(".tweet").find('input[name=tosubmit]').val(true);
                $(this).closest(".tweet").find('#TweetTweetBankId').val(0);
            });

            /*$('select').selectric();*/

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
                        id = $(this).closest('.tweet').find('#TweetCalendarId').attr('value');
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
                    my: 'left top',
                    at: 'right top', 
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
                    scrollTop: $("." + text).offset().top - 60
                }, 2000);
            }
        });

        $('.slick').slick({
            prevArrow: "<div class='slick-arrowleft'></div>",
            nextArrow: "<div class='slick-arrowright'></div>",
            initialSlide: <? echo $this->Session->read('Auth.User.monthSelector');?> + 5
        });

        $('.slick-arrowright, .slick-arrowleft').click(function () {
            $('#table').css('opacity', '.4');
            $('#loading').show();
            var month = $(this).closest('.slick').find('.slick-current').attr('data-month');
            $('#table').load('/editorial_calendars/calendarrefresh/' + month, function () {
                $('#table').css('opacity', '1');
                $('#loading').hide();
            });
        });

        <? if (empty($calendar)) {?>
            $('#noaccount.calendarrefresh').show();
        <?}?>

        $('.fa.fa-camera').click(function () {
            $(this).closest('.tweet').find('.imageUpload').show();
        });

        $('.autoPopulate').click(function () {
            $.ajax({
                url: "/tweet_bank/autoPopulate/" + <?echo $this->Session->read('access_token.account_id');?>,
                processData: false,
                contentType: false,
                success: function(data) {
                    data1 = $.extend(true, {}, data);
                    console.log(JSON.stringify(data));
                    $('.editing').each(function() {
                        if ($(this).val().length == 0) {//if empty tweet
                            bank_category_id = $(this).closest('.tweet').find('.calendar_topic').attr('data-category-id');
                            if (bank_category_id) {//if bank category exists for that calendar
                                console.log(JSON.stringify(data[bank_category_id]));
                                if (data[bank_category_id].length) {//if there are any banked tweets for that calendar
                                    noTweets = false;
                                } else {
                                    if (data1[bank_category_id].length) {
                                        //console.log(JSON.stringify(data[bank_category_id]));
                                        data = $.extend(true, {}, data1);
                                        noTweets = false;
                                    } else {
                                        noTweets = true;
                                    }
                                }

                                if (noTweets == false) {

                                    random = Math.floor(Math.random()*data[bank_category_id].length);
                                    $(this).text(data[bank_category_id][random]["body"]);//set random tweet

                                    if (data[bank_category_id][random]["img_url"]) {//if image exists
                                        image_url = data[bank_category_id][random]["img_url"];
                                        $(this).closest('.tweet').find('#TweetImgUrl').val(image_url);
                                        $(this).closest('.tweet').find('.calendar.verified').after(function () {//hide old image and display new one
                                            $(this).closest('.tweet').find('.imagecontainer').hide();
                                            return '<div class="imagecontainer"><img src="' + image_url + '" style="max-width:496px;"></div>';
                                        });
                                    } else {//if no image exists
                                        $(this).closest('.tweet').find('.imagecontainer').hide();
                                    }
                                    $(this).closest('.tweet').find('input[name=tosubmit]').val(true);
                                    $(this).closest('.tweet').find('#TweetTweetBankId').val(data[bank_category_id][random]["id"]);
                                    data[bank_category_id].splice(random, 1);
                                }
                            }
                        }
                    });
                }
            });
        });

        });

</script>