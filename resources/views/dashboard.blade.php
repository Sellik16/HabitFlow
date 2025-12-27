<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="p-6 text-gray-900">
    {{ __("Twoje Nawyki") }}

    <form action="{{ route('habits.store') }}" method="POST" class="mt-6 bg-gray-100 p-4 rounded shadow-sm">
        @csrf
        <div class="flex gap-4">
            <input type="text" name="title" placeholder="Nazwa nawyku (np. Bieganie)" class="border-gray-300 rounded-md shadow-sm flex-1" required>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Dodaj
            </button>
        </div>
    </form>

    @forelse($habits as $habit)
    <li class="p-4 bg-white border rounded shadow-sm flex justify-between items-center">
        <div>
            <span class="font-bold">{{ $habit->title }}</span> 
            <span class="text-sm text-gray-500">(Streak: {{ $habit->current_streak }})</span>
        </div>
        
        <div class="flex gap-2">
            <button onclick="editHabit({{ $habit->id }}, '{{ $habit->title }}')" class="text-blue-500 hover:text-blue-700">
                Edytuj
            </button>

            <form action="{{ route('habits.destroy', $habit) }}" method="POST" onsubmit="return confirm('Na pewno usunąć?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700">Usuń</button>
            </form>
        </div>
    </li>
        @empty
    @endforelse

    <script>
        function editHabit(id, currentTitle) {
            let newTitle = prompt("Edytuj nazwę nawyku:", currentTitle);
            if (newTitle && newTitle !== currentTitle) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = `/habits/${id}`;
                form.innerHTML = `
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="title" value="${newTitle}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</div>
</x-app-layout>
