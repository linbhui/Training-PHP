
<div id="listContainer">
    <div id="account-status-container">
        <form action="/system/admin/list" method="get">
            <button type="submit" name="account" value="all">All accounts</button>
            <button type="submit" name="account" value="active">Active accounts</button>
            <button type="submit" name="account" value="deleted">Deleted accounts</button>
        </form>
    </div>
    <div id="admin-table-list" class="container mt-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">
                            <form>
                                <input type="checkbox" id="selectAll">
                            </form>
                        </th>
                        <th scope="col">ID</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Created by</th>
                        <th scope="col">Updated by</th>
                        <th scope="col">Account status</th>
                        <th scope="col">Manage</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    for ($i = 0; $i < $data['total']; $i++) {
                        $role = $data[$i]['role'] == 1 ? 'Super Admin' : 'Admin';
                        $status = $color = "";
                        if ($data[$i]['status'] == 1 ){
                            $status = 'Deleted';
                            $color = 'text-danger';
                        } else {
                            $status = 'Active';
                            $color = 'text-success';
                        }
                        $updated_by = "";
                        if (isset($data[$i]['updated_by'])) {
                            $updated_by = $data[$i]['updated_by'];
                        }


                        $row = "<tr>
                        <td><input type='checkbox' class='selectRow' name='id' value='" . $data[$i]['id'] . "'></td>
                        <td>" . $data[$i]['id'] . "</td>
                        <td>" . $data[$i]['name'] . "</td>
                        <td>" . $data[$i]['email'] . "</td>
                        <td>" . $role . "</td>
                        <td>" . $data[$i]['created_by'] . "</td>
                        <td>" . $updated_by . "</td>
                        <td class='" . $color . "'>" . $status . "</td>
                        <td></td>
                        </tr>";
                        echo $row;
                    }
                ?>
                </tbody>
            </table>
        </div>
        <nav>
            <ul class="pagination justify-content-center" id="pagination">
                <?php
                $currentPage = (isset($_GET['page'])) ? $_GET['page'] : 1;
                ?>
                <li class="page-item">
                    <a class="page-link" href="?page=1" aria-label="First">
                        <span aria-hidden="true">&Lang;</span>
                    </a>
                </li>

                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo max(1, $currentPage - 1); ?>" aria-label="Previous">
                        <span aria-hidden="true">&lang;</span>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                    <li class="page-item <?php if ($i == $currentPage) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo min($data['totalPages'], $currentPage + 1); ?>" aria-label="Next">
                        <span aria-hidden="true">&rang;</span>
                    </a>
                </li>

                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $data['totalPages']; ?>" aria-label="Last">
                        <span aria-hidden="true">&Rang;</span>
                    </a>
                </li>
            </ul>
        </nav>

    </div>
</div>
