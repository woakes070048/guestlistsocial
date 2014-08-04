<?php
class Tweet extends AppModel {
	public $actsAs = array (
			'Upload.Upload' => array(
					'img_url' => array(
                		'fields' => array(
                    		'dir' => 'img_url_dir'
                		)
					)
			)
		);
}