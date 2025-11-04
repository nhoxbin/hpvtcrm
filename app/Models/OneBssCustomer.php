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

    protected $fillable = ['phone', 'tra_sau', 'core_balance', 'is_request', 'goi_cuoc_ts', 'goi_cuoc', 'goi_data', 'goi_ir', 'user_id', 'checked_by_user_id', 'sales_state', 'sales_note', 'admin_note'];

    protected $casts = [
        'goi_cuoc_ts' => 'json',
        'goi_cuoc' => 'json',
        'goi_data' => 'json',
        'goi_ir' => 'json',
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

    public function checked_by()
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }

    public function search(Request $request): Builder
    {
        $goi_data = !empty($request->only('goi_data')) ? $request->only('goi_data')['goi_data'] : null;
        $expires_in = !empty($request->only('expires_in')) ? $request->only('expires_in')['expires_in'] : null;
        $tra_sau = !empty($request->only('tra_sau')) ? $request->only('tra_sau')['tra_sau'] : null;
        $worked_user = !empty($request->only('worked_user')) ? $request->only('worked_user')['worked_user'] : null;
        $checked_by_user = !empty($request->only('checked_by_user')) ? $request->only('checked_by_user')['checked_by_user'] : null;
        $phone = !empty($request->only('phone')) ? $request->only('phone')['phone'] : null;
        $sales_state = !empty($request->only('sales_state')) ? $request->only('sales_state')['sales_state'] : null;
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
            ->when($worked_user, function ($query, $search) {
                $query->where('user_id', $search);
            })
            ->when($checked_by_user, function ($query, $search) {
                $query->where('checked_by_user_id', $search);
            })
            ->when($phone, function ($query, $search) {
                $query->where('phone', $search);
            })
            ->when($sales_state, function ($query, $search) {
                $query->where('sales_state', $search);
            })
            ->with(['user', 'checked_by'])
            ->orderBy('id', 'asc');
    }
}
