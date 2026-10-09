<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Event extends Model
{
    use HasFactory;

    protected $table = 'events_table_teacher';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'color',
        'start',
        'end',
    ];

    /**
     * Relasi ke Guru / Pengguna pembuat kegiatan
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}