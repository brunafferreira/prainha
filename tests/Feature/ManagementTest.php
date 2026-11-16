<?php

namespace Tests\Feature;

use App\Livewire\ManageRecords;
use App\Livewire\Settings;
use App\Models\BeachClass;
use App\Models\MonthlyFee;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_management_sections_require_authentication_and_load_for_authenticated_users(): void
    {
        $this->get(route('management.index', 'alunos'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create());

        foreach (['alunos', 'turmas', 'mensalidades'] as $section) {
            $this->get(route('management.index', $section))->assertOk();
        }

        $this->get(route('settings'))->assertOk();
        $this->get('/nao-existe')->assertNotFound();
    }

    public function test_a_user_can_update_their_account_settings(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->set('name', 'Ana Silva')
            ->set('email', 'ana.silva@example.com')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Ana Silva', 'email' => 'ana.silva@example.com']);
    }

    public function test_a_user_can_create_edit_and_delete_a_student(): void
    {
        $class = BeachClass::create([
            'name' => 'Turma da manhã',
            'level' => 'Iniciante',
            'weekday' => 'Segunda-feira',
            'starts_at' => '08:00',
        ]);

        Livewire::actingAs(User::factory()->create())
            ->test(ManageRecords::class, ['section' => 'alunos'])
            ->call('create')
            ->set('name', 'Ana Jogadora')
            ->set('email', 'ana@example.com')
            ->set('phone', '11999999999')
            ->set('beach_class_id', $class->id)
            ->call('save')
            ->assertHasNoErrors();

        $student = Student::where('email', 'ana@example.com')->firstOrFail();
        $this->assertSame($class->id, $student->beach_class_id);

        Livewire::actingAs(User::firstOrFail())
            ->test(ManageRecords::class, ['section' => 'alunos'])
            ->call('edit', $student->id)
            ->set('name', 'Ana Silva')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('students', ['id' => $student->id, 'name' => 'Ana Silva']);

        Livewire::actingAs(User::firstOrFail())
            ->test(ManageRecords::class, ['section' => 'alunos'])
            ->call('delete', $student->id);

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_a_user_can_create_and_update_a_class(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(ManageRecords::class, ['section' => 'turmas'])
            ->call('create')
            ->set('name', 'Treino de sábado')
            ->set('level', 'Intermediário')
            ->set('weekday', 'Sábado')
            ->set('starts_at', '09:30')
            ->call('save')
            ->assertHasNoErrors();

        $class = BeachClass::firstOrFail();

        Livewire::actingAs(User::firstOrFail())
            ->test(ManageRecords::class, ['section' => 'turmas'])
            ->call('edit', $class->id)
            ->set('active', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('beach_classes', ['id' => $class->id, 'active' => false]);
    }

    public function test_a_user_can_manage_monthly_fees_and_mark_them_paid(): void
    {
        $student = Student::create(['name' => 'João Atleta', 'active' => true]);

        Livewire::actingAs(User::factory()->create())
            ->test(ManageRecords::class, ['section' => 'mensalidades'])
            ->call('create')
            ->set('student_id', $student->id)
            ->set('amount', '180.00')
            ->set('due_date', '2026-12-10')
            ->set('reference', '2026-12')
            ->call('save')
            ->assertHasNoErrors();

        $fee = MonthlyFee::firstOrFail();
        $this->assertSame('pending', $fee->status);

        Livewire::actingAs(User::firstOrFail())
            ->test(ManageRecords::class, ['section' => 'mensalidades'])
            ->call('toggleStatus', $fee->id);

        $this->assertDatabaseHas('monthly_fees', ['id' => $fee->id, 'status' => 'paid']);
        $this->assertNotNull($fee->fresh()->paid_at);
    }
}
