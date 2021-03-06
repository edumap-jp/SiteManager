<?php
/**
 * SiteManager Helper
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('AppHelper', 'View/Helper');
App::uses('Room', 'Rooms.Model');
App::uses('Space', 'Rooms.Model');
App::uses('SiteManagerComponent', 'SiteManager.Controller/Component');

/**
 * サイト管理ヘルパー
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\SiteManager\View\Helper
 */
class SiteManagerHelper extends AppHelper {

/**
 * 使用するヘルパー
 *
 * @var array
 */
	public $helpers = array(
		'Form',
		'M17n.SwitchLanguage',
		'NetCommons.NetCommonsForm',
		'NetCommons.NetCommonsHtml',
	);

/**
 * タブ
 *
 * @var array
 */
	protected $_tabs = array(
		'site_manager' => array(
			'controller' => 'site_manager',
			'action' => 'edit',
		),
		'meta_settings' => array(
			'controller' => 'meta_settings',
			'action' => 'edit',
		),
		'default_page_settings' => array(
			'controller' => 'default_page_settings',
			'action' => 'edit',
			//ここは、__constractでやるように修正
			//'key' => Room::PUBLIC_PARENT_ID
		),
		'membership' => array(
			'controller' => 'membership',
			'action' => 'edit',
		),
		'workflow' => array(
			'controller' => 'workflow',
			'action' => 'edit',
		),
		'mail_signature_settings' => array(
			'controller' => 'mail_signature_settings',
			'action' => 'edit',
		),
		'use_languages' => array(
			'controller' => 'use_languages',
			'action' => 'edit',
		),
	);

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		$this->_tabs['default_page_settings']['key'] = Space::getRoomIdRoot(Space::PUBLIC_SPACE_ID);

		parent::__construct($View, $settings);
	}

/**
 * Before render callback. beforeRender is called before the view file is rendered.
 *
 * Overridden in subclasses.
 *
 * @param string $viewFile The view file that is going to be rendered
 * @return void
 */
	public function beforeRender($viewFile) {
		$this->NetCommonsHtml->css(array(
			'/site_manager/css/style.css'
		));
		$this->NetCommonsHtml->script(array(
			'/site_manager/js/site_manager.js'
		));
		parent::beforeRender($viewFile);
	}

/**
 * タブの出力
 *
 * @param string|null $active アクティブタブ
 * @return string HTML
 */
	public function tabs($active = null) {
		if (! isset($active)) {
			$active = $this->_View->request->params['controller'];
		}

		$output = '';
		$output .= '<ul class="nav nav-tabs" role="tablist">';
		foreach ($this->_tabs as $key => $tab) {
			$output .= '<li class="' . ($key === $active ? 'active' : '') . '">';
			$output .= $this->NetCommonsHtml->link(__d('site_manager', 'Tab.' . $key), $tab);
			$output .= '</li>';
		}
		$output .= '</ul>';

		return $output;
	}

/**
 * タブの出力
 *
 * @return string HTML
 */
	public function roomTabs() {
		$output = '';
		$output .= '<ul class="nav nav-pills" role="tablist">';
		foreach ($this->_View->viewVars['rooms'] as $roomId => $room) {
			if ((string)$roomId === $this->_View->viewVars['activeRoomId']) {
				$active = 'active';
			} else {
				$active = '';
			}
			$output .= '<li class="' . $active . '">';
			$output .= $this->NetCommonsHtml->link(
							Hash::get($room, 'RoomsLanguage.name'),
							Hash::merge(
								Hash::get($this->_tabs, $this->_View->request->params['controller']),
								array('key' => $roomId)
							)
						);
			$output .= '</li>';
		}
		$output .= '</ul>';

		return $output;
	}

/**
 * タブの出力
 *
 * @return string HTML
 */
	public function membershipTabs() {
		$output = '';

		$output .= '<ul class="nav nav-pills" role="tablist">';

		$tabs = array(
			'automatic-registration' => __d('site_manager', 'Automatic registration'),
			'membership-cancellation' => __d('site_manager', 'Membership cancellation'),
		);

		$active = $this->_View->viewVars['membershipTab'];
		foreach ($tabs as $key => $label) {
			if ($key === $active) {
				$output .= '<li class="active">';
			} else {
				$output .= '<li>';
			}

			$output .= '<a href="#' . $key . '" aria-controls="' . $key . '" role="tab" data-toggle="tab" ' .
							'ng-click="' . $this->domId('membershipTab') . ' = \'' . $key . '\'">';
			$output .= $label;
			$output .= '</a>';
			$output .= '</li>';
		}

		$output .= '</ul>';

		return $output;
	}

/**
 * inputタグ
 *
 * @param string $model モデル名
 * @param string $key キー
 * @param int $languageId 言語ID
 * @return string HTML
 */
	public function inputHidden($model, $key, $languageId) {
		$output = '';

		$requestKey = SiteManagerComponent::convertRequestKey($key);
		if (! isset($this->_View->request->data[$model][$requestKey])) {
			return $output;
		}

		$inputValue = $model . '.' . $requestKey . '.' . $languageId;

		//id
		$output .= $this->NetCommonsForm->hidden($inputValue . '.id');
		//key
		$output .= $this->NetCommonsForm->hidden($inputValue . '.key');
		//language_id
		$output .= $this->NetCommonsForm->hidden($inputValue . '.language_id');

		return $output;
	}

