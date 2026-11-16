<main class="auth-page">
    <section class="auth-box">
        <a class="brand auth-brand" href="{{ route('home') }}">
            <span class="brand-mark" aria-hidden="true">PV</span>
            <span class="brand-name">Prainha <strong>Vôlei</strong></span>
        </a>
        <div class="auth-intro">
            <h1>{{ $mode === 'login' ? 'Acesse sua conta' : 'Crie sua conta' }}</h1>
            <p>{{ $mode === 'login' ? 'Entre para gerenciar sua prainha.' : 'Cadastre-se para começar a usar o sistema.' }}</p>
        </div>
        <div class="auth-tabs" role="tablist" aria-label="Acesso">
            <button type="button" class="{{ $mode === 'login' ? 'is-active' : '' }}" wire:click="showLogin">Entrar</button>
            <button type="button" class="{{ $mode === 'register' ? 'is-active' : '' }}" wire:click="showRegister">Criar conta</button>
        </div>
        @if ($mode === 'login')
            <form class="auth-form" wire:submit="login">
                <div class="field-group">
                    <label for="login-email">E-mail</label>
                    <input id="login-email" type="email" wire:model="email" autocomplete="email" required>
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="login-password">Senha</label>
                    <input id="login-password" type="password" wire:model="password" autocomplete="current-password" required>
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <label class="remember-option"><input type="checkbox" wire:model="remember"> Manter conectado</label>
                <button class="primary-button full-width" type="submit" wire:loading.attr="disabled" wire:target="login">
                    <span wire:loading.remove wire:target="login">Entrar no sistema</span>
                    <span wire:loading wire:target="login">Aguarde...</span>
                </button>
            </form>
        @else
            <form class="auth-form" wire:submit="register">
                <div class="field-group">
                    <label for="register-name">Nome</label>
                    <input id="register-name" type="text" wire:model="name" autocomplete="name" required>
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="register-email">E-mail</label>
                    <input id="register-email" type="email" wire:model="email" autocomplete="email" required>
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="register-password">Senha (mínimo 8 caracteres)</label>
                    <input id="register-password" type="password" wire:model="password" autocomplete="new-password" required>
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="register-password-confirmation">Confirme a senha</label>
                    <input id="register-password-confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password" required>
                </div>
                <button class="primary-button full-width" type="submit" wire:loading.attr="disabled" wire:target="register">
                    <span wire:loading.remove wire:target="register">Criar conta e entrar</span>
                    <span wire:loading wire:target="register">Aguarde...</span>
                </button>
            </form>
        @endif
        <p class="auth-footnote">Sistema de gerenciamento · Prainha Vôlei</p>
        <a class="public-booking-link" href="{{ route('reservations.create') }}">Quero reservar a quadra</a>
    </section>
</main>
