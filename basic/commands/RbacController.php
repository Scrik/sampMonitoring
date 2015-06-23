<?php

namespace app\commands;
 
use Yii;
use yii\console\Controller;
use app\components\rbac\GroupRule;
use yii\rbac\DbManager;
 
class RbacController extends Controller {
	
    public function actionInit() {
        $auth = new DbManager;
        $auth->init();
 
        $auth->removeAll();
        $groupRule = new GroupRule();
 
        $auth->add($groupRule);
 
        $user = $auth->createRole('user');
        $user->description = 'User';
        $user->ruleName = $groupRule->name;
        $auth->add($user);
 
		$auth->add($auth->createPermission('admin'));

    }
}