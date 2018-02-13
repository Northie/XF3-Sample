<?php

namespace settings;

trait _general {

	protected $settings = [];

	protected function readSettings() {
		$settings = [];

		//$settings['ENVIRONMENT'] = 'LIVE';
		//$settings['ENVIRONMENT'] = 'STAGING';	//User Acceptance
		//$settings['ENVIRONMENT'] = 'TESTING';	//Quality Assurance
		$settings['ENVIRONMENT'] = 'DEVELOPMENT';

		$this->settings = $settings;
	}

}
