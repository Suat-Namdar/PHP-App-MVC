<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h1 class="h5 mb-0">Customers</h1>
    </div>
    <div class="card-body">
        <table id="customersTable" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Created At</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    $(function () {
        $('#customersTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: '/admin/customers/dt',
                type: 'POST',
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: 'DataTables verisi yuklenemedi.'
                    });
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'created_at' }
            ],
            order: [[0, 'desc']]
        });
    });
</script>
