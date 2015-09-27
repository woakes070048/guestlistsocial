<?echo $this->Form->create('Tweet', array('url'=>$this->Html->url(array('controller'=>'twitter', 'action'=>'emptySave')), 'id' => 'edit', 'type' => 'file'));?>
<?php 
$i = 0;
foreach ($tweets as $key) { ?>
<div class='tweet'>
<?php if ($key['Tweet']['verified'] == 1) {
            $checked = 'checked';
            $value = $key['Tweet']['time'];
            $color = 'Green';
        } elseif ($key['Tweet']['verified'] == 1 && $key['Tweet']['client_verified'] == 1) {
            $color = 'Green';
        } else {
            $checked = '';
            $value = '';
            $color = 'Red';
        } 

        if (!empty($key['Tweet']['img_url'])) {
            $txtareaClass = 'withImage';
        } else {
            $txtareaClass = 'withoutImage';
        }
        $id = $key['Tweet']['id'];
        $commentCount = count($key['Comment']);
        $present = 'present';

        if ($this->Session->read('Auth.User.group_id') == 2 || $status == 'published') {
            $disabled = 'disabled';
        } else {
            $disabled = '';
        }

        if (!empty($key['Editor'])) {
            $editors = $key['Editor'];
        } else {
            $editors = false;
        }?>
    <div class='tweetTop'>
        <div class="calendar scheduled <?echo date('jS', $key['Tweet']['timestamp']);?>">
                <i class='fa fa-clock-o'></i>
                <?echo date('H:i ', $key['Tweet']['timestamp']) . '<b class="' .date('l', $key['Tweet']['timestamp']) . '">' . date('l ', $key['Tweet']['timestamp']) . '</b>' . date('jS F Y', $key['Tweet']['timestamp']);?>
                <? if($key['Tweet']['published'] == 1) {
                    echo '<small>[Published]</small>';
                }?>
        </div>
        <div class='categoryContainer'>
            <div class='calendar_topic'><? if (!empty($key['BankCategory']['category'])) {echo $key['BankCategory']['category'];} ?></div>
            <!--<div class='calendar_notes'><? //if (!empty($key1['BankCategory']['category'])) {echo $key1['BankCategory']['category'];} ?> Some comment</div>-->
        </div>
    </div>
    <div class="textBoxAndButtons">
        <?echo $this->Form->textarea('body', array('label' => false, 'value' => $key['Tweet']['body'], 'name' => 'data[Tweet]['.$id.'][body]', 'class' => 'calendar editing ' . $txtareaClass));?>
        <div class='isTyping' style="display: inline-block"></div>
        <div class="tweetButtons">
        <?if ($commentCount > 9) {
            $commentCount = '9plus';
        }?>
            <i class="empty comments <?echo $present;?> fa fa-comments" id="<? echo $id; ?>"></i>
            <i class="smallSaveButton fa fa-floppy-o"></i>
            <i class="urlSubmit1 shortsingle fa fa-code"></i>
            <? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$id.'][img_url1]', 'label' => false, 'class' => 'imgupload')); ?>
        </div>
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
                'name' => 'data[Tweet]['.$id.'][verified]',
                'class' => 'calendar TwitterVerified1',
                'id' => $id,
                'default' => $key['Tweet']['verified'],
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
     <?
    echo $this->Form->input('timestamp', array('type' => 'hidden', 'value' => date('d-m-Y H:i', $key['Tweet']['timestamp']), 'name' => 'data[Tweet]['.$id.'][timestamp]'));
    echo $this->Form->input('id', array('type' => 'hidden', 'value' => $id, 'name' => 'data[Tweet]['.$id.'][id]', 'data-id' => $id));
    echo $this->Form->input('calendar_id', array('type' => 'hidden', 'value' => $key['EditorialCalendar']['id'], 'name' => 'data[Tweet]['.$id.'][calendar_id]'));
    echo $this->Form->input('img_url', array('type' => 'hidden', 'value' => false, 'name' => 'data[Tweet]['.$id.'][img_url]'));
    echo $this->Form->input('forceVerified', array('type' => 'hidden', 'value' => false, 'name' => 'data[Tweet]['.$id.'][forceVerified]'));
    echo $this->Form->input('tosubmit', array('type' => 'hidden', 'value' => false, 'name' => 'tosubmit'));
    ?>
    <? if ($key['Tweet']['img_url']) { ?>
        <div class='imagecontainer'>
            <? echo $this->Html->link("<i class='deleteimage fa fa-times'></i>", array('controller' => 'twitter', 'action' => 'deleteImage', $id), array('escape' => false));?>
            <? echo $this->Html->image($key['Tweet']['img_url'], array('style' => 'max-width:496px')); ?>
        </div>
    <?  } else {?>
        <div id="imagePreview<?echo$id;?>" class='imagecontainer'>
            <img src='' style='max-width:496px'>
        </div>
    <?  }  ?>
