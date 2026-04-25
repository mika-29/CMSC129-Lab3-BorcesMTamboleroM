<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Emergency Inventory System')</title>

    <!-- Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <!-- Emergency Theme -->
    <style>
    :root {
        --bg-dark: #111827;       /* Deep Navy/Black */
        --card-bg: #1f2937;      /* Slate Gray */
        --border-color: #374151;
        --text-main: #f3f4f6;
        --accent-blue: #3b82f6;
        --accent-red: #ef4444;
        --accent-orange: #f59e0b;
        --accent-green: #10b981;
        --text-muted: #c8d0dd;
    }

    body {
        background-color: var(--bg-dark);
        color: var(--text-main) !important;
        font-family: 'Inter', sans-serif;
    }
    h1, h2, h3, h4, h5, .text-muted, p {
        color: var(--text-main) !important;
    }
    .text-muted {
    color: var(--text-muted) !important;
    }

/* Ensure table headers are bright */
    .table thead th {
        color: #ffffff !important;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
    }

    .navbar { background-color: var(--card-bg) !important; border-bottom: 1px solid var(--border-color); }

    .card {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        border-radius: 12px;
    }

    .table { color: var(--text-main); }
    .table-hover tbody tr:hover { background-color: #2d3748; }

    /* Status Badges to match image */
    .badge-in-stock { background-color: rgba(16, 185, 129, 0.2); color: var(--accent-green); border: 1px solid var(--accent-green); }
    .badge-low-stock { background-color: rgba(245, 158, 11, 0.2); color: var(--accent-orange); border: 1px solid var(--accent-orange); }
    .badge-out-of-stock { background-color: rgba(239, 68, 68, 0.2); color: var(--accent-red); border: 1px solid var(--accent-red); }

    .stat-card {
        padding: 1.50rem;
        border-radius: 12px;
        background: #1f2937;
        border: 1px solid #374151;
    }

    .btn-orange {
    background-color: #e8620a;
    color: white;
    transition: background-color 0.2s ease;

    }
    .btn-orange:hover {
        background-color: #c9530a;
        color: white;
    }
    </style>
    @stack('styles')
  </head>

  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-blue">
      <div class="container">
        <a class="navbar-brand" href="{{ route('inventory.index') }}">
          <i class="fas fa-truck-medical"></i> Emergency Inventory
        </a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
    </nav>

    <!-- Alerts -->
    <div class="container mt-3">
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      @endif
    </div>

    <!-- Main Content -->
    <main class="container my-4">
      @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
      <p class="mb-0">
        &copy; {{ date('Y') }} Emergency Preparedness System
      </p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      document.addEventListener('DOMContentLoaded', function(){
        const alerts = document.querySelectorAll('.alert');

        alerts.forEach(function (alertEl){
          setTimeout(function(){
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
            bsAlert.close();
          }, 3000);
        })
      })
    </script>
    @stack('scripts')
  </body>
</html>
