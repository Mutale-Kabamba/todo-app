<div class="min-h-screen bg-[#0f1115] px-4 py-7 text-gray-100">
    <div class="mx-auto w-full max-w-md overflow-hidden rounded-3xl border border-gray-800 bg-[#171a21] shadow-2xl shadow-black/40">
        <div class="bg-[#1f232d] px-5 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight">Task List</h1>
                <span class="rounded-full bg-[#2b303b] px-3 py-1 text-xs font-semibold text-gray-300">
                    {{ $completedTasks }}/{{ $totalTasks }} done
                </span>
            </div>
        </div>

        <div class="space-y-4 p-4">
            <form wire:submit="addTask" class="space-y-3 rounded-2xl border border-gray-700 bg-[#10131a] p-3">
                <input
                    wire:model="title"
                    type="text"
                    placeholder="Task title"
                    class="h-11 w-full rounded-xl border border-gray-700 bg-[#0b0e14] px-3 text-sm text-gray-100 outline-none transition focus:border-[#ffbf3c]"
                >

                <div class="grid grid-cols-2 gap-2">
                    <input
                        wire:model="taskDate"
                        type="date"
                        class="h-11 rounded-xl border border-gray-700 bg-[#0b0e14] px-3 text-sm text-gray-100 outline-none transition focus:border-[#ffbf3c]"
                    >
                    <input
                        wire:model="taskTime"
                        type="time"
                        class="h-11 rounded-xl border border-gray-700 bg-[#0b0e14] px-3 text-sm text-gray-100 outline-none transition focus:border-[#ffbf3c]"
                    >
                </div>

                <div class="flex gap-2">
                    <input
                        wire:model="venue"
                        type="text"
                        placeholder="Venue"
                        class="h-11 w-full rounded-xl border border-gray-700 bg-[#0b0e14] px-3 text-sm text-gray-100 outline-none transition focus:border-[#ffbf3c]"
                    >
                    <button
                        type="submit"
                        class="h-11 rounded-xl bg-[#ffbf3c] px-4 text-sm font-bold text-gray-900 transition hover:bg-[#ffd26d]"
                    >
                        + New
                    </button>
                </div>

                @error('title') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                @error('taskDate') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                @error('taskTime') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                @error('venue') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
            </form>

            <ul class="space-y-3">
                @forelse ($activeTasks as $task)
                    <li class="rounded-2xl border border-gray-700 bg-[#121722] p-3">
                        @if ($editingTaskId === $task->id)
                            <div class="space-y-2">
                                <input wire:model="editTitle" type="text" class="h-10 w-full rounded-lg border border-gray-700 bg-[#0b0e14] px-3 text-sm outline-none focus:border-[#ffbf3c]">
                                <div class="grid grid-cols-2 gap-2">
                                    <input wire:model="editTaskDate" type="date" class="h-10 rounded-lg border border-gray-700 bg-[#0b0e14] px-3 text-sm outline-none focus:border-[#ffbf3c]">
                                    <input wire:model="editTaskTime" type="time" class="h-10 rounded-lg border border-gray-700 bg-[#0b0e14] px-3 text-sm outline-none focus:border-[#ffbf3c]">
                                </div>
                                <input wire:model="editVenue" type="text" class="h-10 w-full rounded-lg border border-gray-700 bg-[#0b0e14] px-3 text-sm outline-none focus:border-[#ffbf3c]" placeholder="Venue">

                                <div class="flex gap-2">
                                    <button type="button" wire:click="saveEdit" class="rounded-md bg-[#35d07f] px-2.5 py-1.5 text-xs font-bold text-[#0b2a18]">Save</button>
                                    <button type="button" wire:click="cancelEdit" class="rounded-md bg-gray-600 px-2.5 py-1.5 text-xs font-bold text-white">Cancel</button>
                                </div>

                                @error('editTitle') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                                @error('editTaskDate') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                                @error('editTaskTime') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                                @error('editVenue') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
                            </div>
                        @else
                            <div class="flex items-start gap-3">
                                <button
                                    type="button"
                                    wire:click="toggleDone({{ $task->id }})"
                                    class="mt-1 h-5 w-5 rounded border-2 border-[#ffbf3c] {{ $task->is_completed ? 'bg-[#ffbf3c]' : 'bg-transparent' }}"
                                    aria-label="Mark done"
                                ></button>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold {{ $task->is_completed ? 'text-gray-500 line-through' : 'text-white' }}">{{ $task->title }}</p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ optional($task->task_date)->format('d M Y') ?? 'No date' }}
                                        ·
                                        {{ $task->task_time ? \Illuminate\Support\Str::of($task->task_time)->substr(0, 5) : 'No time' }}
                                        ·
                                        {{ $task->venue ?? 'No venue' }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 flex gap-2">
                                <button type="button" wire:click="toggleDone({{ $task->id }})" class="rounded-md bg-[#35d07f] px-2.5 py-1.5 text-[11px] font-bold text-[#082111]">Done</button>
                                <button type="button" wire:click="startEdit({{ $task->id }})" class="rounded-md bg-[#3ea6ff] px-2.5 py-1.5 text-[11px] font-bold text-[#071f33]">Edit</button>
                                <button type="button" wire:click="deleteTask({{ $task->id }})" class="rounded-md bg-[#ff5c74] px-2.5 py-1.5 text-[11px] font-bold text-[#3a0a14]">Delete</button>
                            </div>
                        @endif
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-gray-700 bg-[#10131a] p-4 text-center text-sm text-gray-400">
                        No active tasks. Create your first task.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="mx-auto mt-4 w-full max-w-md px-1">
        <h2 class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">Recent Done</h2>

        @if ($recentCompletedTasks->isEmpty())
            <p class="text-xs text-gray-600">No completed tasks yet.</p>
        @else
            <ul class="space-y-1.5 text-sm text-gray-500">
                @foreach ($recentCompletedTasks as $task)
                    <li class="truncate">
                        {{ $task->title }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
