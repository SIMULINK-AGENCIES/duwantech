@extends('emails.layouts.base')

@section('title', $notificationTitle)

@section('content')
    <div class="alert alert-{{ $notificationType }}">
        <strong>{{ $notificationIcon }} {{ $notificationTitle }}</strong><br>
        {{ $notificationMessage }}
    </div>

    @if($notificationData)
    <h3>Details</h3>
    <div class="card">
        <table class="table">
            @foreach($notificationData as $key => $value)
            <tr>
                <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if($actionUrl)
    <div class="text-center mt-4">
        <a href="{{ $actionUrl }}" class="btn btn-primary">
            {{ $actionText ?? 'View Details' }}
        </a>
    </div>
    @endif

    <p class="text-center text-muted text-small mt-4">
        This is an automated notification from {{ config('app.name') }}.
    </p>
@endsection

@section('tagline', 'Administrative notification')
