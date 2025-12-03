<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name'];

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function creditCards()
    {
        return $this->hasMany(CreditCard::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }
}
