<?php namespace GestorImagenes\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use GestorImagenes\Http\Requests\CrearSimulacroRequest;
use GestorImagenes\Http\Requests\MostrarSimulacrosRequest;
use GestorImagenes\Simulacro;


class SimulacroController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function getIndex()
	{
		$simulacros = Simulacro::table('simulacros')->select('nombre')->get();

		//$id = $request->get('email');
		//$simulacros = Simulacro::find($id)->simulacros;
		return view('simulacros.mostrar', ['simulacros' => $simulacros]);


	}

	public function getCrearSimulacro()
	{
		return view('simulacros.crear-simulacro');
	}

	public function postCrearSimulacro()
	{

		return view('simulacros.crear-simulacro');
		/*
		$usuario = Auth::user();
		Album::create
		(
			[
				'nombre' => $request->get('nombre'),
				'descripcion' => $request->get('descripcion'),
				'usuario_id' => $usuario->id
			]
		);

		return redirect('/validado/albumes/')->with('creado', 'El álbum ha sido creado');

		*/
	}

	public function getActualizarSimulacro()
	{
		return 'actualizar Album';
	}

	public function postActualizarSimulacro()
	{
		return 'actualizar Album';
	}

	public function getEliminarSimulacro()
	{
		return 'Formulario de eliminar Album';
	}

	public function postEliminarSimulacro()
	{
		return 'eliminar Album';
	}

	public function missingMethod($parameters = array())
	{
		abort(404);
	}
}