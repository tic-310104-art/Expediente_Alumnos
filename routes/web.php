<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('prueba');
});

 Route::get('/registro',function(){
        return view('register');
});

 Route::get('/sesion',function(){
        return view('login');
});

Route::get('/expedienteGeneral',function(){
        return view('/admins/expedienteG');
});

Route::get('/tutor',function(){
        return view('/tutores/tutor');
});

Route::get('/alumno',function(){
        return view('/alumnos/alumno');
});

Route::get('/gestion-alumnos',function(){
        return view('/admins/gestion_alumnos');
});


Route::get('/gestion-tutores',function(){
        return view('/admins/gestion_tutores');
});

Route::get('/gestion-admins',function(){
        return view('/admins/gestion_admins');
});