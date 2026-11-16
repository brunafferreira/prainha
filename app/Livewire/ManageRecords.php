<?php

namespace App\Livewire;

use App\Models\BeachClass;
use App\Models\CourtReservation;
use App\Models\MonthlyFee;
use App\Models\Student;
use App\Support\ReservationSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ManageRecords extends Component
{
    use WithPagination;

    public bool $sidebarOpen = false;

    #[Locked]
    public string $section = 'alunos';

    #[Locked]
    public ?int $recordId = null;

    public string $search = '';

    public bool $modalOpen = false;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public ?int $beach_class_id = null;

    public string $level = '';

    public string $weekday = '';

    public string $starts_at = '';

    public bool $active = true;

    public ?int $student_id = null;

    public string $amount = '';

    public string $due_date = '';

    public string $reference = '';

    public string $status = 'pending';

    public string $reserved_name = '';

    public string $reserved_phone = '';

    public string $reserved_email = '';

    public int $participant_count = 4;

    public string $reservation_date = '';

    public function mount(string $section): void
    {
        abort_unless(in_array($section, ['alunos', 'turmas', 'mensalidades', 'reservas'], true), 404);

        $this->section = $section;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedReservationDate(): void
    {
        if ($this->section === 'reservas') {
            $this->starts_at = '';
            $this->resetValidation('starts_at');
        }
    }

    public function create(): void
    {
        $this->resetForm();

        if ($this->section === 'reservas') {
            $this->reservation_date = Carbon::today()->toDateString();

            if (ReservationSchedule::availableTimes($this->reservation_date) === []) {
                $this->reservation_date = Carbon::tomorrow()->toDateString();
            }
        }

        $this->modalOpen = true;
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $this->recordId = $id;

        match ($this->section) {
            'alunos' => $this->fillStudent(Student::findOrFail($id)),
            'turmas' => $this->fillClass(BeachClass::findOrFail($id)),
            'mensalidades' => $this->fillFee(MonthlyFee::findOrFail($id)),
            'reservas' => $this->fillReservation(CourtReservation::findOrFail($id)),
        };

        $this->modalOpen = true;
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules());
        $model = $this->model();

        if ($this->section === 'mensalidades') {
            $validated['paid_at'] = $validated['status'] === 'paid' ? ($model->find($this->recordId)?->paid_at ?? now()->toDateString()) : null;
        }

        if ($this->section === 'reservas') {
            $validated['ends_at'] = Carbon::createFromFormat('H:i', $validated['starts_at'])
                ->addMinutes(ReservationSchedule::DURATION_MINUTES)
                ->format('H:i:s');
            $validated['name'] = $validated['reserved_name'];
            $validated['phone'] = $validated['reserved_phone'];
            $validated['email'] = $validated['reserved_email'];
            unset($validated['reserved_name'], $validated['reserved_phone'], $validated['reserved_email']);
        }

        try {
            if ($this->recordId) {
                $model->findOrFail($this->recordId)->update($validated);
            } else {
                $model->create($validated);
            }
        } catch (UniqueConstraintViolationException) {
            $this->addError('starts_at', 'Este horário acabou de ser reservado. Escolha outro horário.');

            return;
        }

        $this->modalOpen = false;
        $this->resetForm();
        session()->flash('notice', 'Registro salvo com sucesso.');
    }

    public function delete(int $id): void
    {
        $this->model()->findOrFail($id)->delete();
        session()->flash('notice', 'Registro removido com sucesso.');
    }

    public function toggleStatus(int $id): void
    {
        abort_unless($this->section === 'mensalidades', 404);

        $fee = MonthlyFee::findOrFail($id);
        $fee->update([
            'status' => $fee->status === 'paid' ? 'pending' : 'paid',
            'paid_at' => $fee->status === 'paid' ? null : now()->toDateString(),
        ]);
    }

    private function fillStudent(Student $student): void
    {
        $this->name = $student->name;
        $this->email = $student->email ?? '';
        $this->phone = $student->phone ?? '';
        $this->beach_class_id = $student->beach_class_id;
        $this->active = $student->active;
    }

    private function fillClass(BeachClass $class): void
    {
        $this->name = $class->name;
        $this->level = $class->level;
        $this->weekday = $class->weekday;
        $this->starts_at = substr($class->starts_at, 0, 5);
        $this->active = $class->active;
    }

    private function fillFee(MonthlyFee $fee): void
    {
        $this->student_id = $fee->student_id;
        $this->amount = (string) $fee->amount;
        $this->due_date = $fee->due_date->format('Y-m-d');
        $this->reference = $fee->reference;
        $this->status = $fee->status;
    }

    private function fillReservation(CourtReservation $reservation): void
    {
        $this->reserved_name = $reservation->name;
        $this->reserved_phone = $reservation->phone;
        $this->reserved_email = $reservation->email ?? '';
        $this->participant_count = $reservation->participant_count;
        $this->reservation_date = $reservation->reservation_date->format('Y-m-d');
        $this->starts_at = substr($reservation->starts_at, 0, 5);
    }

    private function rules(): array
    {
        return match ($this->section) {
            'alunos' => [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
                'beach_class_id' => ['nullable', 'exists:beach_classes,id'],
                'active' => ['boolean'],
            ],
            'turmas' => [
                'name' => ['required', 'string', 'max:255'],
                'level' => ['required', Rule::in(['Iniciante', 'Intermediário', 'Avançado'])],
                'weekday' => ['required', Rule::in(['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'])],
                'starts_at' => ['required', 'date_format:H:i'],
                'active' => ['boolean'],
            ],
            'mensalidades' => [
                'student_id' => ['required', 'exists:students,id'],
                'amount' => ['required', 'numeric', 'gt:0', 'max:99999999'],
                'due_date' => ['required', 'date'],
                'reference' => [
                    'required',
                    'date_format:Y-m',
                    Rule::unique('monthly_fees', 'reference')->where('student_id', $this->student_id)->ignore($this->recordId),
                ],
                'status' => ['required', Rule::in(['pending', 'paid'])],
            ],
            'reservas' => [
                'reserved_name' => ['required', 'string', 'max:255'],
                'reserved_phone' => ['required', 'string', 'max:30'],
                'reserved_email' => ['nullable', 'email', 'max:255'],
                'participant_count' => ['required', 'integer', 'between:1,12'],
                'reservation_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
                'starts_at' => ['required', 'date_format:H:i', Rule::in(ReservationSchedule::availableTimes($this->reservation_date, $this->recordId))],
            ],
        };
    }

    private function model(): Student|BeachClass|MonthlyFee|CourtReservation
    {
        return match ($this->section) {
            'alunos' => new Student,
            'turmas' => new BeachClass,
            'mensalidades' => new MonthlyFee,
            'reservas' => new CourtReservation,
        };
    }

    private function resetForm(): void
    {
        $this->reset([
            'recordId', 'name', 'email', 'phone', 'beach_class_id', 'level',
            'weekday', 'starts_at', 'student_id', 'amount', 'due_date', 'reference',
            'reserved_name', 'reserved_phone', 'reserved_email', 'reservation_date',
        ]);
        $this->active = true;
        $this->status = 'pending';
        $this->participant_count = 4;
        $this->resetValidation();
    }

    public function render()
    {
        $query = match ($this->section) {
            'alunos' => Student::query()->with('beachClass')->when($this->search, fn (Builder $query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")->orWhere('phone', 'like', "%{$this->search}%")),
            'turmas' => BeachClass::query()->withCount('students')->when($this->search, fn (Builder $query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('level', 'like', "%{$this->search}%")),
            'mensalidades' => MonthlyFee::query()->with('student')->when($this->search, fn (Builder $query) => $query->whereHas('student', fn (Builder $student) => $student->where('name', 'like', "%{$this->search}%"))->orWhere('reference', 'like', "%{$this->search}%")),
            'reservas' => CourtReservation::query()->when($this->search, fn (Builder $query) => $query->where('name', 'like', "%{$this->search}%")->orWhere('phone', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%")),
        };

        return view('livewire.manage-records', [
            'records' => $this->section === 'reservas'
                ? $query->orderBy('reservation_date')->orderBy('starts_at')->paginate(10)
                : $query->latest()->paginate(10),
            'classes' => BeachClass::orderBy('name')->get(),
            'students' => Student::orderBy('name')->get(),
            'availableReservationTimes' => ReservationSchedule::availableTimes($this->reservation_date, $this->recordId),
        ]);
    }
}
