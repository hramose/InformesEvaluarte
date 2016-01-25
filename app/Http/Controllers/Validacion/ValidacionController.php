<?php namespace GestorImagenes\Http\Controllers\Validacion;

use GestorImagenes\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;

use GestorImagenes\Http\Requests\IniciarSesionRequest;
use GestorImagenes\Http\Requests\RecuperarContrasenaRequest;
use GestorImagenes\Usuario;

class ValidacionController extends Controller 
{
	protected $auth;
	protected $registrar;

	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getSalida']);
	}

	public function getRegistro()
	{
		return view('validacion.registro');
	}

	public function postRegistro(Request $request)
	{
		$validator = $this->registrar->validator($request->all());

		if ($validator->fails())
		{
			$this->throwValidationException(
				$request, $validator
			);
		}

		$this->auth->login($this->registrar->create($request->all()));

		return redirect($this->redirectPath());
	}

	public function getInicio()
	{
		return view('validacion.inicio');
	}

	public function postInicio(IniciarSesionRequest $request)
	{
		$credentials = $request->only('email', 'password');

		if ($this->auth->attempt($credentials, $request->has('remember')))
		{
			return redirect()->intended($this->redirectPath());
		}

		return redirect($this->loginPath())
					->withInput($request->only('email', 'remember'))
					->withErrors([
						'email' => $this->getFailedLoginMessage(),
					]);
	}

	protected function getFailedLoginMessage()
	{
		return 'email o constraseña incorrecto';
	}

	public function getSalida()
	{
		$this->auth->logout();
		return redirect('/');
	}

	public function redirectPath()
	{
		if (property_exists($this, 'redirectPath'))
		{
			return $this->redirectPath;
		}

		return property_exists($this, 'redirectTo') ? $this->redirectTo : '/inicio';
	}

	public function loginPath()
	{
		return property_exists($this, 'loginPath') ? $this->loginPath : '/validacion/inicio';
	}

	public function getRecuperar()
	{
		return view('validacion.recuperar');
	}

	public function postRecuperar(RecuperarContrasenaRequest $request)
	{
		$pregunta = $request->get('pregunta');
		$respuesta = $request->get('respuesta');

		$email = $request->get('email');
		$usuario = Usuario::where('email', '=' , $email)->first();

		if ($pregunta === $usuario->pregunta && $respuesta === $usuario->respuesta) 
		{
			$constrasena = $request->get('password');
			$usuario->password = bcrypt($constrasena);	
			$usuario->save();

			return redirect('/validacion/inicio')->with(['recuperada' => 'La contraseña se cambió, Inciar Sesión']);
		}

		return redirect('/validacion/recuperar')->withInput($request->only('email', 'pregunta'))
		->withErrors(['pregunta' => 'La pregunta y/o respuesta ingresada no coinciden.']);
	}

	public function missingMethod($parameters = array())
	{
		abort(404);
	}
}
