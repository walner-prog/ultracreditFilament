<!-- resources/views/admin/modals/opciones.blade.php -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones del Modal</title>
    <!-- Importando Tailwind CSS vía CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<!-- resources/views/admin/modals/opciones.blade.php -->
<div class="p-6 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg">
    <h2 class="text-xl font-semibold mb-4">Opciones</h2>
    <!-- Agrega aquí el contenido de tu modal -->
    <form>
        <div class="mb-4">
            <label for="opcion1" class="block text-sm font-medium">Opción 1</label>
            <input type="text" id="opcion1" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"/>
        </div>
        <div class="mb-4">
            <label for="opcion2" class="block text-sm font-medium">Opción 2</label>
            <input type="text" id="opcion2" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm dark:bg-gray-700 dark:text-white"/>
        </div>
       
    </form>
</div>