/**
 * ヘルプオプションの取得
 *
 * @param string $key キー
 * @param array $options オプション
 * @return array オプション
 */
	private function __getHelpOption($key, $options = array()) {
		if (Hash::get($options, 'mailHelp', false)) {
			$help = '';
			if (is_array(Hash::get($options, 'mailHelp'))) {
				$help .= Hash::get($options, 'mailHelp.addMessage');
			}
			$help .= $this->NetCommonsHtml->mailHelp(Hash::get($options, 'help'));
			$options = Hash::insert($options, 'help', $help);
			$options = Hash::remove($options, 'mailHelp');
		}

		return $options;
	}

/**
 * inputタグ
 *
 * @param string $model モデル名
 * @param string $key キー
 * @param array $options オプション
 * @return string HTML
 */
	public function inputCommon($model, $key, $options = array()) {
		$output = '';

		$requestKey = SiteManagerComponent::convertRequestKey($key);
		if (! isset($this->_View->request->data[$model][$requestKey])) {
			return $output;
		}

		$label = Hash::get($options, 'label');
		$options = Hash::remove($options, 'label');

		$options = $this->__getHelpOption($key, $options);

		$languageId = '0';
		$inputValue = $model . '.' . $requestKey . '.' . $languageId;

		//hidden
		$output .= $this->inputHidden($model, $key, $languageId);

		if (Hash::get($options, 'type', 'text') === 'radio') {
			$options = Hash::merge(array(
				'class' => false,
				'inline' => true
			), $options);
		}

		$output .= $this->NetCommonsForm->input($inputValue . '.value',
			Hash::merge(array(
				'label' => $label,
			), $options)
		);
		return $output;
	}

/**
 * inputタグ(言語)
 *
 * @param string $model モデル名
 * @param string $key キー
 * @param array $options オプション
 * @return string HTML
 */
	public function inputLanguage($model, $key, $options = array()) {
		$output = '';

		$requestKey = SiteManagerComponent::convertRequestKey($key);
		if (! isset($this->_View->request->data[$model][$requestKey])) {
			return $output;
		}

		$label = Hash::get($options, 'label');
		$options = Hash::remove($options, 'label');

		$options = $this->__getHelpOption($key, $options);

		$activeLangId = $this->_View->viewVars['activeLangId'];
		$languageIds = array_keys($this->_View->viewVars['languages']);
		foreach ($languageIds as $languageId) {
			$inputValue = $model . '.' . $requestKey . '.' . $languageId;

			if ((string)$activeLangId === (string)$languageId) {
				$active = ' active';
			} else {
				$active = '';
			}
			$output .= '<div class="tab-pane' . $active . '" ' .
							'ng-show="' . 'activeLangId === \'' . $languageId . '\'' . '">';

			//hidden
			$output .= $this->inputHidden($model, $requestKey, $languageId);

			if (Hash::get($options, 'type') === 'wysiwyg') {
				//$options = Hash::remove($options, 'type');
				$field = SiteManagerComponent::invertRequestKey($inputValue . '.value');

				$value = h(json_encode($this->getValue($model, $key, $languageId)));

				$output .= '<div ng-init="' . $this->domId($field) . ' = ' . $value . '">';
				$output .= $this->NetCommonsForm->wysiwyg($inputValue . '.value',
					Hash::merge(
						array(
							'label' => $this->SwitchLanguage->inputLabel($label, $languageId),
							'ng-model' => $this->domId($field),
						),
						$options
					)
				);
				$output .= '</div>';
			} else {
				$output .= $this->NetCommonsForm->input($inputValue . '.value',
					Hash::merge(array(
						'label' => $this->SwitchLanguage->inputLabel($label, $languageId),
					), $options)
				);
			}

			$output .= '</div>';
		}

		return $output;
	}

/**
 * 値の取得
 *
 * @param string $model モデル名
 * @param string $key キー
 * @param int $languageId 言語ID
 * @return string HTML
 */
	public function getValue($model, $key, $languageId = '0') {
		$requestKey = SiteManagerComponent::convertRequestKey($key);
		return Hash::get($this->_View->request->data[$model][$requestKey], $languageId . '.value');
	}

/**
 * サイト停止のヘルプの表示
 *
 * @param string $placement ポジション
 * @return string ヘルプHTML出力
 */
	public function helpSiteClose($placement = 'bottom') {
		$html = '';

		$content = __d('net_commons', '{X-SITE_NAME} : Site name');
		$content = __d('net_commons', 'Each of the embedded keywords, will be converted ' .
				'to the corresponding content. <br />') . $content;

		$html .= __d('net_commons', 'Can use an embedded keyword.') . ' ';
		$html .= '<a href="" data-toggle="popover" data-placement="' . $placement . '"' .
					' title="' . __d('mails', 'Embedded keyword?') . '"' . ' data-content="' . $content . '">';
		$html .= '<span class="glyphicon glyphicon-info-sign"></span>';
		$html .= '</a>';
		$html .= '<script type="text/javascript">' .
			'$(function () { $(\'[data-toggle="popover"]\').popover({html: true}) });</script>';

		return $html;
	}

}
