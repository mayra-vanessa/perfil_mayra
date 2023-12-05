<!-- resources/views/auth/perfil.blade.php -->

@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                @if(session('success'))
                    <div id="success-alert" class="alert alert-success">
                        <i class="fas fa-check"></i>
                        {{ session('success') }}
                    </div>
                    <script>
                        // Ocultar automáticamente la alerta después de 5 segundos
                        setTimeout(function() {
                            $('#success-alert').fadeOut('slow');
                        }, 5000);
                    </script>
                @endif

                <div class="card-header">{{ __('Mi Perfil') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('perfil.actualizar') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Información de la Cuenta -->
                        <div class="mb-3">
                            <h4>Información de la Cuenta</h4>
                            <!-- Aquí coloca el correo, no hace falta cambiarlo -->
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" value="{{ $usuario['correo'] }}" disabled>

                            <!-- Icono para mostrar/ocultar campos de contraseña -->
                            <i class="fas fa-lock" id="toggle-password-fields" style="cursor: pointer;"></i>

                            <!-- Campos de contraseña (inicialmente ocultos) -->
                            <div id="password-fields" style="display: none;">
                                    <label for="password" class="form-label mt-3">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <button class="btn btn-outline-secondary" type="button" id="ver-password">
                                            <i class="fas fa-eye" id="toggle-password"></i>
                                        </button>
                                    </div>

                                    <label for="password_confirmation" class="form-label mt-3">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                        <button class="btn btn-outline-secondary" type="button" id="ver-password-confirm">
                                            <i class="fas fa-eye" id="toggle-password-confirm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        <!-- Información Personal -->
                        <div class="mb-3">
                            <h4>Información Personal</h4>
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $usuario['nombre'] }}" required>

                            <label for="apellido_paterno" class="form-label">Apellido Paterno</label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" value="{{ $usuario['apellido_paterno'] ?? '' }}">

                            <label for="apellido_materno" class="form-label">Apellido Materno</label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" value="{{ $usuario['apellido_materno'] ?? '' }}">

                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-select" id="sexo" name="sexo">
                                <option value="Masculino" {{ $usuario['sexo'] === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ $usuario['sexo'] === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Actualizar Perfil') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            var togglePasswordFields = $('#toggle-password-fields');
            var passwordFields = $('#password-fields');

            togglePasswordFields.click(function () {
                passwordFields.toggle();
            });

            // Script para mostrar/ocultar contraseña
            $('#ver-password').click(function () {
                togglePassword('password', 'toggle-password');
            });

            $('#ver-password-confirm').click(function () {
                togglePassword('password_confirmation', 'toggle-password-confirm');
            });

            function togglePassword(inputId, iconId) {
                var passwordInput = $('#' + inputId);
                var toggleIcon = $('#' + iconId);

                var type = (passwordInput.attr('type') === 'password') ? 'text' : 'password';
                passwordInput.attr('type', type);

                // Cambiar el icono entre ojo abierto y cerrado
                toggleIcon.toggleClass('fa-eye fa-eye-slash');
            }
        });

        // Mostrar la vista previa de la imagen seleccionada
        function mostrarVistaPrevia(input) {
            var vistaPrevia = $('#vista-previa');
            vistaPrevia.empty();

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    // Mostrar la imagen seleccionada arriba del formulario
                    vistaPrevia.append('<img src="' + e.target.result + '" alt="Vista Previa" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">');
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
