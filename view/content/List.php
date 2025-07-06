<div id="listContainer" class="container mt-4">
    <div id="account-status-container" class="mb-3 text-center">
        <?php $currentStatus = $_GET['status'] ?? 'active'?>
        <form action="/system/admin/list" method="get" class="btn-group" role="group">
            <button type="submit" name="status" value="active" class="btn btn-outline-secondary
            <?php echo $currentStatus === 'active' ? 'active' : ''?>">Active Accounts</button>
            <button type="submit" name="status" value="deleted" class="btn btn-outline-secondary
            <?php echo $currentStatus === 'deleted' ? 'active' : ''?>">Deleted Accounts</button>
        </form>
    </div>

    <div id="delete-account">

    </div>

    <div id="admin-table-list" class="table-responsive">
        <form id="bulkAction" method="get">
            <table class="table table-hover align-middle table-bordered">
                <thead class="table-light">
                <tr>
                    <th scope="col" class="text-center">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th scope="col" class="text-center">ID</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Email</th>
                    <th scope="col" class="text-center">Role</th>
                    <th scope="col" class="text-center">Created by</th>
                    <th scope="col" class="text-center">Updated by</th>
                    <th scope="col" class="text-center">Account status</th>
                    <th scope="col" class="text-center">Manage</th>
                </tr>
                </thead>
                <tbody>
                <?php for ($i = 0; $i < $data['total']; $i++): ?>
                    <?php
                    $role = $data[$i]['role'] == 1 ? 'Super Admin' : 'Admin';
                    $updated_by = $data[$i]['updated_by'] ?? '';
                    $isDeleted = $data[$i]['status'] == 1;
                    $status = $isDeleted ? 'Deleted' : 'Active';
                    $color = $isDeleted ? 'text-danger' : 'text-success';
                    $action = $isDeleted ? 'recover' : 'update';
                    $buttonName = $isDeleted ? 'Recover' : 'Update';
                    ?>
                    <tr>
                        <td class="text-center">
                            <input type='checkbox' class='selectRow' name='ids[]' value='<?= $data[$i]['id'] ?>'>
                        </td>
                        <td class="text-center"><?= $data[$i]['id'] ?></td>
                        <td><?= htmlspecialchars($data[$i]['name']) ?></td>
                        <td><?= htmlspecialchars($data[$i]['email']) ?></td>
                        <td class="text-center"><?= $role ?></td>
                        <td class="text-center"><?= $data[$i]['created_by'] ?></td>
                        <td class="text-center"><?= $updated_by ?></td>
                        <td class="text-center <?= $color ?> fw-semibold"><?= $status ?></td>
                        <td class="text-center">
                            <button formaction="/system/admin/<?= $action ?>"
                                    formmethod="get" name="id" value="<?= $data[$i]['id'] ?>"
                                    class="btn btn-outline-<?= $isDeleted ? 'success' : 'info' ?>">
                                <?= $buttonName ?>
                            </button>
                        </td>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-danger" formaction="/system/admin/delete">Delete</button>
                <?php if(isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
                    <button type="submit" class="btn btn-success" formaction="/system/admin/recover">Recover</button>
                <?php endif ?>
            </div>
        </form>
        <form id="action">

        </form>
    </div>

    <nav>
        <ul class="pagination justify-content-center mt-4" id="pagination">
            <?php $currentPage = $_GET['page'] ?? 1; ?>
            <li class="page-item">
                <a class="page-link" href="?page=1" aria-label="First">&laquo;</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?= max(1, $currentPage - 1) ?>" aria-label="Previous">&lsaquo;</a>
            </li>

            <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item">
                <a class="page-link" href="?page=<?= min($data['totalPages'], $currentPage + 1) ?>" aria-label="Next">&rsaquo;</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $data['totalPages'] ?>" aria-label="Last">&raquo;</a>
            </li>
        </ul>
    </nav>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.selectRow');

        selectAllCheckbox.addEventListener('change', function () {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });
</script>