<?php ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
          rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr"
          crossorigin="anonymous">
    <title><?php echo $data['area'] ?> Management</title>
</head>
<body class="p-4">
<div class="container">
    <h1 class="mb-4">
        <a href="/system/admin" class="text-decoration-none text-dark">
            <?php echo $data['area']; ?> Management
        </a>
    </h1>


    <div class="row g-2 mb-4 align-items-center">
        <div class="col-auto">
            <form action="/system/admin/add" method="get">
                <button class="btn btn-success" type="submit">Add new</button>
            </form>
        </div>

        <div class="col-auto">
            <button class="btn btn-primary" onclick="showContent('list')">View all </button>
        </div>

        <div class="col">
            <div class="d-flex">
                <input type="search" class="form-control me-2" placeholder="abc@gmail.com / John Cena" id="searchInput">
                <button class="btn btn-outline-secondary" onclick="showContent('search')">Search</button>
            </div>
        </div>
    </div>

    <div id="content-area">
        <?php if(isset($data['function'])): ?>
            <div id="content-page">
                <?php require_once "./view/content/" . $data['function'] . ".php"; ?>
            </div>
        <?php endif ?>
        <?php if (isset($data['notif'])): ?>
            <div id="message-notif">
                <?= $data['notif'] ?>
            </div>
        <?php endif ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous">
</script>
</body>
</html>