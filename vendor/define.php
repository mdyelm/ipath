<?php
require_once 'setting.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Cake\Core\Configure;
// Configure fix id language default (disable function edit and delete id)
Configure::write('id_language_fix',array(1,4));
// Configure fix language app default
Configure::write('language_default_app',"Japanese");

// Configure define info excel file
Configure::write('info', array(
                'creator' => 'nhoccon',
                'modified' => 'nhoccon',
                'title' => 'ipath route',
                'subject' => 'ipath route content',
                'description' => 'ipath description',
                'keyword' => 'ipath route',
                'category' => 'path route map'    
));

Configure::write('User', array(
    'normal_user' => 0,
    'administrator' => 1,
    'department1' => 2,
    'department2' => 3,
    'department3' => 4,
    'department4' => 5,
));

Configure::write('Permission', array(
    0   => array(0),
    1   => array(),
    2   => array(2),
    3   => array(3),
    4   => array(4),
    5   => array(5),
));

Configure::write('Version', array(
    'web' => 0,
    'iOS' => 1,
    'android' => 2,
    'app_language' => 3,
));
Configure::write('Version2', array(
    array('value' => '0', 'text' => 'Show'),
    array('value' => '1', 'text' => 'Hidden'),
));

Configure::write('Shell.SendMailAfterCompleteSurvey', '/usr/bin/php ' . APP . '..' . DS . 'bin' . DS . 'cake.php SendMailAfterCompleteSurvey');
Configure::write('TitleSendMailAfterCompleteSurvey', 'Have a new survey!');
Configure::write('TemplateSendMailAfterCompleteSurvey', 'complete_survey');

// Configure domain name server by web_id
Configure::write('DomainByWebId',array(
    0 => 'http://ec2-54-249-51-254.ap-northeast-1.compute.amazonaws.com',
    1 => 'http://ec2-54-250-253-85.ap-northeast-1.compute.amazonaws.com',
    2 => 'http://ec2-54-250-166-181.ap-northeast-1.compute.amazonaws.com',
    3 => 'http://ec2-54-238-159-36.ap-northeast-1.compute.amazonaws.com',
    4 => 'http://ec2-54-250-241-161.ap-northeast-1.compute.amazonaws.com',
    5 => 'http://ec2-54-250-239-191.ap-northeast-1.compute.amazonaws.com'
));