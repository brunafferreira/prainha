<main class="public-booking-page">
    <header class="booking-header">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">PV</span>
            <span class="brand-name">Prainha <strong>Vôlei</strong></span>
        </a>
        <a class="booking-admin-link" href="{{ route('home') }}">Área administrativa</a>
    </header>

    <section class="booking-card">
        @if ($confirmationCode)
            <div class="booking-confirmation">
                <span class="confirmation-mark" aria-hidden="true">✓</span>
                <p class="page-eyebrow">RESERVA CONFIRMADA</p>
                <h1>Horário reservado!</h1>
                <p class="confirmation-copy">Pronto, {{ $name }}. Sua quadra está reservada. Anote os dados:</p>
                <div class="confirmation-details">
                    <div><span>Data</span><strong>{{ \Illuminate\Support\Carbon::parse($reservation_date)->locale('pt_BR')->translatedFormat('l, d/m/Y') }}</strong></div>
                    <div><span>Horário</span><strong>{{ $starts_at }}–{{ \Illuminate\Support\Carbon::createFromFormat('H:i', $starts_at)->addMinutes(120)->format('H:i') }}</strong></div>
                    <div><span>Pessoas</span><strong>{{ $participant_count }}</strong></div>
                    <div><span>Confirmação</span><strong>#{{ $confirmationCode }}</strong></div>
                </div>
                <p class="confirmation-note">A reserva é confirmada automaticamente. Para cancelar ou alterar, entre em contato com a prainha pelo telefone informado.</p>
                <button class="primary-button full-width" type="button" wire:click="reserveAnother">Fazer outra reserva</button>
                <a class="booking-back-link" href="{{ route('home') }}">Voltar ao início</a>
            </div>
        @else
            <div class="booking-heading">
                <span class="booking-icon" aria-hidden="true">◷</span>
                <p class="page-eyebrow">AGENDE SUA VISITA</p>
                <h1>Reserve a quadra</h1>
                <p>Escolha um horário e venha jogar.</p>
            </div>

            <div class="schedule-note">
                <strong>Horário de funcionamento</strong>
                <span>Segunda a sexta: 08h–22h</span>
                <span>Sábado e domingo: 08h–18h</span>
                <span>Reservas de 2 horas. Confirmação automática.</span>
            </div>

            <form class="booking-form" wire:submit="reserve">
                <div class="field-group">
                    <label for="booking-name">Seu nome</label>
                    <input id="booking-name" type="text" wire:model="name" autocomplete="name" placeholder="Nome completo" required>
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-grid">
                    <div class="field-group">
                        <label for="booking-phone">Celular / WhatsApp</label>
                        <input id="booking-phone" type="tel" wire:model="phone" autocomplete="tel" placeholder="(00) 00000-0000" required>
                        @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="booking-email">E-mail <span class="optional-note">(opcional)</span></label>
                        <input id="booking-email" type="email" wire:model="email" autocomplete="email" placeholder="voce@email.com">
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field-group">
                        <label for="booking-date">Data</label>
                        <input id="booking-date" type="date" wire:model.live="reservation_date" min="{{ \Illuminate\Support\Carbon::today()->toDateString() }}" required>
                        @error('reservation_date') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="booking-people">Número de pessoas</label>
                        <select id="booking-people" wire:model="participant_count" required>
                            @for ($count = 1; $count <= 12; $count++)
                                <option value="{{ $count }}">{{ $count }} {{ $count === 1 ? 'pessoa' : 'pessoas' }}</option>
                            @endfor
                        </select>
                        @error('participant_count') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="field-group">
                    <label for="booking-time">Horário disponível</label>
                    <select id="booking-time" wire:model="starts_at" required @disabled($availableTimes === [])>
                        <option value="">{{ $reservation_date ? 'Selecione um horário' : 'Selecione uma data primeiro' }}</option>
                        @foreach ($availableTimes as $time)
                            <option value="{{ $time }}">{{ $time }} às {{ \Illuminate\Support\Carbon::createFromFormat('H:i', $time)->addMinutes(120)->format('H:i') }}</option>
                        @endforeach
                    </select>
                    @if ($reservation_date && $availableTimes === [])
                        <span class="field-hint">Não há horários disponíveis nesta data. Selecione outro dia.</span>
                    @endif
                    @error('starts_at') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <button class="primary-button full-width" type="submit" wire:loading.attr="disabled" wire:target="reserve" @disabled($availableTimes === [])>
                    <span wire:loading.remove wire:target="reserve">Confirmar reserva</span>
                    <span wire:loading wire:target="reserve">Confirmando...</span>
                </button>
                <p class="booking-terms">Ao confirmar, o horário fica reservado para você. Uma quadra disponível.</p>
            </form>
        @endif
    </section>
    <footer class="booking-footer">Prainha Vôlei · Areia, esporte e diversão</footer>
</main>
