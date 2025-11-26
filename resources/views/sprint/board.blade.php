@extends('layouts.app')

@section('title', 'Sprint Board')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">{{ $sprintInfo['name'] ?? 'Sprint Board' }}</h4>
        <p class="text-muted mb-0">{{ $sprintInfo['goal'] ?? '' }}</p>
    </div>
    <div class="text-end">
        <small class="text-muted d-block">Duration</small>
        <small class="fw-bold">{{ ($sprintInfo['start_date'] ?? '') . ' — ' . ($sprintInfo['end_date'] ?? '') }}</small>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row gx-3">
                    @foreach($boards as $board)
                        <div class="col-md-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">{{ $board['title'] }}</h6>
                                </div>
                                <div class="card-body">
                                    @foreach($board['item'] as $item)
                                        <div class="mb-3">
                                            <div class="fw-semibold">{{ $item['title'] }}</div>
                                            <small class="text-muted">{{ $item['badge'] ?? '' }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
