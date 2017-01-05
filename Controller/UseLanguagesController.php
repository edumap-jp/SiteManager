<?php
/**
 * SiteManagerApp Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('SiteManagerAppController', 'SiteManager.Controller');
App::uses('M17n.M17nHelper', 'M17n.Controller');

/**
 * サイト管理【利用言語設定】
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\SiteManager\Controller
 */
class UseLanguagesController extends SiteManagerAppController {

/**
 * 使用するモデル
 *
 * @var array
 */
	public $uses = array(
		'SiteManager.SiteSetting',
		'M17n.Language',
	);

/**
 * Helpers
 */
	public $helpers = array(
		'M17n.M17n',
	);

/**
 * edit
 *
 * @return void
 */
	public function edit() {
		$languages = $this->Language->find('list', array(
			'fields' => array('code', 'is_active'),
			'recursive' => -1,
		));
		$activeLangs = array();
		$defaultLangs = array_intersect_key(M17nHelper::$languages, $languages);
		foreach ($defaultLangs as $code => $value) {
			if ($languages[$code]) {
				$activeLangs[] = $code;
			}
			$defaultLangs[$code] = __d('m17n', $value);
		}

		if ($this->request->is('post')) {
			try {
				$this->Language->begin();

				$result = $this->Language->saveActive($this->data);
				if (! $result) {
					$this->NetCommons->handleValidationError($this->Language->validationErrors);
					$this->set('validationErrors', $this->Language->validationErrors);
					$activeLangs = array();
				}

				$this->Language->commit();

			} catch (Exception $ex) {
				$this->Language->rollback($ex);
			}

			if ($result) {
				$this->NetCommons->setFlashNotification(__d('net_commons', 'Successfully saved.'), array(
					'class' => 'success',
				));
				return $this->redirect($this->referer());
			}
		}

		$this->set('activeLangs', $activeLangs);
		$this->set('defaultLangs', $defaultLangs);
	}

}
