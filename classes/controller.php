<?php
    abstract class controller {
        protected $_models = array();
        protected $_views = array();
        protected $_task = '';
        protected $_defaultView = '';
        protected $_code = '';
        public function __construct($code) {
            $this->setCode($code);
            if(req::getVar('mod') == $code || req::getVar('page') == $code) {
                $this->_defaultView = req::getVar('view', 'all', $code);
            } else {
                $this->_defaultView = $code;
            }
        }
        public function init() {
            /*load model and other preload data goes here*/
        }
        protected function _onBeforeInit() {

        }
        protected function _onAfterInit() {

        }
        public function setCode($code) {
            $this->_code = $code;
        }
        public function getCode() {
            return $this->_code;
        }
        public function exec($task = '') {
            if(method_exists($this, $task)) {
                $this->_task = $task;   //For multicontrollers module version - who know, maybe that's will be?))
                $this->$task();
            }
        }
        public function getView($name = '') {
            if(empty($name)) $name = $this->_defaultView;
            if(!isset($this->_views[$name])) {
                $this->_views[$name] = $this->_createView($name);
            }
            return $this->_views[$name];
        }
        public function getModel($name = '') {
            if(!$name)
                $name = $this->_code;
            if(!isset($this->_models[$name])) {
                $this->_models[$name] = $this->_createModel($name);
            }
            return $this->_models[$name];
        }
        protected function _createModel($name) {
            $code = $this->getCode();
            $parentModule = frame::_()->getModule( $this->_code );
            if(empty($name)) $name = $code;
            
            if(import($parentModule->getModDir(). 'models'. DS. $name. '.php')) {
                $className = $name;
            } elseif(import($parentModule->getModDir(). 'models'. DS. $code. '.php')) {
                $className = $code;
            }
            if($className) {
                $className .= 'Model';
                $model = new $className();
				$model->setCode( $this->getCode() );
                return $model;
            }
            return NULL;
        }
        protected function _createView($name = '') {
            $code = $this->_code;
            if(empty($name)) $name = $this->_defaultView;
            $parentModule = frame::_()->getModule( $this->_code );
            if(import($parentModule->getModDir(). 'views'. DS. $name. '.php')) {
                $className = $name;
            }
            if($className) {
                $className .= 'View';
                $view = new $className();
                $view->init();
                $view->setCode($this->_code);
                return $view;
            }
            return NULL;
        }
        public function display($viewName = '') {
            $view = NULL;
            if(($view = $this->getView($viewName)) === NULL) {
                $view = $this->getView();   //Get default view
            }
            if($view) {
                $view->display();
            }
        }
        public function __call($name, $arguments) {
            $model = $this->getModel();
            if(method_exists($model, $name))
                return $model->$name($arguments[0]);
            else
                return false;
        }
		/**
		 * Retrive permissions for controller methods if exist.
		 * If need - should be redefined in each controller where it required.
		 * @return array with permissions
		 * @example :
		 return array(
				S_METHODS => array(
					'save' => array(S_ADMIN),
					'remove' => array(S_ADMIN),
					'restore' => S_ADMIN,
				),
				S_USERLEVELS => array(
					S_ADMIN => array('save', 'remove', 'restore')
				),
			);
		 * Can be used on of sub-array - S_METHODS or S_USERLEVELS
		 */
		public function getPermissions() {
			return array();
		}
		public function getModule() {
			return frame::_()->getModule( $this->getCode() );
		}
    }
?>