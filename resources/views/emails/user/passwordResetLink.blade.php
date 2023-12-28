@component('mail::message')

Hello {{ $user->name }},

You recently requested to reset your password. 

To reset your password, please click the button below or visit the following link: [{{ url('/reset-password/'.$token) }}]({{ url('/reset-password/'.$token) }})

@component('mail::button', ['url' => url('/reset-password/'.$token), 'color' => 'success'])
Reset Password
@endcomponent

Please note that the link is only valid for the next 60 minutes and can only be used once. If you did not make this request, you can safely ignore this email.
@endcomponent
