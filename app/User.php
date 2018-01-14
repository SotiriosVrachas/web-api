<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function entity()
    {
        return $this->fan ? $this->fan : $this->hasOne(CatalogEntity::class);
    }

    public function catalogable()
    {
        return $this->entity->catalogable;
    }

    public function songs()
    {
        return $this->catalogable() ? $this->catalogable()->songs : $this->purchased;
    }

    public function purchased()
    {
        return $this->belongsToMany(Song::class);
    }

    public function fan()
    {
        return $this->hasOne(Fan::class);
    }
}
