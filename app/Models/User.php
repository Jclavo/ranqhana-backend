<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    // use SoftDeletes;

    //Set db connection
    protected $connection = 'mysql_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'name', 'email','identification', 'password', 'store_id', 'api_token'
        'login', 'company_project_id', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate($date)
    {
        $carbonInstance = \Carbon\Carbon::instance($date);

        return $carbonInstance->toISOString();
    }


    /**
     * Get the company_project that owns the user.
     */
    public function company_project(){
        return User::select('company_project.*')
                     ->join('company_project','users.company_project_id','=','company_project.id')
                     ->where('users.company_project_id','=',$this->company_project_id)
                     ->get();
    }

    /**
     * Get the company thay owns the user.
     */
    public function company(){
        return User::select('companies.*')
                    ->join('company_project', function ($join){
                        $join->on('users.company_project_id','=','company_project.id')
                              ->where('users.company_project_id','=',$this->company_project_id);
                    })
                    ->join('companies','company_project.company_id','=','companies.id')
                    ->get();
    }


    /**
     * Get the project thay owns the user.
     */
    public function project(){
        return User::select('projects.*')
                    ->join('company_project', function ($join){
                        $join->on('users.company_project_id','=','company_project.id')
                              ->where('users.company_project_id','=',$this->company_project_id);
                    })
                    ->join('projects','company_project.project_id','=','projects.id')
                    ->get();
    }
}
