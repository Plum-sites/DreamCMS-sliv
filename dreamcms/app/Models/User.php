<?php

namespace App\Models;

use App\Events\FriendshipEvent;
use App\Notifications\FriendRequest;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use GuzzleHttp\Client;
use Hootlex\Friendships\Models\Friendship;
use Hootlex\Friendships\Status;
use Hootlex\Friendships\Traits\Friendable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Yadahan\AuthenticationLog\AuthenticationLogable;

class User extends Authenticatable implements BannableContract, Auditable, JWTSubject, MustVerifyEmail, CanResetPassword
{
    use Friendable;
    use Bannable;
    use Notifiable;
    use CrudTrait;
    use HasRoles;
    use \OwenIt\Auditing\Auditable;
    use AuthenticationLogable;
    use \Illuminate\Auth\Passwords\CanResetPassword;

    public $timestamps = false;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'email', 'password', 'uuid', 'reg_time', 'reg_ip', 'realmoney', 'money',
        'vk_id', 'vk_data', 'otp_secret', 'last_play', 'email_checked',
        'checked', 'refer', 'reputation', 'sign', 'prefix', 'token_reset', 'backup_codes', 'telegram_id', 'telegram_data', 'email_confirmed_at', 'passchange_time', 'accessToken', 'remember_token'
    ];

    protected $casts = [
        'telegram_data' => 'array'
    ];

    protected $auditExclude = [
        'password',
        'remember_token',
        'vk_data',
        'reputation'
    ];

    protected $auditEvents = [
        'updated',
        'deleted',
        'restored',
    ];

    // EMAIL VERIFICATION

    public function hasVerifiedEmail(){
        return $this->email_confirmed_at && $this->email_confirmed_at > 0;
    }

    public function markEmailAsVerified(){
        $this->email_confirmed_at = Carbon::now()->getTimestamp();
        $this->save();
    }

    public function sendEmailVerificationNotification(){
        //\Mail::to($this)->send(new EmailConfirm($this, $this->email, $token));
    }

    public function getEmailForVerification(){
        return $this->email;
    }

    public function getJWTIdentifier()
    {
        return $this->getAuthIdentifier();
    }
    
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'login'=> $this->login,
            'uuid' => $this->uuid,
            'reputation' => $this->reputation,
            'role' => $this->getSiteRole(),
            'moder' => $this->hasAnyRole(['admin', 'curator', 'forummoder', 'coder']),
            'reg_time' => $this->reg_time,
        ];
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'vk_data', 'otp_secret', 'api_token', 'accessToken', 'serverID', 'access_token', 'server_id', 'backup_codes'
    ];

    public function integrations(){
        return $this->hasMany(Integration::class, 'user_id', 'id');
    }

    public function routeNotificationForTelegram()
    {
        $integration = $this->integrations()->where('driver', '=', 'telegram')->first();
        return $integration ? $integration->ext_id : null;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Http\Requests\ResetPasswordNotification($token));
    }

    public function getEmailForPasswordReset()
    {
        return trim($this->email);
    }

    public function notifyAuthenticationLogVia()
    {
        return ['mail'];
    }

    public function clearCache(){
        \Cache::delete('core_user_' . $this->id);
        \Cache::delete('specialoffers_' . $this->id);
    }

    public function clearCoreCache(){
        try {
            \Async::run(function (){
                $client = new Client();
                $client->request('GET', config('app.core_url') . '/user/' . $this->uuid . '/cache/clear');
            }, []);
        }catch (\Throwable $throwable){
            app('sentry')->captureException($throwable);
        }
    }

    /**
     * @return User
     */
    public static function fromUUID($uuid){
        return User::where('uuid', $uuid)->first();
    }

    public function getRefer(){
        return User::find($this->refer);
    }

    /**
     * @return User
     */
    public static function fromLogin($login){
        return User::where('login', $login)->first();
    }

    /**
     * @return User
     */
    public static function fromEmail($login){
        return User::where('email', $login)->first();
    }

    public function withdrawRealmoney($count){
        User::disableAuditing();
        if($this->realmoney >= $count){
            $this->realmoney = round($this->realmoney - $count, 2);
            $saved = $this->save();
            User::enableAuditing();

            return $saved;
        }
        User::enableAuditing();
        return false;
    }

    public function addRealmoney($count){
        User::disableAuditing();
        $this->realmoney = round($this->realmoney + $count, 2);
        $saved = $this->save();
        User::enableAuditing();

        return $saved;
    }

    public function withdrawMoney($count){
        User::disableAuditing();
        if($this->money >= $count){
            $this->money = round($this->money - $count, 2);
            $saved = $this->save();
            User::enableAuditing();

            return $saved;
        }
        User::enableAuditing();
        return false;
    }

    public function addMoney($count){
        User::disableAuditing();
        $this->money = round($this->money + $count, 2);
        $saved = $this->save();
        User::enableAuditing();

        return $saved;
    }

    public function isServerBanned(){
        return $this->getBans()->count() > 0;
    }

    public function getServerBans(){
        //TODO значениие в бд
        $additional = 50 * Activity::user_logs($this, 1, false, ['action' => 'buy_unban'])->count();
    
        $reasons = \DB::table('ban_reason')->get();
        $bans = \DB::table('ban_list')->where('UUID', '=', $this->uuid)->get()->map(function ($item, $key) use ($reasons, $additional){
            $tmp = $reasons->where('reason', trim($item->Reason))->first();
            $item->price = $tmp ? ($tmp->price + $additional) : false;

            $admin = User::fromUUID($item->UUIDadmin);
            $item->admin = $admin ? $admin->only(['id', 'login']) : ['id' => 0, 'login' => 'Server'];
            
            return $item;
        });
        return $bans;
    }

    public function getSiteRole(){
        if ($this->prefix){
            return $this->prefix;
        }
        
        $allroles = $this->getAllRoles();
        
        if ($allroles->contains('name', 'admin')) return '<span style="color: #fa6868;">Администратор</span>';
        if ($allroles->contains('name', 'curator')) return '<span style="color: #fa6868;">Куратор</span>';
        if ($allroles->contains('name', 'coder')) return '<span style="color: #fa6868;">Программист</span>';
        if ($allroles->contains('name', 'grandmoder')) return '<span style="color: #6877fa;">Гранд-модер</span>';
        if ($allroles->contains('name', 'moder')) return '<span style="color: #28a745;">Модератор</span>';
        if ($allroles->contains('name', 'forummoder')) return '<span style="color: #fa68d9;">Форумный модератор</span>';
    
        if ($allroles->contains('name', 'youtube')) return '<span style="color: #ff0011 !important; font-weight: bold;">You<span style="color: black; font-weight: bold;">Tube</span></span>';
    
        if ($allroles->contains('name', 'legend')) return '<span style="color: #faa568; font-weight: bold;">Legend</span>';
        if ($allroles->contains('name', 'deluxe')) return '<span style="color: #fa68d9;">Deluxe</span>';
        if ($allroles->contains('name', 'premium')) return '<span style="color: purple;">Premium</span>';
        if ($allroles->contains('name', 'vip')) return '<span style="color: #009fc3;">VIP</span>';
        
        return '<span>Игрок</span>';
    }

    public function getDonateRole(){
        $allroles = $this->getAllRoles();

        if ($allroles->contains('name', 'legend')) return '<span style="color: #faa568; font-weight: bold;">Legend</span>';
        if ($allroles->contains('name', 'deluxe')) return '<span style="color: #fa68d9;">Deluxe</span>';
        if ($allroles->contains('name', 'premium')) return '<span style="color: purple;">Premium</span>';
        if ($allroles->contains('name', 'vip')) return '<span style="color: #009fc3;">VIP</span>';

        return '<span>Игрок</span>';
    }

    public function isModer(){
        return $this->hasAnyRole(['admin', 'curator', 'forummoder', 'coder', 'moder', 'grandmoder']);
    }

    public function isSuperAdmin(){
        return /*$this->hasRole('admin') && */ $this->id == 1;
    }

    public function isStrictChecking(){
        if ($this->isModer()) return false;
        return $this->reputation < 0 || ((time() - $this->reg_time) < (7 * 24 * 60 * 60));
    }

    public function getReputation(){
        if ($this->reputation > 10) return '<span style="color: #6ba329;">' . $this->reputation .'</span>';
        if ($this->reputation < -10) return '<span style="color: #ab2027;">' . $this->reputation .'</span>';

        return '<span>' . $this->reputation . '</span>';
    }

    public function isOnline(){
        return array_key_exists($this->login, \Illuminate\Support\Facades\Cache::get('forum_online', []));
    }
    
    public function getAllRoles(){
        $donate_roles = collect();
    
        $donate_roles->push($this->getStoredRole('member'));
        $this->getActiveGroups()->each(function ($item, $key) use (&$donate_roles){
            try{
                $role = $this->getStoredRole($item->getDonateGroup()->pexname);
                if($role) $donate_roles->push($role);
            }catch (RoleDoesNotExist $e){}
        });

        $allroles = $this->roles()->get()->union($donate_roles)->unique('id');
        
        return $allroles;
    }

    public function getPermissionsViaRoles(): Collection
    {
        return $this->getAllRoles()->flatMap(function ($role) {
            return $role->permissions;
        })->unique('id')->sort()->values();
    }

    /**
     * Determine if the user has (one of) the given role(s).
     *
     * @param string|array|Role|\Illuminate\Support\Collection $roles
     *
     * @return bool
     */
    public function hasRole($roles)
    {
        $allroles = $this->getAllRoles();

        if (is_string($roles)) {
            return $allroles->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $allroles->contains('id', $roles->id);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return (bool) $roles->intersect($allroles)->count();
    }

    /**
     * Return the collection of active donate groups.
     *
     * @return Collection
     */

    public function getActiveGroups($server = false){
        if($server){
            return UserGroup::where([
                ['user_id', '=', $this->id],
                ['time', '<', time()],
                ['expire', '>', time()],
                ['server_id', '=', $server]
            ])->get();
        }else{
            return UserGroup::where([
                ['user_id', '=', $this->id],
                ['time', '<', time()],
                ['expire', '>', time()]
            ])->get();
        }
    }

    /**
     * Return the collection of active donate groups.
     *
     * @return Collection
     */

    public function getAllGroups($server = false){
        if($server){
            return UserGroup::where([
                ['user_id', '=', $this->id],
                ['server_id', '=', $server]
            ])->get();
        }else{
            return UserGroup::where([
                ['user_id', '=', $this->id]
            ])->get();
        }
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return DonateGroup
     */

    public function getGroup($server = false){
        $groups = $this->getActiveGroups($server);
        $groups = $groups->map(function ($item, $key) {
            return $item->getDonateGroup();
        });
        return $groups->sortByDesc('sort')->first();
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param string|Permission $permission
     *
     * @return bool
     */
    /*public function hasPermissionTo($permission)
    {
        return true;
    }*/


    // FIX

    public function befriend(Model $recipient)
    {
        if (!$this->canBefriend($recipient)) {
            return false;
        }

        $friendship = (new Friendship)->fillRecipient($recipient)->fill([
            'status' => Status::PENDING,
        ]);


        $this->friends()->save($friendship);

        $recipient->notify(new FriendRequest($friendship, 'befriend'));

        event(new FriendshipEvent($friendship));

        return $friendship;
    }

    public function unfriend(Model $recipient)
    {
        $friendship = $this->findFriendship($recipient)->first();

        $recipient->notify(new FriendRequest($friendship, 'unfriend'));

        return $friendship->delete();
    }

    public function acceptFriendRequest(Model $recipient)
    {
        $friendship = $this->findFriendship($recipient)->whereRecipient($this)->first();
        $friendship->status = Status::ACCEPTED;

        $recipient->notify(new FriendRequest($friendship, 'accept'));

        event(new FriendshipEvent($friendship));

        return $friendship->save();
    }

    public function denyFriendRequest(Model $recipient)
    {
        $friendship = $this->findFriendship($recipient)->whereRecipient($this)->first();
        $friendship->status = Status::DENIED;

        $recipient->notify(new FriendRequest($friendship, 'deny'));

        event(new FriendshipEvent($friendship));

        return $friendship->save();
    }

    public function blockFriend(Model $recipient)
    {
        // if there is a friendship between the two users and the sender is not blocked
        // by the recipient user then delete the friendship
        if (!$this->isBlockedBy($recipient)) {
            $this->findFriendship($recipient)->delete();
        }

        $friendship = (new Friendship)->fillRecipient($recipient)->fill([
            'status' => Status::BLOCKED,
        ]);

        $this->friends()->save($friendship);

        event(new FriendshipEvent($friendship));

        return $friendship;
    }

    public function unblockFriend(Model $recipient)
    {
        $deleted = $this->findFriendship($recipient)->whereSender($this)->delete();

        return $deleted;
    }
}
