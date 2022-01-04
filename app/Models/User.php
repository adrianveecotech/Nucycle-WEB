<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'role_id'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function hub_admin()
    {
        return $this->hasOne('App\Models\CollectionHubAdmin');
    }

    public function users_roles()
    {
        return $this->hasMany('App\Models\UsersRoles');
    }

    public function users_roles_id()
    {
        $ids = [];
        foreach ($this->users_roles as $value) {
            $ids[] = $value->role_id;
        }
        return $ids;
    }

    public function hub_reader()
    {
        return $this->hasMany('App\Models\CollectionHubReader');
    }
}
