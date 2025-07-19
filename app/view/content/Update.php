<?php ?>
<div id="formContainer">
    <h2 class="mb-4">Update Account</h2>
    <form action="/system/<?= $data['controller']?>/update" method="post" enctype="multipart/form-data" class="row g-3">
        <input type="hidden" name="updateId" id="updateId" class="form-control" value="<?php echo $data['id'] ?>">

        <div class="col-md-6">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo $data['name'] ?>">
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo $data['email'] ?>">
            <?php if (!empty($data['emailErr'])): ?>
                <small class="text-danger"><?= $data['emailErr'] ?></small>
            <?php endif ?>
        </div>

        <?php if ($data['controller'] === 'admin'): ?>
            <div class="col-md-6">
                <label class="form-label d-block">Role:</label>
                <div class="form-check form-check-inline">
                    <input type="radio" name="role" value="1" id="super-admin" class="form-check-input"
                    <?php echo $data['role'] == 1 ? "checked" : "" ?>>
                    <label for="superAdmin" class="form-check-label">Super Admin</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="role" value="2" id="admin" class="form-check-input"
                        <?php echo $data['role'] == 2 ? "checked" : "" ?>>
                    <label for="admin" class="form-check-label">Admin</label>
                </div>
            </div>
        <?php endif ?>

        <div class="col-md-12">
            <label for="avatar" class="form-label">Upload Avatar:</label>
            <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
            <?php if (!empty($data['fileErr'])): ?>
                <small class="text-danger"><?= $data['fileErr'] ?></small>
            <?php endif ?>
        </div>

        <div class="col-12 d-flex justify-content-between mt-3">
            <a href="/system/<?= $data['controller']?>/list" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>
</div>
