<?php
namespace App\Services\Params;
use Illuminate\Http\Request;
use App\Models\Setting\Param\Param;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ParamService
{
    public function allParams($validated)
    {
        return Param::select('type','color')
                ->where('parameter_order', $validated['parameterOrder'])->get();
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
        Param::create([
           'type'=>$data['type'],
           'color'=>$data['color']??null,
           'parameter_order'=>$data['parameterOrder']
        ]);
        return "done";
    }
    public function updateParam(int $id,array $data)
    {
        $param = Param::find($id);
        if (!$param) {
            throw new ModelNotFoundException();
        }
        $param->update([
           'type'=>$data['type'],
           'color'=>$data['color']??null,
           'parameter_order'=>$data['parameterOrder']
        ]);
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
