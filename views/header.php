<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusPulse - Campus Event Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dark-red: #800000;
            --gold: #d4af37;
            --rich-black: #121212;
            --card-bg: #1e1e1e;
        }
        body { 
            background-color: var(--rich-black); 
            color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        .navbar-custom {
            background-color: #0a0a0a;
            border-bottom: 2px solid var(--gold);
            padding: 12px 0;
        }
        .navbar-brand { 
            font-weight: 700; 
            color: var(--gold) !important; 
            font-size: 1.6rem;
        }
        .hero-banner { 
            background: linear-gradient(135deg, #4a0000 0%, #800000 50%, #1a0000 100%); 
            color: white; 
            padding: 50px 0; 
            margin-bottom: 30px; 
            border-bottom: 3px solid var(--gold);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        }
        .hero-banner h1 {
            color: var(--gold);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
        }
        .card-custom { 
            background-color: var(--card-bg);
            border: 1px solid #333;
            border-radius: 12px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.5); 
            transition: transform 0.2s, border-color 0.2s; 
            color: #fff;
        }
        .card-custom:hover { 
            transform: translateY(-5px); 
            border-color: var(--gold);
        }
        .badge-gold {
            background-color: var(--gold);
            color: #000;
            font-weight: 600;
        }
        .btn-gold {
            background-color: var(--gold);
            color: #000;
            font-weight: 700;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-gold:hover {
            background-color: #b89628;
            color: #000;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
        }
        .btn-outline-gold {
            border: 1px solid var(--gold);
            color: var(--gold);
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-outline-gold:hover {
            background-color: var(--gold);
            color: #000;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.4);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fa-solid fa-graduation-cap me-2"></i>CampusPulse</a>
        <button class="navbar-toggler navbar-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto d-flex gap-2">
                <a href="index.php" class="btn btn-gold text-decoration-none">
                    <i class="fa-solid fa-calendar-days me-2"></i>Events Catalog
                </a>
                <a href="#" class="btn btn-outline-gold text-decoration-none">
                    <i class="fa-solid fa-user-check me-2"></i>My Registrations
                </a>
            </div>
        </div>
    </div>
</nav>