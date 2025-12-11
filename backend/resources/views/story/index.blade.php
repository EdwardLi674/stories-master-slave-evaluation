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
        <h5 class="pt-2 pb-2">Story List</h5>
        <div class="widget-content widget-content-area br-8">
            <table id="story-list" class="table dt-table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th class="checkbox-column"> No. </th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stories as $story)
                    <tr>
                        <td class="checkbox-column">{{ $loop->index + 1 }}</td>
                        <td>
                            {{ $story->title }}
                        </td>
                        <td class="mobile-hide">
                            <p class="align-self-center mb-0 user-name"
                                style="white-space:pre-wrap; word-wrap:break-word">
                                {{ strlen(strip_tags($story->body)) > 140 ? substr(strip_tags($story->body), 0, 140)."..." : strip_tags($story->body) }}
                            </p>
                        </td>
                        <td>
                            <a class="badge badge-light-primary text-start me-2 action-edit"
                                href="javascript:openEditModal({{ $story }} )">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-edit-3">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                            <a class="badge badge-light-danger text-start action-delete"
                                href="javascript:openDeleteAlert({{ $story->id }});">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-trash">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path
                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                    </path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="story_modal" tabindex="-1" role="dialog" aria-labelledby="addStoryModalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title add-title" id="add_story_title">Add Story</h5>
                <h5 class="modal-title edit-title" id="edit_story_title">Edit Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="modal-body">
                <div class="add-contact-box">
                    <div class="add-contact-content">
                        <form id="story_form" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="story_id" id="story_id">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="contact-name">
                                        <label style="margin-bottom: 4px; color: white">Title*</label>
                                        <input type="text" id="story_title" class="form-control form-control-sm"
                                            name="story_title">
                                    </div>
                                    <div class="contact-name mt-3">
                                        <label style="margin-bottom: 4px; color: white">Description*</label>
                                        <textarea class="form-control" id="story_description" name="story_description"
                                            placeholder="Write the description here..."
                                            style="height: 120px;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal"> <i class="flaticon-delete-1"></i>
                    Discard</button>
                <button id="btn_edit" class="float-left btn btn-success" onclick="submitStory()">Save</button>
                <button id="btn_add" class="btn btn-primary" onclick="submitStory()">Add</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script src="{{ asset('src/plugins/src/table/datatable/datatables.js') }}"></script>
<script src="{{ asset('src/plugins/src/table/datatable/button-ext/dataTables.buttons.min.js') }}"></script>
<script>
var invoiceList = $('#story-list').DataTable({
    "dom": "<'inv-list-top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'l<'dt-action-buttons align-self-center'B>><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f<'toolbar align-self-center'>>>>" +
        "<'table-responsive'tr>" +
        "<'inv-list-bottom-section d-sm-flex justify-content-sm-between text-center'<'inv-list-pages-count  mb-sm-0 mb-3'i><'inv-list-pagination'p>>",

    columnDefs: [{
        targets: 0,
        width: "30px",
        className: "",
        orderable: !1,
    }],
    buttons: [{
        text: 'Add New',
        className: 'btn btn-primary',
        action: function(e, dt, node, config) {
            openAddModal();
        }
    }],
    "order": [
        [0, "asc"]
    ],
    "oLanguage": {
        "oPaginate": {
            "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
            "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
        },
        "sInfo": "Showing page _PAGE_ of _PAGES_",
        "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        "sSearchPlaceholder": "Search...",
        "sLengthMenu": "Results :  _MENU_",
    },
    "stripeClasses": [],
    "lengthMenu": [5, 7, 10, 15],
    "pageLength": 5
});

function openAddModal() {

    $('#story_modal #add_story_title').show();
    $('#story_modal #edit_story_title').hide();

    $('#story_modal #btn_add').show();
    $('#story_modal #btn_edit').hide();
    $('#story_form').attr('action', "{{ route('create-story') }}")
    $('#story_form #story_id').val('')
    $('#story_form #story_title').val('')
    $('#story_form #story_description').val('')

    $('#story_modal').modal('show');
}

function openEditModal(story) {

    $('#story_modal #add_story_title').hide();
    $('#story_modal #edit_story_title').show();

    $('#story_modal #btn_add').hide();
    $('#story_modal #btn_edit').show();
    $('#story_form').attr('action', "{{ route('update-story') }}")
    $('#story_form #story_id').val(story.id)
    $('#story_form #story_title').val(story.title)
    $('#story_form #story_description').val(story.body)

    $('#story_modal').modal('show');
}

function openDeleteAlert(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.createElement("form");
            var input_id = document.createElement("input");
            var input_token = document.createElement("input");

            form.method = "POST";
            form.action = "{{ route('delete-story') }}";

            input_id.value = id;
            input_id.name = "story_id";
            form.appendChild(input_id);

            input_token.value = "{{ csrf_token() }}";
            input_token.name = "_token";
            form.appendChild(input_token);

            document.body.appendChild(form);

            form.submit();
        }
    })
}

function submitStory() {
    const storyTitle = $('#story_title').val();
    const storyDescription = $('#story_description').val();
    const storyId = $('#story_id').val();

    if (!storyDescription || !storyTitle) {
        Swal.fire({
            icon: 'error',
            title: 'The required field should not be empty.',
            width: '420px',
            customClass: {
                icon: 'swal-alert-icon',
                title: 'swal-alert-title'
            }
        })
        return;
    }

    $('#story_form').submit();
}
</script>
@endsection