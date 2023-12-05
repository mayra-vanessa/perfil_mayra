<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UsuariosController extends Controller
{
    public function showRegistroForm()
    {
        return view('auth.registro');
    }

    public function registro(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo'),
            'password' => bcrypt($request->input('password')),
        ]);

        Auth::attempt($request->only('correo', 'password'));

        return redirect()->route('perfil')->with('success', '¡Registro exitoso!');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('correo', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended(route('perfil'));
        }

        return redirect()->route('login')->with('error', 'Credenciales incorrectas. Inténtelo de nuevo.');
    }

    public function showPerfil()
    {
        $usuario = Auth::user();

        if ($usuario->imagen === null) {
            $usuario->imagen = 'perfil.png';
        }

        return view('auth.perfil', compact('usuario'));
    }

    public function updatePerfil(Request $request)
{
    $usuario = Auth::user();

    $request->validate([
        'nombre' => 'required|string|max:255',
        'apellido_paterno' => 'nullable|string|max:255',
        'apellido_materno' => 'nullable|string|max:255',
        'sexo' => 'nullable|in:Masculino,Femenino',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    // Actualizar datos del perfil
    $usuario->nombre = $request->nombre;
    $usuario->apellido_paterno = $request->apellido_paterno;
    $usuario->apellido_materno = $request->apellido_materno;
    $usuario->sexo = $request->sexo;

    // Actualizar la contraseña solo si se proporciona una nueva
    if ($request->filled('password')) {
        $usuario->password = bcrypt($request->password);
    }

    // Procesar la imagen si se proporciona
    if ($request->hasFile('imagen')) {
        $imagen = $request->file('imagen');
        $nombreImagen = time() . '_' . $imagen->getClientOriginalName();

        try {
            // Agregar líneas de registro para depurar
            \Log::info('Intentando mover la imagen: ' . $nombreImagen);

            // Mover la imagen al directorio
            $$imagen->move(base_path('public/imagenes'), $nombreImagen);
            $usuario->imagen = $nombreImagen;

            \Log::info('Imagen movida correctamente.');
        } catch (\Exception $e) {
            // Registro de cualquier excepción que ocurra al mover la imagen
            \Log::error('Error al mover la imagen: ' . $e->getMessage());
        }
    }

    $usuario->save();

    return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
}



    public function logout()
    {
        Auth::logout();
        return redirect()->route('inicio');
    }
}
