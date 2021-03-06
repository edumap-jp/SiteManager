<?php
/**
 * 一般設定 Element
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Shohei Nakajima <nakajimashouhei@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('SiteSetting', 'SiteManager.Model');
App::uses('M17nHelper', 'M17n.View/Helper');
?>

<article>
	<?php echo $this->SiteManager->inputLanguage('SiteSetting', 'App.site_name', array(
		'label' => __d('site_manager', 'App.site_name'),
		'required' => true
	)); ?>

	<?php echo $this->SiteManager->inputCommon('SiteSetting', 'Config.language', array(
		'type' => 'select',
		'empty' => __d('site_manager', 'Automatic language'),
		'options' => array_map('__', array_intersect_key(M17nHelper::$languages, array_flip($languages))),
		'label' => __d('site_manager', 'Config.language'),
		'help' => __d('site_manager', 'Config.language help'),
	)); ?>

	<?php echo $this->SiteManager->inputCommon('SiteSetting', 'App.default_start_room', array(
		'type' => 'select',
		'options' => $rooms,
		'label' => __d('site_manager', 'App.default_start_room'),
		'help' => __d('site_manager', 'App.default_start_room help'),
	)); ?>
</article>
