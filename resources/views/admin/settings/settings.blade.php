@extends('twill::layouts.settings')

@section('contentFields')
    @formField('input', [
        'label' => 'Notification Emails',
        'name' => 'notification_emails',
        'textLimit' => '2096',
        'note' => 'Separate Multiple Addresses by Commas',
        'disabled' => in_array(\Auth::guard('twill_users')->user()->role, ['SUPERADMIN', 'ADMIN']) ? false : true 
    ])
    @formField('input', [
        'label' => 'Fee Assess Amount',
        'name' => 'fee_assess',
        'disabled' => in_array(\Auth::guard('twill_users')->user()->role, ['SUPERADMIN', 'ADMIN']) ? false : true  
    ])
@stop