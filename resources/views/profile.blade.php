@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="app-content">
    <div class="container-fluid mt-4" style="max-width:720px">

        {{-- Avatar + name banner --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex align-items-center gap-4 py-4">

                {{-- Initials avatar --}}
                @php
                    $words    = array_filter(explode(' ', trim($user->name)));
                    $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice($words, 0, 2))));
                    $colors   = ['#4e73df','#1cc88a','#36b9cc','#e74a3b','#f6c23e','#6f42c1','#fd7e14'];
                    $avatarBg = $colors[ord($initials[0] ?? 'A') % count($colors)];
                @endphp

                <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                     style="width:90px;height:90px;background:{{ $avatarBg }};font-size:2rem;font-weight:700;color:#fff;letter-spacing:1px;user-select:none;">
                    {{ $initials }}
                </div>

                <div>
                    <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        @if($user->role === 'admin')
                            <span class="badge bg-danger">Admin</span>
                        @else
                            <span class="badge bg-primary">Student</span>
                        @endif

                        @if($user->is_verified)
                            <span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-patch-exclamation me-1"></i>Not Verified</span>
                        @endif
                    </div>
                    <div class="text-muted small mt-1">
                        Member since {{ $user->created_at->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Details card --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-lines-fill me-2"></i>Account Information</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless mb-0" style="table-layout:fixed">
                    <colgroup>
                        <col style="width:200px">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr class="border-bottom">
                            <td class="text-muted fw-semibold ps-4 py-3">
                                <i class="bi bi-person me-2"></i>Full Name
                            </td>
                            <td class="py-3 pe-4">{{ $user->name }}</td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted fw-semibold ps-4 py-3">
                                <i class="bi bi-envelope me-2"></i>Email
                            </td>
                            <td class="py-3 pe-4">
                                {{ $user->email ?? '—' }}
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted fw-semibold ps-4 py-3">
                                <i class="bi bi-phone me-2"></i>Mobile
                            </td>
                            <td class="py-3 pe-4">{{ $user->mobile ?? '—' }}</td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted fw-semibold ps-4 py-3">
                                <i class="bi bi-shield-lock me-2"></i>Role
                            </td>
                            <td class="py-3 pe-4">
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-primary">Student</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted fw-semibold ps-4 py-3">
                                <i class="bi bi-patch-check me-2"></i>Verification
                            </td>
                            <td class="py-3 pe-4">
                                @if($user->is_verified)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-secondary">Not Verified</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted fw-semibold ps-4 py-3">
                                <i class="bi bi-hash me-2"></i>User ID
                            </td>
                            <td class="py-3 pe-4 text-muted">#{{ $user->id }}</td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-muted fw-semibold ps-4 py-3">
                                <i class="bi bi-calendar-event me-2"></i>Joined
                            </td>
                            <td class="py-3 pe-4">{{ $user->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold ps-4 py-3">
                                <i class="bi bi-clock-history me-2"></i>Last Updated
                            </td>
                            <td class="py-3 pe-4 text-muted">{{ $user->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
