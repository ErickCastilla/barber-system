<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

/**
 * Controlador ServiceController (para el Administrador)
 * Gestiona el catálogo de servicios (CRUD: Crear, Leer, Actualizar, Eliminar).
 */
class ServiceController extends Controller
{
    /**
     * Muestra la lista de todos los servicios.
     */
    public function index()
    {
        // Obtenemos todos los servicios ordenados por nombre
        $services = Service::orderBy('name')->get();
        // Retornamos la vista pasándole los servicios
        return view('admin.services.index', compact('services'));
    }

    /**
     * Muestra el formulario para crear un nuevo servicio.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Guarda el nuevo servicio en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validamos que los datos que envía el formulario sean correctos
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5'], // mínimo 5 minutos
            'price' => ['required', 'numeric', 'min:0'], // precio no puede ser negativo
        ]);

        // 2. Creamos el servicio usando asignación masiva
        Service::create($request->all());

        // 3. Redirigimos a la lista de servicios con un mensaje de éxito
        return redirect()->route('admin.services.index')->with('success', 'Servicio creado exitosamente.');
    }

    /**
     * Elimina el servicio especificado de la base de datos.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Servicio eliminado correctamente.');
    }
}
