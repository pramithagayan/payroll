<!DOCTYPE html>
<html dir="ltr" lang="{{ config('twill.locale', 'en') }}">
    <head>
        @include('twill::partials.head')
    </head>
    <body class="env env--{{ app()->environment() }}">
        <div class="a17 a17--login">
            <section class="login">
                <form accept-charset="UTF-8" action="{{ $route }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="logo">
                        <img src="/img/ase_logo.jpg" alt="{{ config('app.name') }}" />
                    </div>
                    <h2 class="f--heading login__heading">{{ 'Payroll '.$screenTitle }}</h2>


                    @yield('form')
                </form>
            </section>

            @include('twill::partials.toaster')
        </div>
    </body>
</html>
