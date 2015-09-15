<? echo $this->Html->script('dropzone');
echo $this->Html->css('dropzone');?>

<form action="/twitter/uploadimage"
      class="dropzone"
      id="my-awesome-dropzone">
<?echo $this->Form->submit('go');?></form>