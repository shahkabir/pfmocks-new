@extends('layouts.app')

@section('title', 'User List')

@section('content')

<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-amber-400 text-black">
            <h5 class="mb-0">Users Management</h5>
        </div>

        <div class="card-body">
            <table id="usersTable" class="table table-striped table-bordered table-hover w-100">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Role</th>
                        <th>Verified</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Example static row (replace with dynamic data) -->
                    <tr>
                        {{-- <td></td>
                        <td>John Doe</td>
                        <td>john@example.com</td>
                        <td>017XXXXXXXX</td>
                        <td>
                            <span class="badge badge-primary badge-role">admin</span>
                        </td>
                        <td>
                            <span class="badge badge-success">Yes</span>
                        </td>
                        <td>2025-01-10</td> --}}
                        <td>
                            {{-- <button class="btn btn-sm btn-info">View</button>
                            <button class="btn btn-sm btn-warning">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button> --}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        const dataTable = $('#usersTable').DataTable({
                            serverSide: true,
                            processing: true,
                            ajax: '{{ route("admin.user.list") }}',
                            columns: [
                                { data: 'id', name: 'id' },
                                { data: 'name', name: 'name' },
                                { data: 'email', name: 'email' },
                                { data: 'mobile', name: 'mobile' },
                                { data: 'role', name: 'role' },
                                { data: 'is_verified', name: 'is_verified' },
                                { data: 'created_at', name: 'created_at' },
                                { data: 'action', name: 'action', orderable: false, searchable: false }
                            ],
                            pageLength: 5,
                            lengthMenu: [10, 25, 50, 100],
                            order: [[0, 'desc']],
                            columnDefs: [
                                { orderable: false, targets: 7 } // disable sorting on Actions
                            ]
                        });


        // Handle delete button click
        $('#usersTable').on('click', '.delete-user-btn', function () {
            var userId = $(this).data('id');
            if (confirm('Are you sure you want to delete this user?')) 
            {
                $.ajax({
                    url: '{{ route("admin.user.delete", ":id") }}'.replace(':id', userId),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if(response.status === 'success'){
                            alert('User deleted successfully.');
                            
                            // Reload table data without resetting pagination
                            dataTable.ajax.reload( null, false ); //callback, boolean 
                        } else {
                            console.log(response);
                            alert('Failed to delete user.');
                        }
                    },
                    error: function (xhr) {
                        alert('An error occurred while deleting the user.'+ xhr.responseText);
                    }
                });
            }
        });


        //Edit user inline
        const editableColumns = [1,2, 3]; // Columns: Name, Email, Mobile, Role

        $('#usersTable').on('click', '.edit-user-btn', function () {
            var userId = $(this).data('id');
            console.log(userId);
            var currentRow = $(this).closest('tr');
            console.log(currentRow);

            makeEditable(currentRow);

            // Append Save and Cancel buttons
            var actionCell = currentRow.find('td').last();
            actionCell.html(
                `<button class="btn btn-sm btn-success update-user-btn" data-id="${userId}">Save</button>
                 <button class="btn btn-sm btn-danger delete-user-btn data-id="${userId}"">Delete</button>
            `);

        });

        function makeEditable(currentRow) {
            // Implement inline editing logic here
            currentRow.find('td').each(function(index) {
                console.log(index);
                if(editableColumns.includes(index)) { // Make only first 4 columns editable
                    var cellValue = $(this).text().trim();
                    $(this).html('<input type="text" class="form-control" value="' + cellValue + '"/>');
                }
            });
        }   

        //Save updated user info
        $('#usersTable').on('click', '.update-user-btn', function () {
            var userId = $(this).data('id');
            var currentRow = $(this).closest('tr');

            // Gather updated data
            var updatedData = {};
            currentRow.find('td').each(function(index) {
                if(editableColumns.includes(index)) {
                    var inputVal = $(this).find('input').val().trim();
                    updatedData.id = userId;
                    switch(index) {
                        case 1:
                            updatedData.name = inputVal;
                            break;
                        case 2:
                            updatedData.email = inputVal;
                            break;
                        case 3:
                            updatedData.mobile = inputVal;
                            break;
                    }
                }
            });

            // Send AJAX request to update user
            $.ajax({
                url: '{{ route("admin.user.update") }}',
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...updatedData
                },
                success: function (response) {
                    if(response.status === 'success'){
                        alert('User updated successfully.');
                        dataTable.ajax.reload( null, false ); // Reload table data
                    } else {
                        alert('Failed to update user. ' + response.message);
                    }
                },
                error: function (xhr) {
                    alert('An error occurred while updating the user.' + xhr.responseText);
                }
            });
        });
    });
</script>
@endsection