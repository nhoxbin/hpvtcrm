<?php

namespace App\Models;

use App\Enums\OneBssSalesStateEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneBssCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'tra_sau', 'core_balance', 'is_request', 'goi_data', 'user_id', 'sales_state', 'sales_note', 'admin_note'];

    protected $casts = [
        'goi_data' => 'json',
        'sales_state' => OneBssSalesStateEnum::class,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function search($goi_data, $expires_in) : Builder {
        return $this->query()
            ->when($goi_data, function($query, $search) {
                foreach ($search as $goi_data) {
                    $query->orWhere(function($q) use ($goi_data) {
                        $q->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(`goi_data`, "$[*].PACKAGE_NAME")) like "%'.$goi_data.'%"');
                        $q->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(`goi_data`, "$[*].SERVICES")) like "%'.$goi_data.'%"');
                    });
                }
            })
            ->when($expires_in, function($query, $search) {
                $query->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(JSON_EXTRACT(`goi_data`, "$[*].TIME_END"), "$[0]")) >= "'.Carbon::now()->format('d/m/Y H:i:s').'"');
                $query->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(JSON_EXTRACT(`goi_data`, "$[*].TIME_END"), "$[0]")) <= "'.Carbon::now()->addDays($search)->format('d/m/Y H:i:s').'"');
            })
            ->with(['user'])
            ->orderBy('id', 'asc');
    }
}
