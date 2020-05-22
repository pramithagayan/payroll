@extends('twill::layouts.settings')

@section('contentFields')
    @formField('input', [
        'label' => 'Notification Emails',
        'name' => 'notification_emails',
        'textLimit' => '2096',
        'note' => 'Separate Multiple Addresses by Commas'
    ])
    @formField('input', [
        'label' => 'Fee Assess Amount',
        'name' => 'fee_assess',
    ])
@stop