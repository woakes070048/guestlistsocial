<div id="refresh1">
<?echo $this->Form->create('TweetBank', array('url' => '/TweetBank/save', 'type' => 'file'));?>
<div class='tweetBank' data-id='new' style='background-color: rgb(255, 252, 161)'>
	<div class='tweetBankTop'>
		<div class='bankCategoryContainer' style="background-color:<?echo$category[0]['BankCategory']['color'];?>">
			<?echo $category[0]['BankCategory']['category'];?>
		</div>
	</div>
	<? echo $this->Form->textarea('body', array('name' => 'data[TweetBank][new][body]', 'label' => false, 'placeholder' => 'Add new tweet...'));?>
	<div class='tweetButtons'>
			<? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[TweetBank][new][img_url1]', 'label' => false, 'class' => 'imgupload', 'div' => array('style' => 'float: none'))); ?>
		<? echo $this->Form->input('bank_category_id', array('name' => 'data[TweetBank][new][bank_category_id]', 'value' => $bank_category_id, 'type' => 'hidden'));?>
			<i class='fa fa-floppy-o saveTweetBank'></i>
			<i class='fa fa-trash-o'></i>	
			<i class='fa fa-code'></i>
		</div>
	<div id="imagePreviewnew" class='imagecontainer'>
        <img src='' style='max-width:300px'>
    </div>
</div>
<?foreach ($tweets as $key) {?>
	<div class='tweetBank' data-id='<?echo $key['TweetBank']['id'];?>'>
		<? //echo $this->Html->link($this->Html->image('/img/trash.png'), array('action' => 'delete/' . $key['TweetBank']['id']), array('class' => 'deleteimage', 'escape' => false));?>
		<div class='tweetBankTop'>
			<div class='bankCategoryContainer' style="background-color:<?echo$key['BankCategory']['color'];?>">
				<?echo $key['BankCategory']['category'];?>
			</div>
		</div>
		<? //echo $this->Form->input('bank_category_id', array('name' => 'data[TweetBank][' . $key['TweetBank']['id'] . '][bank_category_id]', 'type' => 'select', 'options' => $categories, 'selected' => $key['TweetBank']['bank_category_id'], 'label' => false));?>
		<? echo $this->Form->textarea('body', array('name' => 'data[TweetBank][' . $key['TweetBank']['id'] . '][body]', 'value' => $key['TweetBank']['body'], 'label' => false));?>
		<? echo $this->Form->input('bank_category_id', array('name' => 'data[TweetBank][' . $key['TweetBank']['id'] . '][bank_category_id]', 'value' => $bank_category_id, 'type' => 'hidden'));?>
		<div class='tweetButtons'>
			<? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[TweetBank][' . $key['TweetBank']['id'] . '][img_url1]', 'label' => false, 'class' => 'imgupload', 'div' => array('style' => 'float: none'))); ?>
			<i class='fa fa-floppy-o saveTweetBank'></i>
			<i class='fa fa-trash-o deleteTweetBank'></i>	
			<i class='fa fa-code'></i>
		</div>
		<? if (!empty($key['TweetBank']['img_url'])) { ?>
            <div class='imagecontainer' style="max-width: 290px">
                <? echo $this->Html->link("<i class='fa fa-times-circle deleteimage' style='margin-left: 270px'></i>", array('controller' => 'tweetBank', 'action' => 'deleteImage', $key['TweetBank']['id']), array('escape' => false));?>
                <? echo $this->Html->image($key['TweetBank']['img_url'], array('style' => 'max-width:290px')); ?>
            </div>
    	<? } else {?>
        <div id="imagePreview<?echo$key['TweetBank']['id'];?>" class='imagecontainer'>
            <img src='' style='max-width:300px'>
        </div>
    	<? } ?>
	</div>
<?}



echo $this->Form->end(array('class' => 'saveTweetBank', 'label' => 'Save'));?>
</div>

<script>
$(document).ready(function () {

imagesLoaded('#refresh1', function() {
	$("#refresh1").masonry({
	    itemSelector: '.tweetBank',
	    columnWidth: 302,
	    gutter: 27,
	    isOriginTop: true
	});
});

$('.saveTweetBank').click(function (e) {
	e.preventDefault();
	$("#TweetBankCalendarrefreshForm").ajaxSubmit({
		url: '/TweetBank/save',
		type: 'post',
		success: function(data) {
			toastr.success('Saved successfully');
			$('#loading').show();
			$('#refresh').load('/tweetBank/calendarrefresh/' + <?echo$bank_category_id;?>);
			$('#loading').hide();
		},
		error: function() {
			toastr.error('An error occurred. Please try again.');
		}
	});
});

$('.deleteTweetBank').click(function () {
	id = $(this).closest('.tweetBank').attr('data-id');
	$.ajax({
	    url: '/tweetBank/delete/' + id,
	    context: $(this),
	    success: function(data) {
	        toastr.success('Deleted successfully.');
	        $(this).closest('.tweetBank').find('input, select, textarea').prop('disabled', true);
	        $(this).closest('.tweetBank').hide();
			$("#refresh1").masonry({
			    itemSelector: '.tweetBank',
			    columnWidth: 302,
			    gutter: 27,
			    isOriginTop: true
			});
	    },
	    error: function(XMLHttpRequest, textStatus, errorThrown) { 
	        toastr.error('An error occurred. Please try again.');
	    }   
	});
});

$('.input.file input').on('change', function() {
    $(this).parent().css('color', "#0788D3");
	var files = !!this.files ? this.files : [];
    if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support

    if (/^image/.test( files[0].type)){ // only image file
        var reader = new FileReader(); // instance of the FileReader
        reader.readAsDataURL(files[0]); // read the local file
        var id = $(this).closest(".tweetBank").attr('data-id');
        reader.onloadend = function(){ // set image data as background of div
            $("#imagePreview" + id + " img").attr('src', this.result);
        }
    }

});

$('.imagecontainer a').click(function (e) {
    e.preventDefault();
    url = $(this).attr('href');
    $.ajax({
        url: url,
        context: $(this),
        success: function(data) {
            toastr.success('Deleted successfully.');
            $(this).closest('.imagecontainer').hide();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            toastr.error('An error occurred. Please try again.');
        }   
    });
});

});
</script>