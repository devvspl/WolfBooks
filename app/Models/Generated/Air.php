<?php
namespace App\Models\Generated;
use Illuminate\Database\Eloquent\Model;
class Air extends Model
{
    protected $table = 'gen_airs';
    protected $fillable = ['mode', 'agent_name', 'p_n_r_number', 'date_of_booking', 'journey_date', 'air_line', 'ticket_number', 'journey_from', 'journey_upto', 'travel_class', 'location'];

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AirItem::class, 'gen_air_id');
    }
}
