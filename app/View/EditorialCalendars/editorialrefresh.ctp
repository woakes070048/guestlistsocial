<?echo $this->Form->create('Calendar', array('url'=>$this->Html->url(array('controller'=>'editorial_calendars', 'action'=>'calendarsave')), 'id' => 'edit'));?>
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
foreach ($calendar as $key) {?>
    <tr class="topic">
        <td><? echo $key['EditorialCalendar']['time']; ?></td>
        <?
        foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'topic')) {
                    echo '<td>' . $value1 . '</td>';
                }
            }
        }?>
    </tr>
    <tr class="content-type">
        <td>Content type</td>
        <?
        foreach ($key['EditorialCalendar'] as $key1 => $value1) {
            if ($key1 != 'id') {
                if (strpos($key1, 'content')) {
                    echo '<td>' . $value1 . '</td>';
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
                    echo '<td>' . $value1 . '</td>';
                }
            }
        }?>
    </tr>
    <tr style="height: 40px;">
    
    </tr>
<?
}
?>
</table>

<div id='hide'>
<? echo $this->Form->button('HIDE EDITORIAL CALENDAR', array('type' => 'button', 'class' => 'hide')); ?>
<? echo $this->Form->button('SHOW EDITORIAL CALENDAR', array('type' => 'button', 'class' => 'show')); ?>
</div>
<script>
    $(document).ready(function () {

        $('.show').hide();

        <?if ($this->Session->read('Auth.User.show_calendar') === 0) {?>
            $('table#table1').hide();
            $('.hide').hide();
            $('.show').show();
        <? } ?>
        
        $('#hide').on('click', '.hide', function() {
            $('#table1').hide();
            $('.hide').hide();
            $('.show').show();
            $.ajax({
                url: "/editorial_calendars/hidecalendar"
            })
        });
        $('#hide').on('click', '.show', function() {
            $('#table1').show();
            $('.show').hide();
            $('.hide').show();
            $.ajax({
                url: "/editorial_calendars/showcalendar"
            })
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
}
</style>