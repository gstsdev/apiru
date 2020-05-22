<?php

namespace App\Http\Controllers\Assistencia;

use App\Campus;
use App\Course;
use App\Http\Controllers\Controller;
use App\Itensrepublic;
use App\Republic;
use App\Scheduling;
use App\Shift;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ItemRepublicController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    private $rules = [
        'student_id' => 'required',
        'republic_id' => 'required',
        'responsability' => 'required',
    ];
    private $messages = [
        'student_id.required' => 'O estudante é obrigatório',
        'republic_id.required' => 'A república é obrigatória',
        'responsability.required' => 'Informe se o estudante é o responsável',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($idRepublic)
    {
        $republic = Republic::where('id', $idRepublic)->first();
        if(!$republic){
            return response()->json([
                'message' => 'República não encontrada!'
            ], 404);
        }

        $user = auth()->user();
        if($republic->campus_id != $user->campus_id){
            return response()->json([
                'message' => 'O república pertence a outro campus.'
            ], 202);
        }

        $itens_republic = Itensrepublic::where('republic_id', $republic->id)
            ->orderBy('id')
            ->paginate(10);

        return response()->json($itens_republic);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(),$this->rules,$this->messages);

        if($validation->fails()){
            $erros = array('errors' => array(
                $validation->messages()
            ));
            $json_str = json_encode($erros);
            return response($json_str, 202);
        }

        $republic = Republic::where('id', $request->republic_id)->first();
        if(!$republic){
            return response()->json([
                'message' => 'República não encontrada!'
            ], 404);
        }

        $user = auth()->user();
        if($republic->campus_id != $user->campus_id){
            return response()->json([
                'message' => 'O república pertence a outro campus.'
            ], 202);
        }

        $student = Student::where('id', $request->student_id)->first();
        if(!$student){
            return response()->json([
                'message' => 'Estudante não encontrado!'
            ], 404);
        }

        $verify = Itensrepublic::where('republic_id', $republic->id)
            ->where('student_id', $student->id)
            ->first();
        if($verify){
            return response()->json([
                'message' => 'Estudante já está cadastrado nesta república!'
            ], 404);
        }

        $verify = Itensrepublic::where('student_id', $student->id)->first();
        if($verify){
            return response()->json([
                'message' => 'Estudante já cadastrado na república '.$verify->republic_id.'.'
            ], 404);
        }



        $itens_republic = new Itensrepublic();
        $itens_republic->responsability = $request->responsability;
        $itens_republic->republic_id = $request->republic_id;
        $itens_republic->student_id = $request->student_id;
        $itens_republic->save();

        return response()->json(
            $itens_republic, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(),$this->rules,$this->messages);

        if($validation->fails()){
            $erros = array('errors' => array(
                $validation->messages()
            ));
            $json_str = json_encode($erros);
            return response($json_str, 202);
        }

        $item_republic = Itensrepublic::find($id);

        if(!$item_republic){
            return response()->json([
                'message' => 'Item de república não encontrado!'
            ], 404);
        }

        $republic = Republic::where('id', $request->republic_id)->first();
        if(!$republic){
            return response()->json([
                'message' => 'República não encontrada!'
            ], 404);
        }

        $user = auth()->user();
        if($republic->campus_id != $user->campus_id){
            return response()->json([
                'message' => 'O república pertence a outro campus.'
            ], 202);
        }

        $student = Student::where('id', $request->student_id)->first();
        if(!$student){
            return response()->json([
                'message' => 'Estudante não encontrado!'
            ], 404);
        }
        //poderá alterar apenas a responsabilidade
        $item_republic->responsability = $request->responsability;
        $item_republic->save();

        return response()->json(
            $item_republic, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item_republic = Itensrepublic::find($id);
        if (!$item_republic){
            return response()->json([
                'message' => 'Item de república não encontrado!'
            ], 404);
        }

        $republic = Republic::where('id', $item_republic->republic_id)->first();
        if(!$republic){
            return response()->json([
                'message' => 'República não encontrada!'
            ], 404);
        }

        $user = auth()->user();
        if($republic->campus_id != $user->campus_id){
            return response()->json([
                'message' => 'O república pertence a outro campus.'
            ], 202);
        }

        $item_republic->delete();

        return response()->json([
            'message' => 'Operação realizada com sucesso!'
        ], 200);
    }
}