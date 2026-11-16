@php
    $titles = ['alunos' => 'Alunos', 'turmas' => 'Turmas', 'mensalidades' => 'Mensalidades', 'reservas' => 'Reservas'];
    $title = $titles[$section];
@endphp
<div class="admin-shell">
    @include('components.admin-sidebar')
    <main class="admin-main">
        <div class="page-heading">
            <div>
                <p class="page-eyebrow">GERENCIAMENTO</p>
                <h1>{{ $title }}</h1>
                <p class="page-description">{{ $section === 'reservas' ? 'Consulte e organize os horários reservados na quadra.' : 'Cadastre, consulte e atualize ' . strtolower($title) . ' da prainha.' }}</p>
            </div>
            <button type="button" class="primary-button" wire:click="create">+ {{ $section === 'reservas' ? 'Nova reserva' : 'Novo ' . ($section === 'alunos' ? 'aluno' : ($section === 'turmas' ? 'turma' : 'lançamento')) }}</button>
        </div>

        @if (session()->has('notice'))
            <div class="notice-message" role="status">{{ session('notice') }}</div>
        @endif

        <section class="panel-card records-panel">
            <div class="records-toolbar">
                <div><h2>{{ $section === 'reservas' ? 'Agenda da quadra' : 'Todos ' . ($section === 'alunos' ? 'os alunos' : ($section === 'turmas' ? 'as turmas' : 'os lançamentos')) }}</h2><p>{{ $records->total() }} registros</p></div>
                <label class="search-box">
                    <span aria-hidden="true">⌕</span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar...">
                </label>
            </div>

            @if ($records->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">{{ $section === 'alunos' ? '♙' : ($section === 'turmas' ? '▤' : ($section === 'mensalidades' ? 'R$' : '◷')) }}</span>
                    <strong>{{ $search ? 'Nenhum resultado encontrado' : 'Nenhum registro cadastrado' }}</strong>
                    <p>{{ $search ? 'Tente outra busca.' : ($section === 'reservas' ? 'Ainda não há horários reservados para a quadra.' : 'Adicione o primeiro registro para começar.') }}</p>
                    @unless ($search)
                        <button class="primary-button" type="button" wire:click="create">{{ $section === 'reservas' ? 'Adicionar reserva' : 'Adicionar ' . ($section === 'alunos' ? 'aluno' : ($section === 'turmas' ? 'turma' : 'mensalidade')) }}</button>
                    @endunless
                </div>
            @else
                <div class="table-wrap">
                    @if ($section === 'alunos')
                        <table>
                            <thead><tr><th>Nome</th><th>E-mail</th><th>Telefone</th><th>Turma</th><th>Situação</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($records as $student)
                                    <tr>
                                        <td class="table-primary">{{ $student->name }}</td><td>{{ $student->email ?: '—' }}</td><td>{{ $student->phone ?: '—' }}</td><td>{{ $student->beachClass?->name ?? 'Sem turma' }}</td>
                                        <td><span class="status-badge {{ $student->active ? 'status-active' : 'status-inactive' }}">{{ $student->active ? 'Ativo' : 'Inativo' }}</span></td>
                                        <td class="row-actions"><button type="button" wire:click="edit({{ $student->id }})">Editar</button><button type="button" class="delete-action" wire:click="delete({{ $student->id }})" wire:confirm="Excluir este aluno e suas mensalidades?">Excluir</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif ($section === 'turmas')
                        <table>
                            <thead><tr><th>Turma</th><th>Nível</th><th>Dia</th><th>Horário</th><th>Alunos</th><th>Situação</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($records as $class)
                                    <tr>
                                        <td class="table-primary">{{ $class->name }}</td><td>{{ $class->level }}</td><td>{{ $class->weekday }}</td><td>{{ substr($class->starts_at, 0, 5) }}</td><td>{{ $class->students_count }}</td>
                                        <td><span class="status-badge {{ $class->active ? 'status-active' : 'status-inactive' }}">{{ $class->active ? 'Ativa' : 'Inativa' }}</span></td>
                                        <td class="row-actions"><button type="button" wire:click="edit({{ $class->id }})">Editar</button><button type="button" class="delete-action" wire:click="delete({{ $class->id }})" wire:confirm="Excluir esta turma? Os alunos associados ficarão sem turma.">Excluir</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif ($section === 'mensalidades')
                        <table>
                            <thead><tr><th>Aluno</th><th>Referência</th><th>Vencimento</th><th>Valor</th><th>Situação</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($records as $fee)
                                    <tr>
                                        <td class="table-primary">{{ $fee->student->name }}</td><td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $fee->reference)->format('m/Y') }}</td><td>{{ $fee->due_date->format('d/m/Y') }}</td><td>R$ {{ number_format((float) $fee->amount, 2, ',', '.') }}</td>
                                        <td><button type="button" class="status-badge {{ $fee->status === 'paid' ? 'status-active' : 'status-pending' }}" wire:click="toggleStatus({{ $fee->id }})">{{ $fee->status === 'paid' ? 'Pago' : 'Pendente' }}</button></td>
                                        <td class="row-actions"><button type="button" wire:click="edit({{ $fee->id }})">Editar</button><button type="button" class="delete-action" wire:click="delete({{ $fee->id }})" wire:confirm="Excluir esta mensalidade?">Excluir</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <table>
                            <thead><tr><th>Data</th><th>Horário</th><th>Cliente</th><th>Contato</th><th>Pessoas</th><th>Situação</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($records as $reservation)
                                    <tr>
                                        <td class="table-primary">{{ $reservation->reservation_date->format('d/m/Y') }}</td>
                                        <td>{{ substr($reservation->starts_at, 0, 5) }}–{{ substr($reservation->ends_at, 0, 5) }}</td>
                                        <td>{{ $reservation->name }}</td>
                                        <td>{{ $reservation->phone }}{{ $reservation->email ? ' · ' . $reservation->email : '' }}</td>
                                        <td>{{ $reservation->participant_count }}</td>
                                        <td><span class="status-badge status-active">Confirmada</span></td>
                                        <td class="row-actions"><button type="button" wire:click="edit({{ $reservation->id }})">Editar</button><button type="button" class="delete-action" wire:click="delete({{ $reservation->id }})" wire:confirm="Cancelar esta reserva e liberar o horário?">Cancelar</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="pagination-wrap">{{ $records->links() }}</div>
            @endif
        </section>
        <p class="page-footer">Prainha Vôlei · Sistema de gerenciamento</p>
    </main>

    @if ($modalOpen)
        <div class="modal-backdrop" wire:click.self="$set('modalOpen', false)">
            <section class="record-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
                <div class="modal-heading">
                    <div><h2 id="modal-title">{{ $recordId ? 'Editar' : 'Novo' }} {{ $section === 'alunos' ? 'aluno' : ($section === 'turmas' ? 'turma' : ($section === 'mensalidades' ? 'lançamento' : 'reserva')) }}</h2><p>Preencha os dados abaixo.</p></div>
                    <button class="modal-close" type="button" wire:click="$set('modalOpen', false)" aria-label="Fechar">×</button>
                </div>
                <form wire:submit="save" class="record-form">
                    @if ($section === 'alunos')
                        <div class="field-group"><label for="record-name">Nome completo</label><input id="record-name" type="text" wire:model="name" required>@error('name') <span class="field-error">{{ $message }}</span> @enderror</div>
                        <div class="form-grid">
                            <div class="field-group"><label for="record-email">E-mail</label><input id="record-email" type="email" wire:model="email">@error('email') <span class="field-error">{{ $message }}</span> @enderror</div>
                            <div class="field-group"><label for="record-phone">Telefone</label><input id="record-phone" type="tel" wire:model="phone">@error('phone') <span class="field-error">{{ $message }}</span> @enderror</div>
                        </div>
                        <div class="field-group"><label for="record-class">Turma</label><select id="record-class" wire:model="beach_class_id"><option value="">Sem turma</option>@foreach ($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>@error('beach_class_id') <span class="field-error">{{ $message }}</span> @enderror</div>
                        <label class="remember-option"><input type="checkbox" wire:model="active"> Aluno ativo</label>
                    @elseif ($section === 'turmas')
                        <div class="field-group"><label for="record-name">Nome da turma</label><input id="record-name" type="text" wire:model="name" required>@error('name') <span class="field-error">{{ $message }}</span> @enderror</div>
                        <div class="form-grid">
                            <div class="field-group"><label for="record-level">Nível</label><select id="record-level" wire:model="level" required><option value="">Selecione</option><option>Iniciante</option><option>Intermediário</option><option>Avançado</option></select>@error('level') <span class="field-error">{{ $message }}</span> @enderror</div>
                            <div class="field-group"><label for="record-weekday">Dia da semana</label><select id="record-weekday" wire:model="weekday" required><option value="">Selecione</option><option>Segunda-feira</option><option>Terça-feira</option><option>Quarta-feira</option><option>Quinta-feira</option><option>Sexta-feira</option><option>Sábado</option><option>Domingo</option></select>@error('weekday') <span class="field-error">{{ $message }}</span> @enderror</div>
                        </div>
                        <div class="field-group"><label for="record-time">Horário</label><input id="record-time" type="time" wire:model="starts_at" required>@error('starts_at') <span class="field-error">{{ $message }}</span> @enderror</div>
                        <label class="remember-option"><input type="checkbox" wire:model="active"> Turma ativa</label>
                    @elseif ($section === 'mensalidades')
                        <div class="field-group"><label for="record-student">Aluno</label><select id="record-student" wire:model="student_id" required><option value="">Selecione o aluno</option>@foreach ($students as $student)<option value="{{ $student->id }}">{{ $student->name }}</option>@endforeach</select>@error('student_id') <span class="field-error">{{ $message }}</span> @enderror</div>
                        <div class="form-grid">
                            <div class="field-group"><label for="record-amount">Valor (R$)</label><input id="record-amount" type="number" step="0.01" min="0.01" wire:model="amount" required>@error('amount') <span class="field-error">{{ $message }}</span> @enderror</div>
                            <div class="field-group"><label for="record-due">Vencimento</label><input id="record-due" type="date" wire:model="due_date" required>@error('due_date') <span class="field-error">{{ $message }}</span> @enderror</div>
                        </div>
                        <div class="form-grid">
                            <div class="field-group"><label for="record-reference">Referência</label><input id="record-reference" type="month" wire:model="reference" required>@error('reference') <span class="field-error">{{ $message }}</span> @enderror</div>
                            <div class="field-group"><label for="record-status">Situação</label><select id="record-status" wire:model="status"><option value="pending">Pendente</option><option value="paid">Pago</option></select>@error('status') <span class="field-error">{{ $message }}</span> @enderror</div>
                        </div>
                    @else
                        <div class="field-group"><label for="record-reserved-name">Nome de quem reservou</label><input id="record-reserved-name" type="text" wire:model="reserved_name" required>@error('reserved_name') <span class="field-error">{{ $message }}</span> @enderror</div>
                        <div class="form-grid">
                            <div class="field-group"><label for="record-reserved-phone">Celular / WhatsApp</label><input id="record-reserved-phone" type="tel" wire:model="reserved_phone" required>@error('reserved_phone') <span class="field-error">{{ $message }}</span> @enderror</div>
                            <div class="field-group"><label for="record-reserved-email">E-mail <span class="optional-note">(opcional)</span></label><input id="record-reserved-email" type="email" wire:model="reserved_email">@error('reserved_email') <span class="field-error">{{ $message }}</span> @enderror</div>
                        </div>
                        <div class="form-grid">
                            <div class="field-group"><label for="record-reservation-date">Data</label><input id="record-reservation-date" type="date" wire:model.live="reservation_date" min="{{ now()->toDateString() }}" required>@error('reservation_date') <span class="field-error">{{ $message }}</span> @enderror</div>
                            <div class="field-group"><label for="record-participants">Número de pessoas</label><input id="record-participants" type="number" min="1" max="12" wire:model="participant_count" required>@error('participant_count') <span class="field-error">{{ $message }}</span> @enderror</div>
                        </div>
                        <div class="field-group"><label for="record-reservation-time">Horário (reservas de 2 horas)</label><select id="record-reservation-time" wire:model="starts_at" required><option value="">Selecione um horário</option>@foreach ($availableReservationTimes as $time)<option value="{{ $time }}">{{ $time }}–{{ \Illuminate\Support\Carbon::createFromFormat('H:i', $time)->addMinutes(120)->format('H:i') }}</option>@endforeach</select>@error('starts_at') <span class="field-error">{{ $message }}</span> @enderror</div>
                        <p class="field-hint">Seg–Sex: 08h–22h · Sáb–Dom: 08h–18h. O horário é confirmado ao salvar.</p>
                    @endif
                    <div class="modal-actions"><button class="secondary-button" type="button" wire:click="$set('modalOpen', false)">Cancelar</button><button class="primary-button" type="submit" wire:loading.attr="disabled" wire:target="save">Salvar</button></div>
                </form>
            </section>
        </div>
    @endif
</div>
