<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Tag;

/**
 * Clase TaskController
 *
 * Controlador para manejar operaciones relacionadas con las tareas.
 */
class TaskController extends Controller
{
    /**
     * Muestra una lista de los recursos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Recupera las tareas del usuario autenticado
        $tasks = Task::where('user_id', auth()->id())->get();

        // Devuelve la vista de índice de tareas con los datos de las tareas
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Muestra el formulario para crear un nuevo recurso.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Recupera todas las categorías y etiquetas
        $categories = Category::all();
        $tags = Tag::all();

        // Devuelve la vista de creación de tareas con los datos de las categorías y etiquetas
        return view('tasks.create', compact('categories', 'tags'));
    }

    /**
     * Almacena un recurso recién creado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Valida los datos de la solicitud
        $request->validate([
            'title' => 'required',
            'category_id' => 'required',
            'priority' => 'required',
            'due_date' => 'nullable|date',
        ]);

        // Crea una nueva tarea con los datos de la solicitud y la guarda
        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->due_date = $request->due_date;
        $task->user_id = Auth::id();
        $task->save();

        // Adjunta las etiquetas seleccionadas a la tarea
        $task->tags()->attach($request->tags);

        // Redirige a la vista de índice de tareas con un mensaje de éxito
        return redirect()->route('tasks.index')->with('success', 'Tarea creada con éxito');
    }

    /**
     * Muestra el recurso especificado.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function show(Task $task)
    {
        // Devuelve la vista de mostrar tarea con los datos de la tarea
        return view('tasks.show', compact('task'));
    }

    /**
     * Muestra el formulario para editar el recurso especificado.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\View\View
     */
    public function edit(Task $task)
    {
        // Recupera todas las categorías y etiquetas
        $categories = Category::all();
        $tags = Tag::all();

        // Devuelve la vista de edición de tareas con los datos de la tarea, las categorías y las etiquetas
        return view('tasks.edit', compact('task', 'categories', 'tags'));
    }

    /**
     * Actualiza el recurso especificado en el almacenamiento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Task $task)
    {
        // Valida los datos de la solicitud
        $request->validate([
            'title' => 'required',
            'category_id' => 'required',
            'priority' => 'required',
            'due_date' => 'nullable|date',
        ]);

        // Actualiza la tarea con los datos de la solicitud y la guarda
        $task->title = $request->title;
        $task->description = $request->description;
        $task->category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->due_date = $request->due_date;
        $task->save();

        // Sincroniza las etiquetas seleccionadas con la tarea
        $task->tags()->sync($request->tags);

        // Redirige a la vista de índice de tareas con un mensaje de éxito
        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada con éxito');
    }

    /**
     * Elimina el recurso especificado del almacenamiento.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {
        // Elimina la tarea
        $task->delete();

        // Redirige a la vista de índice de tareas con un mensaje de éxito
        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada con éxito');
    }
}
