<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
            'correo' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $response = Http::post('https://www.proyectowebuni.com/pasteleria_files/perfil_mayra/api.php', [
            't_o' => 1,
            'nombre' => $request->input('nombre'),
            'correo' => $request->input('correo'),
            'password' => $request->input('password'),
        ]);

        $data = $response->json();
        Log::info('Respuesta de la API en registro:', ['data' => $data]);

        if (isset($data['respuesta']) && $data['respuesta'] === 'ok' && isset($data['usuario'])) {
            // Guardar toda la información del usuario en el almacenamiento local
            Session::put('usuario', $data['usuario']);
            return redirect()->route('perfil')->with('success', '¡Registro exitoso!');
        } else {
            $errorMessage = isset($data['message']) ? $data['message'] : 'Error desconocido en la respuesta de la API.';
            return redirect()->route('registro')->with('error', $errorMessage);
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required',
        ]);

        $response = Http::post('https://www.proyectowebuni.com/pasteleria_files/perfil_mayra/api.php', [
            't_o' => 2,
            'correo' => $request->input('correo'),
            'password' => $request->input('password'),
        ]);

        $data = $response->json();

        Log::info('Respuesta de la API en login:', ['data' => $data]);

        if (isset($data['respuesta']) && $data['respuesta'] === 'ok' && isset($data['usuario']['id'])) {
            // Guardar toda la información del usuario en el almacenamiento local
            Session::put('usuario', $data['usuario']);
            return redirect()->route('perfil');
        } else {
            return redirect()->route('login')->with('error', 'Credenciales incorrectas. Inténtelo de nuevo.');
        }
    }

    public function showPerfil()
    {
        // Obtener toda la información del usuario desde el almacenamiento local
        $usuario = Session::get('usuario');

        return view('auth.perfil', compact('usuario'));
    }

    public function updatePerfil(Request $request)
    {
        // Obtener toda la información del usuario desde el almacenamiento local
        $usuario = Session::get('usuario');
    
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'nullable|string|max:255',
            'apellido_materno' => 'nullable|string|max:255',
            'sexo' => 'nullable|in:Masculino,Femenino',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
    
        $data = [
            't_o' => 3,
            'id' => $usuario['id'],
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'sexo' => $request->sexo,
            'password' => $request->filled('password') ? $request->password : null,
        ];
    
        $response = Http::post('https://www.proyectowebuni.com/pasteleria_files/perfil_mayra/api.php', $data);
    
        $responseData = $response->json();
        Log::info('Respuesta de la API en actualizar perfil:', ['data' => $responseData]);
    
        if ($responseData['respuesta'] === 'ok') {
            // Actualizar la información del usuario en la sesión local
            $usuario['nombre'] = $responseData['usuario']['nombre'];
            $usuario['apellido_paterno'] = $responseData['usuario']['apellido_paterno'];
            $usuario['apellido_materno'] = $responseData['usuario']['apellido_materno'];
            $usuario['sexo'] = $responseData['usuario']['sexo'];
    
            // Actualizar la información del usuario en la sesión de Laravel
            Session::put('usuario', $usuario);
    
            return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente.');
        } else {
            return redirect()->route('perfil')->with('error', $responseData['message']);
        }
    }
    

    public function logout()
    {
        // Eliminar toda la información del usuario del almacenamiento local
        Session::forget('usuario');
        return redirect()->route('inicio');
    }
}
