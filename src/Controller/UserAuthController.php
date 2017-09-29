<?php

namespace App\Controller;

use App\Controller\UserBaseController;
use Cake\Event\Event;

class UserAuthController extends UserBaseController {

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow(['add', 'logout']);
    }

    public function login() {
        $this->loadModel('AppLanguages');

        $this->viewBuilder()->layout('login');

        if ($this->Auth->user()) {
            return $this->redirect(array('controller' => 'Surveys', 'action' => 'index'));
        }
        $cookieData = $this->Cookie->read('Auth.User');
        if ($cookieData) {
            $usernameDefault = $cookieData['username'];
            $passwordDefault = $cookieData['password'];
        } else {
            $usernameDefault = '';
            $passwordDefault = '';
        }
        if ($this->request->is('post')) {
                  
            $data = $this->request->data;
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                if ($data['remember_me']) {
                    $cookie = array();
                    $cookie['username'] = $data['username'];
                    $cookie['password'] = $data['password'];
                    $this->Cookie->write('Auth.User', $cookie);
                } else {
                    $this->Cookie->delete('Auth.User');
                }
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $usernameDefault = $data['username'];
                $passwordDefault = $data['password'];
                $this->Flash->error(__('The user name or password is incorrect. Please check your user name or password and login again.'));
            }
        }
        $this->set(compact('usernameDefault', 'passwordDefault'));
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

}
