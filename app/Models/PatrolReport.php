<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatrolReport extends Model
{
    protected $fillable = [
  'user_id',
  'report_date',
  'report_day',
  'submitted_time',
  'situasi',
  'aght',
  'cuaca',
  'pdam',
  'personel_wfo',
  'personel_tambahan',
];
}
