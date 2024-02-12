<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use App\Passwords;
use DB;

class User extends Authenticatable
{
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $primaryKey = 'user_id';

    protected $fillable = ['firstname', 'middlename', 'lastname', 'extname', 'username', 'email', 'secondaryemail', 'birthday', 'sex', 'country', 'region', 'province', 'municipality', 'barangay', 'philrice_idno', 'designation', 'fullname', 'isactive', 'isapproved', 'cooperative', 'agency', 'school', 'contact_no', 'accreditation_no', 'age'];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'password'
    ];

    public function passwords() {
        return $this->hasOne('App\Passwords', 'user_id');
    }

    public function getPasswordAttribute() {
        return $this->passwords->getAttribute('password');
    }

    public function isAdmin()
    {
        return $this->admin ? true : false;
    }

    // Get all users
    function getUsers() {
        $users = DB::table('users')
        ->rightJoin('passwords', 'passwords.user_id', '=', 'users.user_id') // show only users with passwords
        ->select('users.*')
        ->where('users.isdeleted', '=', 0)
        ->where('users.isapproved', '=', null)
        ->orWhere('users.isapproved', '=', 1)
        ->get();

        return $users;
    }

    /*function addUser($input) {
        \DB::beginTransaction();
        try {
            // Insert user's profile
            $userid = \DB::table('users')
            ->insertGetId([
                'firstname' => $input['firstname'],
                'middlename' => $input['middlename'],
                'lastname' => $input['lastname'],
                'extname' => $input['extname'],
                'username' => $input['username'],
                'password' => $input['hashed_password'],
                'email' => $input['email'],
                'secondaryemail' => $input['secondaryemail'],
                'sex' => $input['sex'],
                'stationid' => $input['stationid'],
                'isdeleted' => 0,
                'fullname' => $input['fullname'],
                'philrice_idno' => $input['philrice_idno']
            ]);

            // Insert user's roles
            foreach ($input['roles'] as $key => $value) {
                \DB::table('role_user')
                ->insert([
                    'user_id' => $userid,
                    'role_id' => $value
                ]);
            }

            \DB::commit();
            return "success";
        } catch (\Exception $e) {
            \DB::rollback();
            return $e->getMessage();
        }
    }*/

    // Get user's profile
    function getUser($userid) {
        $user = DB::table('users')
        ->select('*')
        ->where('users.user_id', $userid)
        ->first();

        return $user;
    }

    // Update user's profile
    /*function updateUser($userid, $input) {
        \DB::beginTransaction();
        try {
            // Update user's profile
            \DB::table('users')
            ->where('id', $userid)
            ->update([
                'firstname' => $input['firstname'],
                'middlename' => $input['middlename'],
                'lastname' => $input['lastname'],
                'extname' => $input['extname'],
                'username' => $input['username'],
                'email' => $input['email'],
                'secondaryemail' => $input['secondaryemail'],
                'sex' => $input['sex'],
                'stationid' => $input['stationid'],
                'isdeleted' => 0,
                'fullname' => $input['fullname'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Delete user's roles
            \DB::table('role_user')->where('user_id', $userid)->delete();

            // Insert user's roles
            foreach ($input['roles'] as $key => $value) {
                \DB::table('role_user')
                ->insert([
                    'user_id' => $userid,
                    'role_id' => $value
                ]);
            }

            \DB::commit();
            return "success";
        } catch (\Exception $e) {
            \DB::rollback();
            return $e->getMessage();
        }
    }*/

    // Delete user
    function deleteUser($user_id, $log) {
        DB::beginTransaction();
        try {
            DB::table('users')
            ->where('user_id', $user_id)
            ->update([
                'isdeleted' => 1
            ]);

            // Add log
            $this->add_log($log);

            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function get_region_code($region_id) {
        $region = DB::connection('seed_grow')
        ->table('regions')
        ->select('reg_code')
        ->where('region_id', $region_id)
        ->first();

        return $region->reg_code;
    }

    public function get_municipalities($province_id) {
        $municipalities = DB::connection('seed_grow')
        ->table('municipalities')
        ->select('name', 'mun_code')
        ->where('province_id', $province_id)
        ->orderBy('name', 'asc')
        ->get();

        return $municipalities;
    }

    public function add_log($log) {
        DB::table('activities_user')->insert($log);
    }

    public function get_province_id($province_code) {
        $province = DB::connection('seed_grow')
        ->table('provinces')
        ->select('province_id')
        ->where('prov_code', $province_code)
        ->first();

        return $province;
    }

    public function get_user_affiliation($user_id) {
        $user_affiliation = DB::table('affiliation_user')
        ->leftJoin('affiliations', 'affiliations.affiliation_id', '=', 'affiliation_user.affiliation_id')
        ->leftJoin('philrice_station', 'philrice_station.philrice_station_id', '=', 'affiliation_user.affiliated_to')
        ->select('affiliations.affiliation_id', 'affiliations.name as affiliation_name', 'philrice_station.philrice_station_id', 'philrice_station.name as station_name')
        ->where('affiliation_user.user_id', $user_id)
        ->first();

        return $user_affiliation;
    }

    public function get_user_region($reg_code) {
        $region = DB::connection('seed_grow')
        ->table('regions')
        ->select('name')
        ->where('reg_code', $reg_code)
        ->first();

        return $region->name;
    }

    public function get_user_province($prov_code) {
        $province = DB::connection('seed_grow')
        ->table('provinces')
        ->select('name')
        ->where('prov_code', $prov_code)
        ->first();

        return $province->name;
    }

    public function get_user_municipality($mun_code) {
        $mun = DB::connection('seed_grow')
        ->table('municipalities')
        ->select('name')
        ->where('mun_code', $mun_code)
        ->first();

        return $mun->name;
    }

    public function get_date_created($user_id) {
        $date_created = DB::table('activities_user')
        ->select('timestamp')
        ->where('user_id', $user_id)
        ->where('activity_id', 1)
        ->first();

        return $date_created;
    }

    public function get_date_updated($user_id) {
        $date_updated = DB::table('activities_user')
        ->select('timestamp')
        ->where('user_id', $user_id)
        ->where('activity_id', 4)
        ->orderBy('timestamp', 'DESC')
        ->first();

        return $date_updated;
    }

    public function get_logged_in_status($user_id) {
        $logged_in = DB::table('sessions')
        ->where('user_id', $user_id)
        ->count();

        return $logged_in;
    }

    public function delete_session($user_id, $log) {
        DB::beginTransaction();
        try {
            DB::table('sessions')
            ->where('user_id', $user_id)
            ->delete();

            // Add log
            $this->add_log($log);

            DB::commit();
            return "success";
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

}
