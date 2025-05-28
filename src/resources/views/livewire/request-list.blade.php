<div>
    <div class="tab-container">
        <button wire:click="switchTab('pending')" class="tab-button {{ $tab == 'pending' ? 'active' : '' }}">承認待ち</button>
        <button wire:click="switchTab('approved')" class="tab-button {{ $tab == 'approved' ? 'active' : '' }}">承認済み</button>
    </div>

    <div class="table-container">
        <table class="request-table">
            <thead>
                <tr>
                    <th class="request-table-header">状態</th>
                    <th class="request-table-header">名前</th>
                    <th class="request-table-header">対象日時</th>
                    <th class="request-table-header">申請理由</th>
                    <th class="request-table-header">申請日時</th>
                    <th class="request-table-header">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($correctionRequests as $correctionRequest)
                <tr>
                    <td class="request-table-data">{{ $correctionRequest->approved ? '承認済み' : '承認待ち' }}</td>
                    <td class="request-table-data">{{ $correctionRequest->user->name }}</td>
                    <td class="request-table-data">{{ Carbon\Carbon::parse($correctionRequest->date)->format('Y/m/d') }}</td>
                    <td class="request-table-data">{{ $correctionRequest->notes }}</td>
                    <td class="request-table-data">{{ Carbon\Carbon::parse($correctionRequest->created_at)->format('Y/m/d') }}</td>
                    <td class="request-table-data"><a href="@if(Auth::guard('admin')->check()){{ route('admin.attendance.detail', ['id' => Carbon\Carbon::parse($correctionRequest->date)->format('Ymd'), 'userId' => $correctionRequest->user_id]) }}@else{{ route('attendance.detail', Carbon\Carbon::parse($correctionRequest->date)->format('Ymd')) }}@endif" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>