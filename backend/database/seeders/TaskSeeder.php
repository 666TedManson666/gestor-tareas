<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $task1 = Task::create([
            'title'       => 'Configurar entorno de desarrollo',
            'description' => 'Instalar y configurar todas las herramientas necesarias para el proyecto.',
            'status'      => 'done',
        ]);
        $task1->items()->createMany([
            ['title' => 'Instalar Docker',        'is_completed' => true,  'priority' => 'high'],
            ['title' => 'Configurar VSCode',       'is_completed' => true,  'priority' => 'medium'],
            ['title' => 'Clonar repositorio',      'is_completed' => true,  'priority' => 'high'],
            ['title' => 'Levantar servicios',      'is_completed' => true,  'priority' => 'high'],
        ]);

        $task2 = Task::create([
            'title'       => 'Desarrollar API REST con Laravel',
            'description' => 'Crear todos los endpoints para el gestor de tareas siguiendo buenas prácticas REST.',
            'status'      => 'inProgress',
        ]);
        $task2->items()->createMany([
            ['title' => 'Crear modelos y migraciones',       'is_completed' => true,  'priority' => 'high'],
            ['title' => 'Implementar controladores',         'is_completed' => true,  'priority' => 'high'],
            ['title' => 'Configurar FormRequests',           'is_completed' => true,  'priority' => 'medium'],
            ['title' => 'Escribir tests de integración',    'is_completed' => false, 'priority' => 'medium'],
        ]);

        $task3 = Task::create([
            'title'       => 'Diseñar interfaz con Vue 3',
            'description' => 'Crear los componentes Vue para el frontend del gestor de tareas.',
            'status'      => 'pending',
        ]);
        $task3->items()->createMany([
            ['title' => 'Componente TaskList',   'is_completed' => false, 'priority' => 'high'],
            ['title' => 'Componente TaskCard',   'is_completed' => false, 'priority' => 'high'],
            ['title' => 'Componente TaskForm',   'is_completed' => false, 'priority' => 'high'],
            ['title' => 'Componente ItemList',   'is_completed' => false, 'priority' => 'medium'],
            ['title' => 'Estilos con Tailwind',  'is_completed' => false, 'priority' => 'low'],
        ]);
    }
}
