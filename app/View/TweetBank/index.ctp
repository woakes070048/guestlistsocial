<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"> </script>
<script type="text/javascript" src="http://malsup.github.io/jquery.form.js"></script> 
<?
echo $this->Html->script('jquery-ui-1.10.3.custom');
echo $this->Html->script('toastr.min');
echo $this->Html->css('toastr.min');?>

<?
echo $this->Form->create('TwitterAccount');
        echo $this->Form->input('Select Account:', array(
        'name' => 'accountSubmit',
        'onchange' => 'this.form.submit()',
        'options' => array('empty' => 'Select Account...', array_combine($accounts,$accounts)), //Setting the HTML "value" = to screen_name
        'selected' => $selected
        )); 
echo $this->Form->end();
$a = $this->Session->read('access_token.account_id');
if (!empty($a)) {
	echo $this->Form->create('CategorySelect');
	echo $this->Form->input('Category', array(
		'type' => 'select',
		'name' => 'BankCategory',
		'onchange' => 'this.form.submit()',
		'options' => $categories,
		'selected' => $selectedCategories,
		'empty' => 'Select a Category'
		));
	echo $this->Form->end();
}

echo $this->Form->create('TweetBank', array('url' => '/TweetBank/save', 'type' => 'file'));?>
<div class='tweetBank' data-id='new'>
	<? echo $this->Form->input('bank_category_id', array('name' => 'data[TweetBank][new][bank_category_id]', 'type' => 'select', 'options' => $categories, 'label' => false, 'empty' => 'Select a Category', 'selected' => $selectedCategories));?>
	<? echo $this->Form->textarea('body', array('name' => 'data[TweetBank][new][body]', 'placeholder' => 'Add a new Tweet to the bank', 'label' => false, 'required' => false));?>
	<? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[TweetBank][new][img_url1]', 'label' => false, 'class' => 'imgupload', 'div' => array('style' => 'float: none'))); ?>
	<div id="imagePreviewnew" class='imagecontainer'>
        <img src='' style='max-width:500px'>
    </div>
</div>

<?
foreach ($tweets as $key) {?>
	<div class='tweetBank' data-id='<?echo $key['TweetBank']['id'];?>'>
		<? echo $this->Html->link($this->Html->image('/img/trash.png'), array('action' => 'delete/' . $key['TweetBank']['id']), array('class' => 'deleteimage', 'escape' => false));?>
		<? echo $this->Form->input('bank_category_id', array('name' => 'data[TweetBank][' . $key['TweetBank']['id'] . '][bank_category_id]', 'type' => 'select', 'options' => $categories, 'selected' => $key['TweetBank']['bank_category_id'], 'label' => false));?>
		<? echo $this->Form->textarea('body', array('name' => 'data[TweetBank][' . $key['TweetBank']['id'] . '][body]', 'value' => $key['TweetBank']['body'], 'label' => false));?>
		<? echo $this->Form->input('img_url1', array('type' => 'file', 'name' => 'data[TweetBank][' . $key['TweetBank']['id'] . '][img_url1]', 'label' => false, 'class' => 'imgupload', 'div' => array('style' => 'float: none'))); ?>
		<? if (!empty($key['TweetBank']['img_url'])) { ?>
            <div class='imagecontainer'>
                <? echo $this->Html->image($key['TweetBank']['img_url'], array('style' => 'max-width:500px')); ?>
                <? echo $this->Html->link("<div class='deleteimage'>Delete image</div>", array('controller' => 'twitter', 'action' => 'deleteImage', $key['TweetBank']['id']), array('escape' => false));?>
            </div>
    	<? } else {?>
        <div id="imagePreview<?echo$key['TweetBank']['id'];?>" class='imagecontainer'>
            <img src='' style='max-width:500px'>
        </div>
    	<? } ?>
	</div>
<?}
echo $this->Form->end('Save');?>

<script> 
// wait for the DOM to be loaded 
$(document).ready(function () { 
$('.deleteimage').click(function (e) {
    e.preventDefault();
    url = $(this).attr('href');
    $.ajax({
        url: url,
        context: $(this),
        success: function(data) {
            toastr.success('Deleted successfully.');
            $(this).closest('.tweetBank').find('input, select, textarea').prop('disabled', true);
            $(this).closest('.tweetBank').hide();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) { 
            toastr.error('An error occurred. Please try again.');
        }   
    });
});

$('input[type=submit]').click(function (e) {
	e.preventDefault();
	$("#TweetBankIndexForm").ajaxSubmit({
		url: '/TweetBank/save',
		type: 'post',
		success: function(data) {
			toastr.success('Saved successfully');
		},
		error: function() {
			toastr.error('An error occurred. Please try again.');
		}
	});
});

$('.input.file input').on('change', function() {
    $(this).parent().css('background', "url(/img/upload_image_green.png) left center no-repeat");
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
});
</script>