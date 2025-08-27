<?php
namespace App\Services\Settings\Param;
use App\Models\Setting\Param\Param;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class ParamService
{
    public function allParams()
    {
        return Param::select('type','color')->get();
    }
    public function getParamById(int $id)
    {
        $param= Param::select('type','color')->find($id);
        if (!$param){
         throw  new ModelNotFoundException();
        }
        return $param;
    }
    public function createParam(array $data)
    {
        Param::create($data);
        return "done";
    }
    public function updateParam(int $id,array $data)
    {
        $param = Param::find($id);
        if (!$param) {
            throw new ModelNotFoundException();
        }
        $param->update($data);
        return "done";
    }
    public function deleteParam(int $id)
    {
        $param = Param::find($id);
        if (!$param) {
            throw new ModelNotFoundException();
        }
        $param->delete();
        return "done";
    }

}
