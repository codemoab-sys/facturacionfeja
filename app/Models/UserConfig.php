<?php
namespace App\Models;

use App\Core\Model;

class UserConfig extends Model
{
    protected $table = 'user_configs';

    public function findByUserId($userId)
    {
        return $this->findOneBy('user_id', $userId);
    }
}
