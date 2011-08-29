<?php
App::import('Controller', 'Users.Users');
class AppUsersController extends UsersController {
	public $viewPath = 'app_users';

	public function beforeFilter() {
		parent::beforeFilter();
		$this->User = ClassRegistry::init('AppUser');
		$this->User->Detail = ClassRegistry::init('AppDetail');
	}

	public function render($action = null, $layout = null, $file = null) {
		if (is_null($action)) {
			$action = $this->action;
		}
		if (!file_exists(VIEWS . $this->viewPath . DS . $action . '.ctp')) {
			$file = App::pluginPath('users') . 'views' . DS . 'users' . DS . $action . '.ctp';
		}
		return parent::render($action, $layout, $file);
	}

/**
 * Sets the cookie to remember the user (overriding plugin method)
 *
 * @param array Cookie component properties as array, like array('domain' => 'yourdomain.com')
 * @param string Cookie data keyname for the userdata, its default is "User". This is set to User and NOT using the model alias to make sure it works with different apps with different user models accross different (sub)domains.
 * @return void
 * @link http://api13.cakephp.org/class/cookie-component
 */
	protected function _setCookie($options = array(), $cookieKey = 'User') {
		if (empty($this->data[$this->modelClass]['remember_me'])) {
			$this->Cookie->delete($cookieKey);
		} else {
			$validProperties = array('domain', 'key', 'name', 'path', 'secure', 'time');
			$defaults = array(
				'name' => $this->_cookieName);

			$options = array_merge($defaults, $options);
			foreach ($options as $key => $value) {
				if (in_array($key, $validProperties)) {
					$this->Cookie->{$key} = $value;
				}
			}

			$cookieData = array();
			$cookieData[$this->Auth->fields['username']] = $this->data[$this->modelClass][$this->Auth->fields['username']];
			$cookieData[$this->Auth->fields['password']] = $this->data[$this->modelClass][$this->Auth->fields['password']];
			$this->Cookie->write($cookieKey, $cookieData, true, '1 Month');
		}
		unset($this->data[$this->modelClass]['remember_me']);
	}

/**
 * Edit
 *
 * @param string $id User ID
 * @return void
 */
	public function edit() {
		if (!empty($this->data)) {
//die(debug($this->data));
//			$this->data['Detail'] = $this->data['Detail']['User'];
//			unset($this->data['Detail']['User']);

			if ($this->User->Detail->saveSection($this->Auth->user('id'), $this->data, 'User')) {
				$this->data['Detail']['User'] = $this->data['Detail'];
				$this->Session->setFlash(__d('users', 'Profile saved.', true));
			} else {
				$this->Session->setFlash(__d('users', 'Could not save your profile.', true));
			}
		} else {
			$this->data = $this->User->find('first', array(
				'conditions' => array(
					'User.id' => $this->Auth->user('id'),
				)
			));
		}

		$this->_setLanguages();
	}

}
