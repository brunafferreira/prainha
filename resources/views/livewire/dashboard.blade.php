<div class="admin-shell">
    @include('components.admin-sidebar')
    <main class="admin-main">
        <div class="page-heading">
            <div>
                <p class="page-eyebrow">PAINEL DE CONTROLE</p>
                <h1>Visão geral</h1>
                <p class="page-description">Resumo da sua prainha para {{ $monthLabel }}.</p>
            </div>
            <a class="primary-button" href="{{ route('management.index', 'alunos') }}">+ Novo aluno</a>
        </div>
        <section class="summary-grid" aria-label="Resumo">
            <article class="summary-card">
                <span class="summary-label">Alunos ativos</span>
                <strong class="summary-value">{{ $studentCount }}</strong>
                <a class="summary-link" href="{{ route('management.index', 'alunos') }}">Ver alunos →</a>
            </article>
            <article class="summary-card">
                <span class="summary-label">Turmas ativas</span>
                <strong class="summary-value">{{ $classCount }}</strong>
                <a class="summary-link" href="{{ route('management.index', 'turmas') }}">Ver turmas →</a>
            </article>
            <article class="summary-card">
                <span class="summary-label">Mensalidades pendentes</span>
                <strong class="summary-value">{{ $pendingCount }}</strong>
                <span class="summary-detail">R$ {{ number_format((float) $pendingAmount, 2, ',', '.') }} em aberto</span>
            </article>
            <article class="summary-card">
                <span class="summary-label">Próximas reservas</span>
                <strong class="summary-value">{{ $upcomingReservationCount }}</strong>
                <a class="summary-link" href="{{ route('management.index', 'reservas') }}">Ver agenda →</a>
            </article>
        </section>
        <section class="panel-card">
            <div class="panel-heading">
                <div><h2>Alunos recentes</h2><p>Últimos alunos adicionados ao sistema.</p></div>
                <a class="text-link" href="{{ route('management.index', 'alunos') }}">Ver todos</a>
            </div>
            @if ($recentStudents->isEmpty())
                <div class="empty-state">
                    <span class="empty-icon">♙</span>
                    <strong>Nenhum aluno cadastrado</strong>
                    <p>Adicione o primeiro aluno para começar a organizar sua prainha.</p>
                    <a class="primary-button" href="{{ route('management.index', 'alunos') }}">Cadastrar aluno</a>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Aluno</th><th>Contato</th><th>Turma</th><th>Situação</th></tr></thead>
                        <tbody>
                            @foreach ($recentStudents as $student)
                                <tr>
                                    <td class="table-primary">{{ $student->name }}</td>
                                    <td>{{ $student->email ?: ($student->phone ?: '—') }}</td>
                                    <td>{{ $student->beachClass?->name ?? 'Sem turma' }}</td>
                                    <td><span class="status-badge {{ $student->active ? 'status-active' : 'status-inactive' }}">{{ $student->active ? 'Ativo' : 'Inativo' }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
        <section class="panel-card dashboard-reservations">
            <div class="panel-heading">
                <div><h2>Próximas reservas</h2><p>Horários confirmados para a quadra.</p></div>
                <a class="text-link" href="{{ route('management.index', 'reservas') }}">Ver agenda</a>
            </div>
            @if ($upcomingReservations->isEmpty())
                <div class="empty-state compact-empty-state">
                    <strong>Nenhuma reserva futura</strong>
                    <p>Compartilhe o link público para receber reservas.</p>
                    <a class="public-url-link" href="{{ route('reservations.create') }}" target="_blank" rel="noopener">{{ route('reservations.create') }}</a>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Data</th><th>Horário</th><th>Cliente</th><th>Telefone</th><th>Pessoas</th></tr></thead>
                        <tbody>
                            @foreach ($upcomingReservations as $reservation)
                                <tr>
                                    <td class="table-primary">{{ $reservation->reservation_date->format('d/m/Y') }}</td>
                                    <td>{{ substr($reservation->starts_at, 0, 5) }}–{{ substr($reservation->ends_at, 0, 5) }}</td>
                                    <td>{{ $reservation->name }}</td>
                                    <td>{{ $reservation->phone }}</td>
                                    <td>{{ $reservation->participant_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
        <p class="page-footer">Prainha Vôlei · Sistema de gerenciamento</p>
    </main>
</div>
