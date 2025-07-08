<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
          rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr"
          crossorigin="anonymous">
    <title>Admin Dashboard</title>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h1 class="card-title mb-3">Welcome to your dashboard, <?=$data=['name']?>!</h1>
                    <h5 class="card-subtitle mb-4 text-muted">You are:
                        <span class="badge <?= $_SESSION['admin_role'] === 'Super Admin' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= $_SESSION['admin_role']; ?>
                        </span>
                    </h5>


                    <div class="d-grid gap-3 col-6 mx-auto">
                        <form action="/system/admin" method="get">
                            <button type="submit" class="btn btn-outline-primary btn-lg">Admin Management</button>
                        </form>

                        <?php if ($_SESSION['admin_role'] === "Super Admin"): ?>
                            <form action="/system/user" method="get">
                                <button type="submit" class="btn btn-outline-success btn-lg">User Management</button>
                            </form>
                        <?php endif; ?>

                        <form action="/system/logout" method="get">
                            <button type="submit" class="btn btn-outline-danger btn-lg">Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>
</html>
