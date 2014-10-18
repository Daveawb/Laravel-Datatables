<?php

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model {
	
	public $table = "users";

    public function content()
    {
        return $this->hasMany('ContentModel', 'user_id');
    }
	
}
