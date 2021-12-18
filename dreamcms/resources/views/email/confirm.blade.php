@component('vendor.mail.html.message')
    <h1>Подтверждение Email адреса</h1>

    Вы запросили подтверждение данного почтового адреса для игрока {{ $user->login }}. Если вы хотите подтвердить данный адрес, перейдите по ссылке ниже.

    @component('vendor.mail.html.button', ['url' => url('email/confirm/' . $token), 'color' => 'success'])
        Подтвердить почту
    @endcomponent

    С любовью,
    {{ config('app.name') }}
@endcomponent