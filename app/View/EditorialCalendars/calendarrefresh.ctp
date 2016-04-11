<div class="shortenAllUrls" id="shortIt1"><i class="fa fa-code fa-fw"></i>Shorten All URLs</div>
<?
if ($isTeamAdmin) {
?>
    <div class="approveAll"><i class="fa fa-check fa-fw"></i>Approve All</div>
    <div class="autoPopulate"><i class="fa fa-refresh fa-fw"></i>Auto-Populate</div>
<?
}
?>

<div class='slick'>
    <?
    foreach ($monthsarray as $key => $value) {
    ?>
        <div data-month=<?echo $key;?>>
            <?echo $value;?>
        </div>
    <?
    }
    ?>
</div>
<div class="loadingTweets">
    <div class="loader1">
        <svg class="circular1">
            <circle class="path" cx="5" cy="5" r="4" fill="none" stroke-width="1" stroke-miterlimit="2"/>
        </svg>
    </div>
    Loading your tweets, please wait.
</div>
<?php 
if (!empty($calendar)) { 
?>
    <?
    echo $this->Form->create('Tweet', array('url' => '/editorial_calendars/editcalendartweet1', 'id' => 'submitTweets', 'type' => 'file'));
    ?>
    <?

    $allApproved = array();
    foreach ($days as $key => $value) {
        //$allApproved[date('jS', strtotime($key))] = 0; ?>
            <div class='bigdate'>
                <?echo date('jS F Y', strtotime($key));?>
            </div>
        <?php
        foreach ($calendar as $time => $key1) {
            $timestamp = strtotime("$key $time");
            $class = $key1[strtolower($value)]['EditorialCalendar']['id'] . '-' . $timestamp;?>
            <div class="tweetWrapper <? echo $class;?>">
            </div>
        <?
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
                /*if ($allApproved[date('jS', strtotime($key))] == count($calendar)) {
                    $class = 'allApproved';
                } elseif ($allApproved[date('jS', strtotime($key))] > -1000 && is_float($allApproved[date('jS', strtotime($key))])) {
                    $class = 'notAllApproved';
                } elseif ($allApproved[date('jS', strtotime($key))] < -900000) {
                    $class = 'improveApproved';
                } elseif ($allApproved[date('jS', strtotime($key))] < 0) {
                    $class = '';
                } else {
                    $class = '';
                }*/?>
                <tr>
                    <td class='scroll<?echo date('jS', strtotime($key));?>'><?echo date('jS', strtotime($key));?></td>
                </tr>
            <?}?>
        </table>
    </div>
<?
} else {
?>
    <!--<div id='noaccount' style="display: none;" class="calendarrefresh">
        <?echo $this->Html->image('/img/logogrey.png', array('width' => '35px'));?>
        You have not created an editorial calendar. Please click on the button to the left to create one or switch calendar view off.
    </div>-->
<?
}
?>



<script> 
        // wait for the DOM to be loaded 
        $(document).ready(function () {
            verified = {};
            calendarCount = {};
            <?
            $j = 0;
            foreach ($days as $key => $value) {
                $jSdate = date('jS', strtotime($key));?>
                verified["<?echo date('jS', strtotime($key));?>"] = {0 : 0, 1 : 0, 2 : 0};
                calendarCount["<?echo $jSdate;?>"] = 0;
                <?
                $i = 0;
                $j++;
                foreach ($calendar as $time => $key1) {
                    $i++;
                    $timestamp = strtotime("$key $time");
                    ?>
                    $(".<?echo $key1[strtolower($value)]['EditorialCalendar']['id'];?>-<?echo $timestamp;?>").load("/editorial_calendars/tweet/<?echo $key1[strtolower($value)]['EditorialCalendar']['id'];?>/<?echo $timestamp;?>", function () {
                            $(".tweetWrapper.<?echo $key1[strtolower($value)]['EditorialCalendar']['id'];?>-<?echo $timestamp;?>").css('opacity', '1');

                            v = $(".tweetWrapper.<?echo $key1[strtolower($value)]['EditorialCalendar']['id'];?>-<?echo $timestamp;?>").find('.TwitterVerified1:checked').val();

                            body = $(".tweetWrapper.<?echo $key1[strtolower($value)]['EditorialCalendar']['id'];?>-<?echo $timestamp;?>").find('#TweetBody').val();

                            if (v == 1) {
                                verified["<?echo $jSdate;?>"][1] = verified["<?echo $jSdate;?>"][1] + 1;
                            } else if (v == 0 && body) {
                                verified["<?echo $jSdate;?>"][0] = verified["<?echo $jSdate;?>"][0] + 1;
                            } else if (v == 2) {
                                verified["<?echo $jSdate;?>"][2] = verified["<?echo $jSdate;?>"][2] + 1;
                            }
                            calendarCount["<?echo $jSdate;?>"] = calendarCount["<?echo $jSdate;?>"] + 1;

                            <? if ($i == count($calendar)) {?>
                                initiateScroller(verified, calendarCount, "<?echo $jSdate;?>");

                                <?if ($j == count($days)) {?>
                                    $(".loadingTweets").fadeOut();
                                    verified = null;
                                    calendarCount = null;
                                <?}?>
                            <?}?>
                            v = null;
                            body = null;
                    });
                    <?
                }
            }
            ?>

            function initiateScroller(verified, calendarCount, jSdate, body) {
                if (verified[jSdate][1] == calendarCount[jSdate] && calendarCount[jSdate] != 0) {
                    $(".scroll" + jSdate).addClass('allApproved');
                } else if (verified[jSdate][2] >= 1 &&  calendarCount[jSdate] != 0) {
                    $(".scroll" + jSdate).addClass('improveApproved');
                } else if ((verified[jSdate][0] + verified[jSdate][1]) == calendarCount[jSdate]) {
                    $(".scroll" + jSdate).addClass('notAllApproved');
                } else {
                    $(".scroll" + jSdate).css('background', "#fff");
                }
            }

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

            //save on radio click
            $(".TwitterVerified1[value='1'] + label").click(function() {
                $(this).closest(".tweet").find('.TwitterVerified1[value="1"]').prop('checked', true);
                $(this).closest(".tweet").find('input[name=tosubmit]').val(true);
                $(this).closest(".tweet").find('#TweetForceVerified').val(true);
                $(this).closest(".tweet").css('opacity', '.4');
                //$('#loading').show();
                var dat = new FormData();
                $(this).closest(".tweet").find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
                    if ($(this).attr('type') == 'file') {
                        dat.append($(this).attr('name'), this.files[0]);
                    } else {
                        dat.append($(this).attr('name'), $(this).val());
                    }
                });
                
                $.ajax({
                    type: "POST",
                    url: "/editorial_calendars/editcalendartweet",
                    data: dat,
                    context: this,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        calendar_id = $(this).closest(".tweet").find('#TweetCalendarId').val();
                        timestamp = $(this).closest(".tweetWrapper").attr('class').split(' ').pop().split('-')[1];
                        $(this).closest(".tweetWrapper").load('/editorial_calendars/tweet/' + calendar_id + '/' + timestamp, function () {
                            warnMessage = null;
                            $(this).closest('.tweet').css('opacity', 1);
                            toastr.success('Saved successfully');
                            //pusher.disconnect();
                            //$('#loading').hide();
                        });
                    },
                    error: function(data) {
                        toastr.error('There was a problem saving your tweet. Please try again.');
                        warnMessage = null;
                        $(this).closest('.tweet').css('opacity', 1);
                    }
                });
            });
    
            //won't need hopefully
            $("#table").on("change", ".TwitterVerified1", function() {
                pusher.disconnect();
            });


            //submit all
            /*$('#table1').on('click', '#tweetsubmit', function() {
                $('#submitTweets').submit();
            });*/

            //switch months
            /*$('#table1').on('change', '#monthSelector', function() {
                $('#monthForm').submit(); 
            });*/
        
            $(".tweetWrapper").on("click", ".shortsingle", function () {
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
            
            //shorten all URLs
            /*$("#shortIt1").click(function () {
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
            });*/

            $('.tweetWrapper').on('change', '.input.file input', function() {
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

            

            

            /*$('select').selectric();*/

            $(".tweetWrapper").on("change", ".TwitterVerified1", function() {
                $(this).closest(".tweet").css('opacity', '.4');
                //$('#loading').show();
                var dat = new FormData();
                //$('input[name=tosubmit][value=true]').each(function () {
                    //dat = dat + '&' + $.param($(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked'));
                    $(this).closest(".tweet").find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
                        if ($(this).attr('type') == 'file') {
                            dat.append($(this).attr('name'), this.files[0]);
                        } else {
                            dat.append($(this).attr('name'), $(this).val());
                        }
                    });
                //});
                
                $.ajax({
                    type: "POST",
                    url: "/editorial_calendars/editcalendartweet",
                    data: dat,
                    context: this,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        calendar_id = $(this).closest(".tweet").find('#TweetCalendarId').val();
                        timestamp = $(this).closest(".tweetWrapper").attr('class').split(' ').pop().split('-')[1];
                        $(this).closest(".tweetWrapper").load('/editorial_calendars/tweet/' + calendar_id + '/' + timestamp, function () {
                            warnMessage = null;
                            $(this).closest('.tweet').css('opacity', 1);
                            toastr.success('Saved successfully');
                            //pusher.disconnect();
                            //$('#loading').hide();
                        });
                    },
                    error: function(data) {
                        toastr.error('There was a problem saving your tweet. Please try again.');
                        warnMessage = null;
                        $(this).closest('.tweet').css('opacity', 1);
                    }
                });
            });

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
    
            //single save button
            $(".tweetWrapper").on("click", ".tweet .tweetButtons .smallSaveButton", function () {
                $(this).closest(".tweet").css('opacity', '.4');
                //$('#loading').show();
                var dat = new FormData();
                //$('input[name=tosubmit][value=true]').each(function () {
                    //dat = dat + '&' + $.param($(this).closest("tr").find('input:not([type=radio]), textarea, input[type=radio]:checked'));
                    $(this).closest(".tweet").find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
                        if ($(this).attr('type') == 'file') {
                            dat.append($(this).attr('name'), this.files[0]);
                        } else {
                            dat.append($(this).attr('name'), $(this).val());
                        }
                    });
                //});
                
                $.ajax({
                    type: "POST",
                    url: "/editorial_calendars/editcalendartweet",
                    data: dat,
                    context: this,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        calendar_id = $(this).closest(".tweet").find('#TweetCalendarId').val();
                        timestamp = $(this).closest(".tweetWrapper").attr('class').split(' ').pop().split('-')[1];
                        $(this).closest(".tweetWrapper").load('/editorial_calendars/tweet/' + calendar_id + '/' + timestamp, function () {
                            warnMessage = null;
                            $(this).closest('.tweet').css('opacity', 1);
                            toastr.success('Saved successfully');
                            console.log(data);
                            //pusher.disconnect();
                            //$('#loading').hide();
                        });
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
                //$('#progress table').load('/twitter/progressrefresh/daybyday/<?echo $this->Session->read("Auth.User.monthSelector");?>');
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

        $("#tableContainer").on("click", ".fixedScroller td", function() {
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

        $('.tweetWrapper').on("click", '.fa.fa-camera', function () {
            $(this).closest('.tweet').find('.imageUpload').show();
        });

        $(".tweetWrapper").on("click", ".deleteImagelink", function(event) {
            event.preventDefault();
            $(this).closest(".tweet").css('opacity', '.4');
            id = $(this).closest('.tweet').find('#TweetId').val();
            $.ajax({
                url: "/twitter/deleteImage/" + id, 
                context: this,
                success: function(data) {
                    calendar_id = $(this).closest(".tweet").find('#TweetCalendarId').val();
                    timestamp = $(this).closest(".tweetWrapper").attr('class').split(' ').pop().split('-')[1];
                    $(this).closest(".tweetWrapper").load('/editorial_calendars/tweet/' + calendar_id + '/' + timestamp, function () {
                        warnMessage = null;
                        $(this).closest('.tweet').css('opacity', 1);
                        toastr.success(data);
                        //pusher.disconnect();
                        //$('#loading').hide();
                    });
                },
                error: function(data) {
                    calendar_id = $(this).closest(".tweet").find('#TweetCalendarId').val();
                    timestamp = $(this).closest(".tweetWrapper").attr('class').split(' ').pop().split('-')[1];
                    $(this).closest(".tweetWrapper").load('/editorial_calendars/tweet/' + calendar_id + '/' + timestamp, function () {
                        warnMessage = null;
                        $(this).closest('.tweet').css('opacity', 1);
                        toastr.error(data);
                    });
                }
            });




        });


        $("#table").on("click", ".longbutton", function(event) {
            $("#table").css('opacity', '.4');
            $('#loading').show();
            event.preventDefault();
            var dat = new FormData();
            $('input[name=tosubmit][value=true]').each(function () {
                $(this).closest('.tweet').find('input:not([type=radio]), textarea, input[type=radio]:checked').each(function () {
                    if ($(this).attr('type') == 'file') {
                        dat.append($(this).attr('name'), this.files[0]);
                    } else {
                        dat.append($(this).attr('name'), $(this).val());
                    }
                });
            });

            $.ajax({
                url: "/editorial_calendars/editMultipleCalendarTweet", 
                context: this,
                type: "POST",
                data: dat,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function () {
                        $('#table').css('opacity', '1');
                        $('#loading').hide();
                        toastr.success("Saved successfully");
                    });
                },
                error: function(data) {
                    $('#table').load('/editorial_calendars/calendarrefresh/<?echo $this->Session->read("Auth.User.monthSelector");?>', function () {
                        $('#table').css('opacity', '1');
                        $('#loading').hide();
                        toastr.error("Error, please try again.");
                    });
                }
            });
        });

        $('.autoPopulate').click(function () {
            $("#table").css('opacity', '.4');
            $('#loading').show();
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
                    $("#table").css('opacity', '1');
                    $('#loading').hide();
                }
            });
        });

        });

</script>
