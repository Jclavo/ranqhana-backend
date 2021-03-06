<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\RanqhanaUser;

class User extends Authenticatable
{
    use Notifiable;
    // use SoftDeletes;

    //Set db connection
    protected $connection = 'taapaq_DB';

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
    // protected function serializeDate($date)
    // {
    //     $carbonInstance = \Carbon\Carbon::instance($date);

    //     return $carbonInstance->toISOString();
    // }

    /**
     * Get the company_project that owns the user.
     */
    // public function company_project(){
    //     return User::select('company_project.*')
    //                  ->join('company_project','users.company_project_id','=','company_project.id')
    //                  ->where('users.company_project_id','=',$this->company_project_id)
    //                  ->distinct()
    //                  ->first();
    // }

    /**
     * Get the company thay owns the user.
     */
    // public function company(){
    //     return User::select('companies.*')
    //                 ->join('company_project', function ($join){
    //                     $join->on('users.company_project_id','=','company_project.id')
    //                           ->where('users.company_project_id','=',$this->company_project_id);
    //                 })
    //                 ->join('companies','company_project.company_id','=','companies.id')
    //                 ->distinct()
    //                 ->first();
    // }


    /**
     * Get the project thay owns the user.
     */
    // public function project(){
    //     return User::select('projects.*')
    //                 ->join('company_project', function ($join){
    //                     $join->on('users.company_project_id','=','company_project.id')
    //                           ->where('users.company_project_id','=',$this->company_project_id);
    //                 })
    //                 ->join('projects','company_project.project_id','=','projects.id')
    //                 ->distinct()
    //                 ->first();
    // }

    /**
     * Get ID from Local User table
     */
    // public function getLocalUserID(){
    //     $RanqhanaUserResult = RanqhanaUser::
    //                             where('external_user_id','=',$this->id)
    //                             ->where('company_project_id','=',$this->company_project_id)
    //                             ->pluck('id');

    //     if($RanqhanaUserResult == null) return null;
    //     return $RanqhanaUserResult[0];
    // }

    /**
     * Get country's timezone
     */
    public function getTimezone(){
        $country = $this->country();
        return $country->timezone;
    }

    /**
     * Get country's tax
     */
    public function getCountryTax(){
        $country = $this->country();
        return $country->tax;
    }

    /**
     * Get the country thay owns the company from user.
     */
    public function country(){
        return User::select('countries.*')
                    ->join('company_project', function ($join){
                        $join->on('users.company_project_id','=','company_project.id')
                              ->where('users.company_project_id','=',$this->company_project_id);
                    })
                    ->join('companies','company_project.company_id','=','companies.id')
                    ->join('universal_people','companies.universal_person_id','=','universal_people.id')
                    ->join('countries','universal_people.country_code','=','countries.code')
                    ->distinct()
                    ->first();
    }

    public function isAdminByToken($token){

        $isAdmin = User::join('model_has_roles','users.id','=','model_has_roles.model_id')
                    ->join('roles','model_has_roles.role_id','=','roles.id')  
                    ->where('users.api_token','=',$token)
                    ->where('roles.name','like','%ADMIN%')
                    ->count();

        if ($isAdmin > 0) {
            return 1;
        }
        return 0;
    }

}
