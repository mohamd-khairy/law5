<?php

namespace App\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Str;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|min:6',
        'telephone' => 'nullable|numeric',
    ];

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $guarded = ['id'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'telephone',
        'password',
        'roleId',
        'sectorId',
        'token',
        'resetPasswordCode',
        'resetPasswordCodeCreationdate',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * @var array
     */
    /*protected $dispatchesEvents = [
        'saving' => SaveUserEvent::class,
    ];*/

    /**
     * @return string
     */
    public static function generateApiToken(): string
    {
        $str = date('YmdHis', strtotime('+1 week')) . '.' . Str::random(50);
        for ($i = 0; $i < 5; $i++) {
            $str = base64_encode($str);
        }
        return $str;
    }

    public function isRole()
    {
        return $this->roleId;
    }

    public function roles()
    {
        return $this->belongsTo("App\Model\Role", "roleId");
    }

    public function requests()
    {

        return $this->hasMany("App\Model\RequestModel", "employeeId");
    }

    public function logs()
    {
        return $this->hasMany("App\Model\Log","createdByUserId");
    }
}
