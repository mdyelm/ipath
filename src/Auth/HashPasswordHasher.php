<?php
namespace App\Auth;

use Cake\Auth\AbstractPasswordHasher;
use Cake\Core\Configure;

class HashPasswordHasher extends AbstractPasswordHasher
{

	public function hash($password)
	{
		return md5(Configure::read('Security.salt').$password);
	}

	public function check($password, $hashedPassword)
	{
		return md5(Configure::read('Security.salt').$password) === $hashedPassword;
	}
}
?>