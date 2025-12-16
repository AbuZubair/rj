<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = "user";

    public $primaryKey = "id";

    const CREATED_AT = 'created_date';

    const UPDATED_AT = 'updated_date';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'password',
        'phone_number',
        'email',
        'role',
        'username',    
        'created_date',
        'updated_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'created_date', 'updated_date'
    ];

    public function getId(){
        return $this->id;
    }

    public function getUsername(){
        return $this->username;
    }

    public function getFullname(){
        return $this->first_name. ' ' .$this->last_name;
    }

    public function getRole()
    {
        $role = ['admin','staff','finance','inventory'];
        return $role[$this->role];
    }

    public function getActive()
    {
        return $this->is_active;
    }

    public function look_for_role($check) {
        $role =  explode(",",$this->role);
        foreach ($role as $key => $el) {
          if (in_array($el,$check)) {
            return true;
          }
        }
        return false;
      }

}
