<?php

class Marketify_Template {

	public function __construct() {
		$this->includes();
		$this->setup();
	}

	private function includes() {
		$files = array(
			'class-template-assets.php',
			'class-template-page-templates.php',
			'class-template-navigation.php',
			'class-template-header.php',
			'class-template-page-header.php',
			'class-template-entry.php',
			'class-template-comments.php',
			'class-template-pagination.php',
		);

		foreach ( $files as $file ) {
			require( get_template_directory() . '/inc/template/' . $file );
		}
	}

	private function setup() {
		$this->assets = new Marketify_Template_Assets();
		$this->page_templates = new Marketify_Template_Page_Templates();
		$this->navigation = new Marketify_Template_Navigation();
		$this->header = new Marketify_Template_Header();
		$this->page_header = new Marketify_Template_Page_Header();
		$this->entry = new Marketify_Template_Entry();
		$this->comments = new Marketify_Template_Comments();
		$this->pagination = new Marketify_Template_Pagination();
	}

}
