<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Email Verification
                </div>
                <div class="card-body">
                    @if(session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @else
                        <div class="alert alert-danger">
                            Email verification failed. Please try again.
                        </div>
                    @endif

                    <a href="{{ url('/') }}" class="btn btn-primary">Go to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
