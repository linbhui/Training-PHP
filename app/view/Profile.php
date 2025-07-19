<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
          rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr"
          crossorigin="anonymous">
    <title>User Profile</title>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body card-body d-flex flex-column align-items-center">
                    <h1 class="mb-3">Profile</h1>
                    <img src="<?= $data['avatar']?>"
                         alt="Avatar"
                         class="rounded-circle mb-3"
                         style="width: 240px; height: 240px; object-fit: cover"
                    >
                    <div class="w-100" style="max-width: 350px;">
                        <ul class="list-unstyled mb-4 lh-lg text-start">
                            <li class="mb-2" style="font-size: 24px"><strong>ID: </strong><?= $data['id']?></li>
                            <li class="mb-2" style="font-size: 24px"><strong>Name: </strong><?= $data['name']?></li>
                            <li class="mb-2" style="font-size: 24px"><strong>Email: </strong><?= $data['email']?></li>
                            <li class="mb-2" style="font-size: 24px"><strong>Status: </strong><?= $data['status']?></li>
                            <li class="mb-2" style="font-size: 24px"><strong>Created By: </strong><?= $data['ins_name']?></li>
                        </ul>
                    </div>
                    <form action="/logout" method="get">
                        <button type="submit" class="btn btn-outline-danger btn-lg">Log Out</button>
                    </form>
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

