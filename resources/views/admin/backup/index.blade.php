@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body">
                <!-- Modern Tabbed Interface -->
                <ul class="nav nav-tabs" id="backupTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="database-tab" data-bs-toggle="tab" data-bs-target="#database" type="button" role="tab" aria-controls="database" aria-selected="true">
                            <i class="las la-database mr-1"></i> @lang('Database Backups')
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="application-tab" data-bs-toggle="tab" data-bs-target="#application" type="button" role="tab" aria-controls="application" aria-selected="false">
                            <i class="las la-server mr-1"></i> @lang('Application Backups')
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="restore-tab" data-bs-toggle="tab" data-bs-target="#restore" type="button" role="tab" aria-controls="restore" aria-selected="false">
                            <i class="las la-history mr-1"></i> @lang('Restore Points')
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content mt-4" id="backupTabsContent">
                    <!-- Database Backups Tab -->
                    <div class="tab-pane fade show active" id="database" role="tabpanel" aria-labelledby="database-tab">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title">@lang('Database Backups')</h5>
                            <a href="{{ route('admin.backup.create.database') }}" class="btn btn--primary">
                                <i class="las la-plus mr-1"></i> @lang('Create Database Backup')
                            </a>
                        </div>
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Size')</th>
                                        <th>@lang('Created At')</th>
                                        <th>@lang('Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dbBackups as $backup)
                                        <tr>
                                            <td>{{ $backup['name'] }}</td>
                                            <td>{{ $backup['size'] }}</td>
                                            <td>{{ $backup['date'] }}</td>
                                            <td>
                                                <a href="{{ route('admin.backup.download', ['type' => 'database', 'filename' => $backup['name']]) }}" class="btn btn-sm btn--primary">
                                                    <i class="las la-download"></i> @lang('Download')
                                                </a>
                                                <button class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Are you sure you want to delete this backup?')" data-action="{{ route('admin.backup.delete', ['type' => 'database', 'filename' => $backup['name']]) }}">
                                                    <i class="las la-trash"></i> @lang('Delete')
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="4">@lang('No database backups found')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Application Backups Tab -->
                    <div class="tab-pane fade" id="application" role="tabpanel" aria-labelledby="application-tab">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title">@lang('Application Backups')</h5>
                            <a href="{{ route('admin.backup.create.application') }}" class="btn btn--primary">
                                <i class="las la-plus mr-1"></i> @lang('Create Application Backup')
                            </a>
                        </div>
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Size')</th>
                                        <th>@lang('Created At')</th>
                                        <th>@lang('Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($appBackups as $backup)
                                        <tr>
                                            <td>{{ $backup['name'] }}</td>
                                            <td>{{ $backup['size'] }}</td>
                                            <td>{{ $backup['date'] }}</td>
                                            <td>
                                                <a href="{{ route('admin.backup.download', ['type' => 'application', 'filename' => $backup['name']]) }}" class="btn btn-sm btn--primary">
                                                    <i class="las la-download"></i> @lang('Download')
                                                </a>
                                                <button class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Are you sure you want to delete this backup?')" data-action="{{ route('admin.backup.delete', ['type' => 'application', 'filename' => $backup['name']]) }}">
                                                    <i class="las la-trash"></i> @lang('Delete')
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="4">@lang('No application backups found')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Restore Points Tab -->
                    <div class="tab-pane fade" id="restore" role="tabpanel" aria-labelledby="restore-tab">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="card-title">@lang('Restore Points')</h5>
                            <button class="btn btn--primary" data-bs-toggle="modal" data-bs-target="#createRestorePointModal">
                                <i class="las la-plus mr-1"></i> @lang('Create Restore Point')
                            </button>
                        </div>
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two">
                                <thead>
                                    <tr>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Size')</th>
                                        <th>@lang('Created At')</th>
                                        <th>@lang('Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($restorePoints as $point)
                                        <tr>
                                            <td>{{ $point['name'] }}</td>
                                            <td>{{ $point['size'] }}</td>
                                            <td>{{ $point['date'] }}</td>
                                            <td>
                                                <button class="btn btn-sm btn--warning confirmationBtn" data-question="@lang('Are you sure you want to restore to this point? This will replace your current database.')" data-action="{{ route('admin.backup.restore', ['type' => 'restore_point', 'filename' => $point['name']]) }}">
                                                    <i class="las la-undo-alt"></i> @lang('Restore')
                                                </button>
                                                <button class="btn btn-sm btn--danger confirmationBtn" data-question="@lang('Are you sure you want to delete this restore point?')" data-action="{{ route('admin.backup.delete', ['type' => 'restore_point', 'filename' => $point['name']]) }}">
                                                    <i class="las la-trash"></i> @lang('Delete')
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="4">@lang('No restore points found')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Restore Point Modal -->
<div class="modal fade" id="createRestorePointModal" tabindex="-1" aria-labelledby="createRestorePointModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.backup.create.restore_point') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createRestorePointModalLabel">@lang('Create Restore Point')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">@lang('Restore Point Name')</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <small class="text-muted">@lang('Give a descriptive name to identify this restore point')</small>
                    </div>
                    <div class="form-group mt-3">
                        <label for="description">@lang('Description')</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <small class="text-muted">@lang('Optional description of what this restore point represents')</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary">@lang('Create')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">@lang('Confirmation')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirmationQuestion"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                <form action="" method="POST" id="confirmationForm">
                    @csrf
                    <button type="submit" class="btn btn--danger">@lang('Confirm')</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Handle confirmation modal
        $('.confirmationBtn').on('click', function() {
            var modal = $('#confirmationModal');
            var question = $(this).data('question');
            var action = $(this).data('action');
            
            modal.find('#confirmationQuestion').text(question);
            modal.find('#confirmationForm').attr('action', action);
            
            modal.modal('show');
        });
        
        // Handle active tab persistence
        $(document).ready(function() {
            // Check if there's an active tab in session
            @if(session('active_tab'))
                // Activate the tab based on session data
                $('#{{ session('active_tab') }}-tab').tab('show');
            @endif
        });
    })(jQuery);
</script>
@endpush
@endsection
