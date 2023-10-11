<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirewallRulesModel extends Model
{
    public $table = "firewall_rules";
    // use HasFactory;
    protected $guarded = [];
}
