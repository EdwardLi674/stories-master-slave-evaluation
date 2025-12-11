@extends('layouts.main')
@section('title', 'Story')

@section('page-css')
<link href="{{ asset('src/plugins/src/table/datatable/datatables.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('src/plugins/css/dark/table/datatable/dt-global_style.css') }}">
<link href="{{ asset('src/assets/css/dark/apps/list.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('src/assets/css/dark/components/modal.css') }}" rel="stylesheet" type="text/css">
<style>
@media (max-width: 767px) {
    .mobile-hide {
        display: none;
    }
}
</style>
@endsection

@section('content')
<div class="row" id="cancel-row">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-top-spacing layout-spacing">
        @include('layouts.alerts')
        <h5 class="pt-2 pb-2">Replication</h5>

        <div class="row mb-4 align-items-end">
            <div class="col-md-3">
                <div>
                    <label for="emotion" style="margin-right: 20px">Table on Master</label>
                    <select class="table-select2 form-select form-control-sm" name="tables[]" id="tables"
                        multiple="multiple">
                        @foreach ($tables as $table)
                        <option value="{{ $table }}">{{ $table }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary" id="replicateBtn">Replicate</button>
            </div>
        </div>

        <div class="mt-3" id="replicationContainer" style="display: none;">
            <!-- Completed Message -->
            <p id="completedMessage" style="display: none; font-weight: bold; color: green; margin-top: 10px;">
                ✔ Replication Completed!
            </p>
            <!-- Progress Bar Wrapper -->
            <div id="progressWrapper" style="display: block;">
                <div class="progress">
                    <div id="replicationProgress" class="progress-bar progress-bar-striped progress-bar-animated"
                        style="width: 0%"></div>
                </div>
                <p id="progressText" class="mt-2">0%</p>
            </div>

            <!-- Current Replicating Table -->
            <div id="currentTableBox" style="display:none; margin-top:10px;">
                <strong>Current Replicating Table:</strong>
                <span id="currentTable"></span>
            </div>

            <!-- Completed Table List -->
            <div id="completedBox" style="display: none; margin-top: 20px;">
                <strong>Completed Tables:</strong>
                <ul id="completedTables"></ul>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-js')
<script>
$(document).ready(function() {
    $('.table-select2').select2({
        placeholder: "Select Table",
        allowClear: true,
    });
});

document.getElementById('replicateBtn').addEventListener('click', function() {

    // Get selected tables
    const selected = Array.from(document.getElementById('tables').selectedOptions)
        .map(option => option.value);

    // List of restricted tables
    const restricted = ['admin_notes', 'internal_logs'];

    // Check if any selected table is restricted
    const forbidden = selected.filter(t => restricted.includes(t));

    if (forbidden.length > 0) {
        alert("You cannot replicate the following table(s): " + forbidden.join(', '));
        $('#tables').val(null).trigger('change');
        return; // stop execution
    }

    // If no table selected => show alert and stop
    if (selected.length === 0) {
        alert("Please select at least one table to replicate.");
        return; // stop execution
    }

    // Disable button and show replication container
    const btn = this;
    btn.disabled = true; // disable button while running
    document.getElementById('replicationContainer').style.display = 'block';

    // Start replication
    fetch('{{ route("replicate-table") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            tables: selected
        })
    });

    // Poll progress
    let displayedProgress = 0;

    const interval = setInterval(() => {
        fetch('{{ route("replication-progress") }}')
            .then(res => res.json())
            .then(data => {

                const actualProgress = data.progress;
                const currentTable = data.current_table || '';
                const completedTables = data.completed_tables || [];

                // Smooth progress
                if (displayedProgress < actualProgress) {
                    displayedProgress += 1;
                    if (displayedProgress > actualProgress) {
                        displayedProgress = actualProgress;
                    }
                }

                // Update progress UI
                document.getElementById('replicationProgress').style.width = displayedProgress +
                    '%';
                document.getElementById('progressText').innerText = displayedProgress + '%';

                // Show current table
                document.getElementById('currentTableBox').style.display = "block";
                document.getElementById('currentTable').innerText = currentTable;

                // Hide completed table section until finished
                document.getElementById('completedBox').style.display = "none";
                document.getElementById('completedMessage').style.display = "none";

                // When finished
                if (displayedProgress >= 100) {

                    clearInterval(interval);

                    // Hide current table
                    document.getElementById('currentTableBox').style.display = "none";

                    // Hide progress bar
                    document.getElementById('progressWrapper').style.display = "none";

                    // Show completed message
                    document.getElementById('completedMessage').style.display = "block";

                    // Show completed tables list
                    document.getElementById('completedBox').style.display = "block";

                    const list = document.getElementById('completedTables');
                    list.innerHTML = "";

                    completedTables.forEach(t => {
                        const li = document.createElement("li");
                        li.textContent = t;
                        list.appendChild(li);
                    });

                    // Re-enable button
                    btn.disabled = false;

                    // ✅ Clear Select2 dropdown after replication
                    $('#tables').val(null).trigger('change');
                }
            });
    }, 100);
});
</script>
@endsection