<?php
namespace App\Models\Generated;
use Illuminate\Database\Eloquent\Model;
class Air extends Model
{
    protected $table = 'gen_airs';
    protected $fillable = ['mode', 'agent_name', 'p_n_r_number', 'date_of_booking', 'journey_date', 'air_line', 'ticket_number', 'journey_from', 'journey_upto', 'travel_class', 'location', 'items'];
    protected $casts = [
        'items' => 'array',
    ];
}
