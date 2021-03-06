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

App::uses('AppController', 'Controller');

/**
 * SiteManagerApp Controller
 *
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @package NetCommons\SiteManager\Controller
 */
class SiteManagerAppController extends AppController {

/**
 * 使用コンポーネント
 *
 * @var array
 */
	public $components = array(
		'ControlPanel.ControlPanelLayout',
		'M17n.SwitchLanguage',
		'NetCommons.Permission' => array(
			'type' => PermissionComponent::CHECK_TYPE_SYSTEM_PLUGIN,
			'allow' => array()
		),
		'Security',
		'SiteManager.SiteManager',
	);

/**
 * 使用ヘルパー
 *
 * @var array
 */
	public $helpers = array(
		'SiteManager.SiteManager',
	);
}
