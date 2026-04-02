<?php

namespace App\Livewire;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class TodoList extends Component
{
    public string $title = '';
    public string $taskDate = '';
    public string $taskTime = '';
    public string $venue = '';

    public ?int $editingTaskId = null;
    public string $editTitle = '';
    public string $editTaskDate = '';
    public string $editTaskTime = '';
    public string $editVenue = '';

    public function addTask(): void
    {
        if (! Schema::hasTable('tasks')) {
            return;
        }

        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'taskDate' => 'required|date',
            'taskTime' => 'required|date_format:H:i',
            'venue' => 'required|string|max:255',
        ]);

        Task::create([
            'title' => $validated['title'],
            'task_date' => $validated['taskDate'],
            'task_time' => $validated['taskTime'],
            'venue' => $validated['venue'],
            'is_completed' => false,
            'user_id' => Auth::id(),
        ]);

        $this->reset('title', 'taskDate', 'taskTime', 'venue');
    }

    public function toggleDone(int $taskId): void
    {
        if (! Schema::hasTable('tasks')) {
            return;
        }

        $task = $this->queryTasks()->findOrFail($taskId);

        $task->update([
            'is_completed' => ! $task->is_completed,
        ]);
    }

    public function startEdit(int $taskId): void
    {
        if (! Schema::hasTable('tasks')) {
            return;
        }

        $task = $this->queryTasks()->findOrFail($taskId);

        $this->editingTaskId = $task->id;
        $this->editTitle = $task->title;
        $this->editTaskDate = $task->task_date?->format('Y-m-d') ?? '';
        $this->editTaskTime = $task->task_time ? substr((string) $task->task_time, 0, 5) : '';
        $this->editVenue = $task->venue ?? '';
    }

    public function cancelEdit(): void
    {
        $this->resetEditFields();
    }

    public function saveEdit(): void
    {
        if (! Schema::hasTable('tasks') || ! $this->editingTaskId) {
            return;
        }

        $validated = $this->validate([
            'editTitle' => 'required|string|max:255',
            'editTaskDate' => 'required|date',
            'editTaskTime' => 'required|date_format:H:i',
            'editVenue' => 'required|string|max:255',
        ]);

        $task = $this->queryTasks()->findOrFail($this->editingTaskId);

        $task->update([
            'title' => $validated['editTitle'],
            'task_date' => $validated['editTaskDate'],
            'task_time' => $validated['editTaskTime'],
            'venue' => $validated['editVenue'],
        ]);

        $this->resetEditFields();
    }

    public function deleteTask(int $taskId): void
    {
        if (! Schema::hasTable('tasks')) {
            return;
        }

        $task = $this->queryTasks()->findOrFail($taskId);
        $task->delete();
    }

    public function render()
    {
        if (! Schema::hasTable('tasks')) {
            return view('livewire.todo-list', [
                'tasks' => collect(),
                'totalTasks' => 0,
                'completedTasks' => 0,
            ]);
        }

        $tasks = $this->queryTasks()->latest()->get();

        return view('livewire.todo-list', [
            'tasks' => $tasks,
            'totalTasks' => $tasks->count(),
            'completedTasks' => $tasks->where('is_completed', true)->count(),
        ]);
    }

    protected function queryTasks(): Builder
    {
        return Task::query()->when(
            Auth::check(),
            fn ($query) => $query->where('user_id', Auth::id()),
            fn ($query) => $query->whereNull('user_id')
        );
    }

    protected function resetEditFields(): void
    {
        $this->reset('editingTaskId', 'editTitle', 'editTaskDate', 'editTaskTime', 'editVenue');
    }
}
