<?php

namespace App\Models;

use App\Enums\OneBssSalesStateEnum;
use App\Enums\SalesStateEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class OneBssCustomer extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['phone', 'tra_sau', 'core_balance', 'is_request', 'goi_cuoc_ts', 'goi_cuoc', 'goi_data', 'user_id', 'sales_state', 'sales_note', 'admin_note'];

    protected $casts = [
        'goi_data' => 'json',
        'sales_state' => OneBssSalesStateEnum::class,
    ];

    protected $appends = [
        'state'
    ];

    protected function state(): Attribute
    {
        return Attribute::get(fn() => !empty($this->attributes['sales_state']) && SalesStateEnum::tryFrom($this->attributes['sales_state']) ? __('sales_state.' . $this->attributes['sales_state']) : null);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function search(Request $request): Builder
    {
        $goi_data = !empty($request->only('goi_data')) ? $request->only('goi_data')['goi_data'] : null;
        $expires_in = !empty($request->only('expires_in')) ? $request->only('expires_in')['expires_in'] : null;
        $tra_sau = !empty($request->only('tra_sau')) ? $request->only('tra_sau')['tra_sau'] : null;
        return $this->query()
            ->when($goi_data, function ($query, $search) {
                foreach ($search as $goi_data) {
                    $query->orWhere(function ($q) use ($goi_data) {
                        $operator = 'like "' . $goi_data . '"';
                        $q->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(JSON_EXTRACT(`goi_data`, "$[*].SERVICES"), "$[0]")) ' . $operator);
                    });
                }
            })
            ->when($expires_in, function ($query, $search) {
                $query->whereRaw('str_to_date(JSON_UNQUOTE(JSON_EXTRACT(JSON_EXTRACT(`goi_data`, "$[*].TIME_END"), "$[0]")), "%d/%m/%Y %H:%i:%s") >= "' . Carbon::now() . '"');
                $query->whereRaw('str_to_date(JSON_UNQUOTE(JSON_EXTRACT(JSON_EXTRACT(`goi_data`, "$[*].TIME_END"), "$[0]")), "%d/%m/%Y %H:%i:%s") <= "' . Carbon::now()->addDays($search) . '"');
            })
            ->when($tra_sau, function ($query, $search) {
                if (count($search) == 1) {
                    $query->where('tra_sau', $search);
                }
            })
            ->with(['user'])
            ->orderBy('id', 'asc');
    }
}
