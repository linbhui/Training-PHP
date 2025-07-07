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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
            crossorigin="anonymous">
    </script>
    <title><?php echo ucfirst($data['controller']) ?> Management</title>
</head>
<body class="p-4">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <a href="/system/<?= $data['controller'] ?>" class="text-decoration-none text-dark">
                <?php echo ucfirst($data['controller']) ?> Management
            </a>
        </h1>

        <div class="d-flex flex-wrap gap-2">
            <form action="/system/dashboard" method="get" class="mb-0">
                <button type="submit" class="btn btn-outline-info btn-lg">Dashboard</button>
            </form>

            <form action="/system/logout" method="get" class="mb-0">
                <button type="submit" class="btn btn-outline-danger btn-lg">Log Out</button>
            </form>
        </div>
    </div>

    <div class="row g-2 mb-4 align-items-center">
        <div class="col-auto">
            <form action="/system/<?= $data['controller'] ?>/add" method="get">
                <button class="btn btn-success" type="submit">Add new</button>
            </form>
        </div>

        <div class="col-auto">
            <form action="/system/<?= $data['controller'] ?>/list" method="get">
                <button class="btn btn-primary" type="submit">View all</button>
            </form>
        </div>
    </div>
    <div class="row g-2 mb-4 align-items-center">
        <div class="d-flex">
            <form action="/system/<?= $data['controller'] ?>/search" method="get" class="d-flex align-items-center w-100">
                <select name="search-by" id="searchBySelect" class="form-select me-2" style="width: 150px;">
                    <option value="email">By email</option>
                    <option value="name">By name</option>
                </select>
                <input name="search"
                       type="search"
                       class="form-control me-2 flex-grow-1"
                       id="searchInput"
                       placeholder="abc@gmail.com"
                       value="<?= $data['search-term'] ?? '' ?>"
                >
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div id="content-area" class="mt-4">
        <?php if (isset($data['notif'])): ?>
            <div id="message-notif"
                 class="alert alert-<?= $data['result'] == 'Success' ? 'info' : 'danger' ?> text-center">
                <?php require_once "./app/view/content/" . $data['notif'] . ".php"; ?>
            </div>
        <?php endif ?>

        <?php if (isset($data['action'])): ?>
            <div id="content-page" class="card shadow-sm border-0">
                <div class="card-body">
                    <?php require_once "./app/view/content/" . ucfirst($data['action']) . ".php"; ?>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>

<script>
    const searchBySelect = document.getElementById('searchBySelect');
    const searchInput = document.getElementById('searchInput');

    searchBySelect.addEventListener('change', () => {
        const selected = searchBySelect.value;
        searchInput.placeholder = selected === 'by-name'
            ? 'Jane Doe'
            : 'abc@gmail.com';
    });
</script>
</body>
</html>