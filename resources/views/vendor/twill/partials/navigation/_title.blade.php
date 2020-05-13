<h1 class="header__title">
    <a href={{ config('twill.enabled.dashboard') ? route('admin.dashboard') : '#' }}>
        <img src="/img/ase_logo.jpg" alt="{{ config('app.name') }}" class="header-logo"/>
        <!--<span class="envlabel">
            {{ app()->environment() === 'production' ? 'prod' : app()->environment() }}
        </span>-->
    </a>
</h1>
