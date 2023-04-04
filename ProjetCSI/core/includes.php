<?php
require ROOT.DS.'config'.DS.'conf.php';
require 'Session.php';
require 'Form.php';
require 'Router.php';

//on définie les différents prefixes utilisables pour devenir admin, ici il n'y a que admin
Router::prefix('admin', 'admin');

require 'Request.php';
require 'Controller.php';
require 'Dispatcher.php';
require 'Model.php';


