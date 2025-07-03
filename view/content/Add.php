<?php ?>
<div id="formContainer">
    <h2 class="mb-4">Account Information</h2>
    <form action="/system/admin/add" method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Full Name" required>
            <?php if (!empty($data['nameErr'])): ?>
                <small class="text-danger"><?= $data['nameErr'] ?></small>
            <?php endif ?>
        </div>

        <div class="col-md-6">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="abc@gmail.com" required>
            <?php if (!empty($data['emailErr'])): ?>
                <small class="text-danger"><?= $data['emailErr'] ?></small>
            <?php endif ?>
        </div>

        <div class="col-md-6">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" class="form-control" minlength="8" required>
            <?php if (!empty($data['passwordErr'])): ?>
                <small class="text-danger"><?= $data['passwordErr'] ?></small>
            <?php endif ?>
        </div>

        <div class="col-md-6">
            <label class="form-label d-block">Role:</label>
            <div class="form-check form-check-inline">
                <input type="radio" name="role" value="super-admin" id="superAdmin" class="form-check-input" required>
                <label for="superAdmin" class="form-check-label">Super Admin</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" name="role" value="admin" id="admin" class="form-check-input">
                <label for="admin" class="form-check-label">Admin</label>
            </div>
            <?php if (!empty($data['roleErr'])): ?>
                <small class="text-danger"><?= $data['roleErr'] ?></small>
            <?php endif ?>
        </div>

        <div class="col-md-12">
            <label for="avatar" class="form-label">Upload Avatar:</label>
            <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
            <?php if (!empty($data['fileErr'])): ?>
                <small class="text-danger"><?= $data['fileErr'] ?></small>
            <?php endif ?>
        </div>

        <div class="col-12 d-flex justify-content-between mt-3">
            <a href="/system/admin" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </form>
</div>