</div>
<?$i++;
}?>
<? echo $this->Form->end(array('id' => 'tweetsubmit1', 'label' => 'SAVE', 'value' => 'Save', 'class' => 'longbutton'));?>

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

            $(".TwitterVerified1:checked").click(function() {
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
                        url: "/twitter/emptySave",
                        data: dat,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            $('#table').load('/twitter/indexrefresh/<?echo $params;?>', function() {
                                warnMessage = null;
                                $("#table").css('opacity', '1');
                                $('#loading').hide();
                                toastr.success('Saved successfully');
                                pusher.disconnect();
                            });
                        },
                        error: function(data) {
                            $('#table').load('/twitter/indexrefresh/<?echo $params;?>', function() {
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

            $('#table1').on('click', '#tweetsubmit', function() {
                $('#submitTweets').submit();
            });

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
                tweet_id = $(this).closest(".tweet").find('#TweetId').attr('data-id');
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
                    $('#TweetId[data-id="' + data['tweet_id'] + '"]').closest('.tweet').find('.editing').text(data['body']);
                }
            );

            channel1.bind('client-body_typing',
                function(data) {
                    if (data['typing'] == true) {  
                        string = data['first_name'] + ' ' + data['last_name'] + ' is typing...'; 
                        $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('.tweet').find('.isTyping').text(string).slideDown();
                        $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('.tweet').find('.editing').attr('disabled', 'disabled');
                    } else {
                        $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('.tweet').find('.isTyping').slideUp();
                        $('#TweetId[data-id=' + data['tweet_id'] + ']').closest('.tweet').find('.editing').attr('disabled', false);
                    }
                }
            );

            $('input:submit, button:submit').on('click', function() {
                warnMessage = null;
            });

            $('.input.file input').on('change', function() {
                $(this).parent().css('color', "#0788D3");
                $(this).closest(".tweet").find('input[name=tosubmit]').val(true);
                $(this).closest(".tweet").find('.editing').addClass('withImage').removeClass('withoutImage');
                $(this).closest(".tweet").find('.counter1').hide();
                val = $(this).closest(".tweet").find('.TwitterVerified1:checked').val();
                if (val == 0) {
                    color = '#ffcc00';
                } else if (val == 1) {
                    color = '#21a750';
                } else if (val == 2) {
                    color = '#ff0000';
                }
                $(this).closest(".tweet").find('#TweetBody').css("border", "1px solid" + color);

                //$('.editing.withImage').charCount({css: 'counter counter1', allowed: 117});
                $(this).closest(".tweet").find('.editing').charCount({css: 'counter counter2', allowed: 117});

                var files = !!this.files ? this.files : [];
                if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
         
                if (/^image/.test( files[0].type)){ // only image file
                    var reader = new FileReader(); // instance of the FileReader
                    reader.readAsDataURL(files[0]); // read the local file
                    var id = $(this).closest(".tweet").find('#TweetId').attr('data-id');
                    reader.onloadend = function(){ // set image data as background of div
                        $("#imagePreview" + id + " img").attr('src', this.result);
                        $("#imagePreview" + id + " img").css('margin-top', '20px');
                    }
                }
            });

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
                    url: "/twitter/emptySave",
                    data: dat,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        $('#table').load('/twitter/indexrefresh/<?echo $params;?>', function() {
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
        });
</script>