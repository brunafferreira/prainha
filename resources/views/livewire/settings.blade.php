<div class="admin-shell">
    @include('components.admin-sidebar')
    <main class="admin-main">
        <div class="page-heading">
            <div>
                <p class="page-eyebrow">GERENCIAMENTO</p>
                <h1>Configurações</h1>
                <p class="page-description">Atualize os dados da sua conta.</p>
            </div>
        </div>
        @if (session()->has('notice'))
            <div class="notice-message" role="status">{{ session('notice') }}</div>
        @endif
        <section class="panel-card settings-panel">
            <div class="panel-heading">
                <div><h2>Minha conta</h2><p>Altere seu nome, e-mail ou senha de acesso.</p></div>
            </div>
            <form class="settings-form" wire:submit="save">
                <div class="field-group">
                    <label for="settings-name">Nome</label>
                    <input id="settings-name" type="text" wire:model="name" autocomplete="name" required>
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="field-group">
                    <label for="settings-email">E-mail</label>
                    <input id="settings-email" type="email" wire:model="email" autocomplete="email" required>
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-grid">
                    <div class="field-group">
                        <label for="settings-password">Nova senha <span class="optional-note">(opcional)</span></label>
                        <input id="settings-password" type="password" wire:model="password" autocomplete="new-password">
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="settings-password-confirmation">Confirme a nova senha</label>
                        <input id="settings-password-confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
                <div class="settings-actions"><button class="primary-button" type="submit" wire:loading.attr="disabled" wire:target="save">Salvar alterações</button></div>
            </form>
        </section>
        <p class="page-footer">Prainha Vôlei · Sistema de gerenciamento</p>
    </main>
</div>
