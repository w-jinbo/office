<?php


namespace app\admin\action;


class UserAction extends BaseAction {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->setView('user/index');
    }
}