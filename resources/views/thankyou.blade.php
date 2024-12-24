<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Success</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success text-center shadow p-4 rounded">
            <h3 class="mb-4">Thank you for verifying your email!</h3>
            <p class="fs-4">{{ $message }}</p>
            <a href="https://gilgalcareprovider.hyperiontech.com.au/" class="btn btn-primary mt-3">Click here to go to the homepage</a>
        </div>
    </div>

    <!-- Add Bootstrap JS (Optional for additional functionality) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
