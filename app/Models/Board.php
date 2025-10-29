<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int      $bid
 * @property int      $bid
 * @property int      $parent_id
 * @property int      $status
 * @property int      $parent_id
 * @property string   $content
 * @property string   $multi
 * @property string   $subject
 * @property string   $userid
 * @property string   $content
 * @property string   $multi
 * @property string   $subject
 * @property string   $userid
 * @property DateTime $modifydate
 * @property DateTime $regdate
 * @property DateTime $modifydate
 * @property DateTime $regdate
 * @property boolean  $status
 */
class Board extends Model
{
    use HasFactory;

    protected $table = 'board';
    protected $primaryKey = 'bid';

    protected $fillable = [
        'content', 'multi', 'subject', 'userid', 'cnt', 'status', 'attachfiles', 'memo_cnt', 'memo_date'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bid' => 'int', 'bid' => 'int', 'content' => 'string', 'modifydate' => 'datetime', 'multi' => 'string', 'parent_id' => 'int', 'regdate' => 'datetime', 'status' => 'int', 'subject' => 'string', 'userid' => 'string', 'content' => 'string', 'modifydate' => 'datetime', 'multi' => 'string', 'parent_id' => 'int', 'regdate' => 'datetime', 'status' => 'boolean', 'subject' => 'string', 'userid' => 'string'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'modifydate', 'regdate', 'modifydate', 'regdate'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

    protected $hidden = [
       
    ];
}
