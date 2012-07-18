<?php
/**
 * Administration channel controller
 * 
 * @package com.makeabyte.jemonweb.control
 */
class ChannelController extends BaseModelActionController {

	private $model;

	public function __construct() { 

		$this->model = new Channel();
		parent::__construct(false);
	}

	/**
	 * (non-PHPdoc)
	 * @see AgilePHP/mvc/BaseModelController#getModel()
	 */
	public function getModel() { 

		return $this->model;
	}
}
?>