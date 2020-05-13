@component('mail::message')

# Gipson Tire Payroll File

A payroll file has been uploaded for Gipson Tire.

<ul>
<li>Number of payees : {{ $data['payeeCount'] }}</li>
<li>Total Credits: {{ $data['totalCredits'] }}</li>
<li>Processed File: <a href="{{ config('app.url').$data['fileLink'] }}" download>{{ $data['fileName'] }}</a></li>
</ul>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
