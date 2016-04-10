<!-- Display Tweets -->
<?

    echo $this->Form->create('Tweet', array('url' => '/editorial_calendars/editcalendartweet1', 'id' => 'submitTweets', 'type' => 'file'));
?>

            <div class="tweet">
                <div class="tweetTop">
                    <div class="calendar scheduled <?echo date('jS', $tweet['timestamp']);?>">
                        <i class='fa fa-clock-o'></i>
                        <? echo date('H:i ', $tweet['timestamp']);?>
                        <b class ="<?echo date('l', $tweet['timestamp']);?>">
                        <?echo date('l', $tweet['timestamp']);?>
                        </b>
                        <? echo date('jS F Y', $tweet['timestamp']);?>
                        <? if ($tweet['published'] == 1) {?>
                            <small>[Published]</small>
                        <?}?>
                    </div>

                    <div class='categoryContainer'>
                        <div class='calendar_topic' data-category-id="<?echo $tweet['BankCategory']['id'];?>">
                            <? if (!empty($tweet['BankCategory']['category'])) {
                                echo $tweet['BankCategory']['category'];
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="textBoxAndButtons">
                <? echo $this->Form->textarea('body', 
                                                array(
                                                    'label' => false, 
                                                    'value' => $tweet['body'], 
                                                    'name' => 'data[Tweet]['.$tweet['id'].'][body]', 
                                                    'class' => 'calendar editing ' . $obj['withImage'] . ' verif' . $tweet['verified']
                                                    )
                                            ); 
                ?>
                    <div class='isTyping' style="display: inline-block"></div>
                    <div class="tweetButtons">
                        <i class="empty comments <?echo $obj['present'];?> fa fa-comments commentBadge badge<?echo $obj['commentCount'];?>" id="<? echo $tweet['id']; ?>"></i>
                        <i class="smallSaveButton fa fa-floppy-o"></i>
                        <i class="urlSubmit1 shortsingle fa fa-code"></i>
                        <i class="fa fa-camera"></i>
                    </div>
                </div>

                <div class="imageUpload" style="display:none">
                    <? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[Tweet]['.$tweet['id'].'][img_url1]', 'label' => "<span class='button'>Upload Image</span>", 'class' => 'button', 'id' => 'TweetImgUrl1' . $obj['idForPusher'])); ?>
                    <span>OR</span>
                    <? echo $this->Form->input('img_url2', array('name' => 'data[Tweet]['.$tweet['id'].'][img_url2]', 'label' => false, 'placeholder' => 'Paste Link...', 'class' => 'TweetImgUrl2'));?>
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
                            'name' => 'data[Tweet]['.$tweet['id'].'][verified]',
                            'class' => 'calendar TwitterVerified1',
                            'id' => $tweet['id'],
                            'default' => $tweet['verified'],
                            $obj['disabled'],
                            'before' => '<div class="verifiedLabel">',
                            'separator' => '</div><div class="verifiedLabel">',
                            'after' => '</div>'
                        )
                );?>
                    
                    <?if (!empty($tweet['Editor'])) {?>
                    <ul style='list-style: none; font-size: 9px; margin: 5px 0 5px 5px;'><?
                        foreach ($tweet['Editor'] as $keyx) {
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
                <? if (!empty($tweet['img_url'])) {?>
                    <div class='imagecontainer'>
                        <? echo $this->Html->link("<i class='deleteimage fa fa-times'></i>", array('controller' => 'twitter', 'action' => 'deleteImage', $tweet['id']), array('escape' => false, 'class' => 'deleteImagelink'));?>
                        <img class="lazy" data-original="<?echo $tweet['img_url']?>" style="max-width:496px" />
                    </div>
                <?}?>
                <div id="imagePreview<?echo$obj['idForPusher'];?>" class='imagecontainer' style="display: none">
                    <img src='' style='max-width:496px'>
                </div>
                    <?
                    echo $this->Form->input('date', array('type' => 'hidden', 'value' => $obj['calendarTime'], 'name' => 'data[Tweet]['.$tweet['id'].'][time]'));
                    echo $this->Form->input('id', array('type' => 'hidden', 'value' => $tweet['id'], 'name' => 'data[Tweet]['.$tweet['id'].'][id]', 'data-id' => $obj['idForPusher']));
                    echo $this->Form->input('calendar_id', array('type' => 'hidden', 'value' => $tweet['EditorialCalendar']['id'], 'name' => 'data[Tweet]['.$tweet['id'].'][calendar_id]'));
                    echo $this->Form->input('img_url', array('type' => 'hidden', 'value' => false, 'name' => 'data[Tweet]['.$tweet['id'].'][img_url]'));
                    echo $this->Form->input('forceVerified', array('type' => 'hidden', 'value' => false, 'name' => 'data[Tweet]['.$tweet['id'].'][forceVerified]'));
                    echo $this->Form->input('tosubmit', array('type' => 'hidden', 'value' => false, 'name' => 'tosubmit'));
                    echo $this->Form->input('tweet_bank_id', array('type' => 'hidden', 'value' => $tweet['tweet_bank_id'], 'name' => 'data[Tweet]['.$tweet['id'].'][tweet_bank_id]'));
                    ?>
            </div>
<script> 
        // wait for the DOM to be loaded 
        $(document).ready(function () {
            $(".<?echo $tweet['EditorialCalendar']['id'];?>-<?echo $tweet['timestamp'];?>").find('.editing.withoutImage').charCount({css: 'counter counter1', allowed: 140});
            $(".<?echo $tweet['EditorialCalendar']['id'];?>-<?echo $tweet['timestamp'];?>").find('.editing.withImage').charCount({css: 'counter counter2', allowed: 117});

            $(".<?echo $tweet['EditorialCalendar']['id'];?>-<?echo $tweet['timestamp'];?> .lazy").lazyload();

            $(".<?echo $tweet['EditorialCalendar']['id'];?>-<?echo $tweet['timestamp'];?> .calendar_topic").qtip({
                overwrite: false,
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

            $(".<?echo $tweet['EditorialCalendar']['id'];?>-<?echo $tweet['timestamp'];?> .comments.present").qtip({ 
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