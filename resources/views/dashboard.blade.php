<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Moje Nawyki</h3>

                <form action="{{ route('habits.store') }}" method="POST" class="mb-10 bg-gray-100 p-6 rounded-xl border border-gray-200">
                    @csrf
                    <div class="flex gap-4">
                        <input type="text" name="title" placeholder="Wpisz nowy nawyk..." 
                               class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm flex-1 p-3" required>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-lg transition uppercase text-sm tracking-widest">
                            Dodaj nawyk
                        </button>
                    </div>
                </form>

                <div class="space-y-4">
                    @forelse($habits as $habit)
                        <div class="p-5 bg-white border-2 border-gray-100 rounded-xl shadow-md flex justify-between items-center hover:border-indigo-200 transition">
                            
                            <div class="flex flex-col gap-1">
                                <span class="font-black text-xl text-gray-800">{{ $habit->title }}</span>
                                <div class="flex items-center">
                                    <span class="px-3 py-1 rounded-full text-sm font-bold bg-orange-500 text-white shadow-sm">
                                        Streak: {{ $habit->current_streak }} 🔥
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <form action="{{ route('habits.complete', $habit) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-black rounded-lg shadow-md transition transform active:scale-95 uppercase text-xs">
                                        Zalicz
                                    </button>
                                </form>

                                <button onclick="editHabit({{ $habit->id }}, '{{ $habit->title }}')" 
                                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-lg shadow-md transition uppercase text-xs">
                                    Edytuj
                                </button>

                                <form action="{{ route('habits.destroy', $habit) }}" method="POST" onsubmit="return confirm('Usunąć ten nawyk?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-black rounded-lg shadow-md transition uppercase text-xs">
                                        Usuń
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed">
                            Nie masz jeszcze żadnych nawyków.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function editHabit(id, currentTitle) {
            let newTitle = prompt("Zmień nazwę nawyku:", currentTitle);
            if (newTitle && newTitle.trim() !== "" && newTitle !== currentTitle) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = `/habits/${id}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="title" value="${newTitle.trim()}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>