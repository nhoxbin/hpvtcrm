<?php

namespace App\Base\Repositories\Cache;

use App\Base\Repositories\Interfaces\IRepository;
use App\Base\Repositories\Services\Cache\Cache;
use App\Models\MySql\UserAppInfo;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
// use Illuminate\Support\Facades\Redis;
use Psr\SimpleCache\InvalidArgumentException;
use Illuminate\Support\Facades\Cache as LaravelCache;

abstract class CacheAbstractDecorator implements IRepository
{
    protected $repository;

    protected $cache;

    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
        $key = 'admin_user_id:' . auth()->user()->getKey();
        if (LaravelCache::has($key)) {
            $uAppInfo = unserialize(LaravelCache::get($key));
        } else {
            $uAppInfo = UserAppInfo::firstWhere('user_id', auth()->user()->getKey())->toArray();
            LaravelCache::put($key, serialize($uAppInfo), strtotime(config('cache.time')*2 . ' minute', 0));
        }
        $this->cache = new Cache(app('cache'), ($uAppInfo['app_id'] ?? get_class($repository->getModel())));
    }

    public function getCacheInstance()
    {
        return $this->cache;
    }

    public function getRepositoryInstance()
    {
        return $this->repository;
    }

    public function getDataIfExistCache($function, array $args)
    {
        if (!config('cache.enable', false)) {
            return call_user_func_array([$this->getRepositoryInstance(), $function], $args);
        }
        $input = Arr::except(request()->input(), 'page');
        try {
            $cacheKey = md5(
                get_class($this) .
                $function .
                serialize($input) . serialize(url()->current()) .
                serialize(json_encode($args))
            );
            if ($this->cache->has($cacheKey)) {
                return $this->cache->get($cacheKey);
            }

            $cacheData = call_user_func_array([$this->getRepositoryInstance(), $function], $args);

            // Store in cache for next request
            $this->cache->put($cacheKey, $cacheData);

            return $cacheData;
        } catch (Exception | InvalidArgumentException $ex) {
            info($ex->getMessage());
            return call_user_func_array([$this->repository, $function], $args);
        }
    }

    public function getDataWithoutCache($function, array $args)
    {
        return call_user_func_array([$this->repository, $function], $args);
    }

    public function flushCacheAndUpdateData($function, $args, $flushCache = true)
    {
        if ($flushCache) {
            try {
                $this->cache->flush();
            } catch (FileNotFoundException $exception) {
                info($exception->getMessage());
            }
        }

        return call_user_func_array([$this->repository, $function], $args);
    }

    public function getModel()
    {
        return $this->getRepositoryInstance()->getModel();
    }

    public function setModel($model)
    {
        return $this->getRepositoryInstance()->setModel($model);
    }

    public function getTable()
    {
        return $this->getRepositoryInstance()->getTable();
    }

    public function buildQuery()
    {
        return $this->getRepositoryInstance()->buildQuery();
    }

    public function sorts($sorts)
    {
        return $this->getRepositoryInstance()->sorts($sorts);
    }

    public function search($search_params)
    {
        return $this->getRepositoryInstance()->search($search_params);
    }

    public function freeQuery($callback)
    {
        return $this->getRepositoryInstance()->freeQuery($callback);
    }

    public function applyBeforeExecuteQuery($data, $isSingle = false)
    {
        return $this->getRepositoryInstance()->applyBeforeExecuteQuery($data, $isSingle);
    }

    public function make(array $conditions = [], array $with = [])
    {
        return $this->getRepositoryInstance()->make($conditions, $with);
    }

    public function get(int $page, int $limit)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function find($value, array $with = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function findOrFail($id, array $with = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getFirstBy(array $condition = [], array $select = [], array $with = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function pluck($column, $key = null, array $condition = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function all(array $with = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function allBy(array $condition, array $with = [], array $select = ['*'])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function create(array $data)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function createOrUpdate($data, array $condition = [])
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function delete(Model $model)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function firstOrCreate(array $data, array $with = [])
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function update(array $condition, array $data)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function select(array $select = ['*'])
    {
        return $this->getDataWithoutCache(__FUNCTION__, func_get_args());
    }

    public function deleteBy(array $condition = [])
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function count()
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getByWhereIn($column, array $value = [], array $args = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function advancedGet(array $params = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function forceDelete(array $condition = [])
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function restoreBy(array $condition = [])
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function getFirstByWithTrash(array $condition = [], array $select = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function insert(array $data)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function firstOrNew(array $condition)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function defaultSorts($excepts)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function filters(...$params)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
