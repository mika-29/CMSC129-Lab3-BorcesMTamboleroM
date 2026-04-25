@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Inventory Management</h2>
            <p class="text-muted">Tracking supplies for critical operations.</p>
        </div>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary px-4 py-2">
            <i class="fas fa-plus me-2"></i> Add New Item
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <small class="text-muted">Total Items</small>
                <h3 class="fw-bold">{{ $items->total() }}</h3>
            </div>
        </div>
         <div class="col-md-4">
            <div class="stat-card h-100 border-danger"
                 style="cursor:pointer; transition: transform 0.2s, box-shadow 0.2s;"
                 onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(239,68,68,0.3)'"
                 onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'"
                 data-bs-toggle="modal" data-bs-target="#criticalModal">
                <div class="d-flex justify-content-between align-items-start">
                    <small class="text-muted text-uppercase fw-semibold" style="letter-spacing:.05em">Critical Status</small>
                    <span class="badge bg-danger bg-opacity-25 text-danger px-2 py-1">
                        <i class="fas fa-triangle-exclamation"></i>
                    </span>
                </div>
                <h3 class="fw-bold mt-2 mb-0 text-danger">{{ $criticalCount ?? 0 }}</h3>
                <small class="text-danger">
                    @if(($criticalCount ?? 0) > 0)
                        {{ $criticalCount }} item(s) need attention · <span class="text-decoration-underline">View all</span>
                    @else
                        All items sufficiently stocked
                    @endif
                </small>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <small class="text-muted">Newest Addition</small>
                <h3 class="fw-bold">{{ $items->first()->name ?? 'N/A' }}</h3>
                <small class="text-muted">Added recently</small>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="criticalModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background-color:#1f2937; border:1px solid #374151;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger fw-bold">
                        <i class="fas fa-triangle-exclamation me-2"></i> Critical Items
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-2">
                    @if($criticalItems->isEmpty())
                        <p class="text-muted fst-italic text-center py-4">
                            <i class="fas fa-check-circle text-success fa-2x mb-2 d-block"></i>
                            No critical items. All stock levels are sufficient.
                        </p>
                    @else
                        <p class="text-muted mb-3">These items are at or below their minimum stock level and require restocking.</p>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover align-middle mb-0">
                                <thead style="background-color:#111827;">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Qty</th>
                                        <th>Min Stock</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($criticalItems as $critical)
                                    <tr>
                                        <td class="fw-bold">{{ $critical->name }}</td>
                                        <td class="text-muted">{{ $critical->category }}</td>
                                        <td>{{ $critical->quantity }}</td>
                                        <td>{{ $critical->minimum_stock }}</td>
                                        <td>
                                            @if($critical->quantity <= 0)
                                                <span class="badge badge-out-of-stock">OUT OF STOCK</span>
                                            @else
                                                <span class="badge badge-low-stock">LOW STOCK</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('inventory.edit', $critical) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-edit"></i> Restock
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs border-0 mb-3" id="inventoryTabs">
        <li class="nav-item">
            <a class="nav-link active bg-transparent text-white border-0 border-bottom border-primary" href="#">Active Inventory</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-muted border-0" href="{{ route('inventory.trashed') }}">Trash</a>
        </li>
    </ul>

    {{-- ===================== SEARCH & FILTER FORM ===================== --}}
    <form method="GET" action="{{ route('inventory.index') }}" class="mb-3">
        <div class="row g-2 align-items-end">

            {{-- Search --}}
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        class="form-control bg-dark border-secondary text-white"
                        placeholder="Search by name, category..."
                        value="{{ request('search') }}"
                    >
                </div>
            </div>

            {{-- Filter: Status --}}
            <div class="col-md-2">
                <select name="status" class="form-select bg-dark border-secondary text-white">
                    <option value="">All Status</option>
                    <option value="in_stock"   {{ request('status') === 'in_stock'   ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock"  {{ request('status') === 'low_stock'  ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_stock"  {{ request('status') === 'out_stock'  ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            {{-- Filter: Category --}}
            <div class="col-md-2">
                <select name="category" class="form-select bg-dark border-secondary text-white">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Buttons --}}
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times"></i>
                </a>
            </div>

        </div>
        {{-- Active filter badges --}}
        @if(request('search') || request('status') || request('date_from') || request('date_to'))
        <div class="mt-2 d-flex gap-2 flex-wrap">
            <small class="text-muted align-self-center">Active filters:</small>
            @if(request('search'))
                <span class="badge bg-primary">Search: "{{ request('search') }}"</span>
            @endif
            @if(request('status'))
                <span class="badge bg-secondary">Status: {{ ucfirst(str_replace('_', ' ', request('status'))) }}</span>
            @endif
            @if(request('category'))
                <span class="badge bg-secondary">Category: {{ request('category') }}</span>
            @endif
        </div>
        @endif
    </form>
    {{-- ================================================================ --}}

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="bg-light bg-opacity-10">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Expiration</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td><span class="text-muted">{{ $item->category }}</span></td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                @if($item->quantity <= 0)
                                    <span class="badge badge-out-of-stock">OUT OF STOCK</span>
                                @elseif($item->is_low_stock)
                                    <span class="badge badge-low-stock">LOW STOCK</span>
                                @else
                                    <span class="badge badge-in-stock">IN STOCK</span>
                                @endif
                            </td>

                            <td>
                                @if($item->expiration_date)
                                    {{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}
                                    @if($item->expiry_status == 'expired')
                                        <span class="text-danger ms-1">⚠️ Expired</span>
                                    @elseif($item->expiry_status == 'warning')
                                        <span class="text-warning ms-1">⚠️ Near Expiry</span>
                                    @endif
                                @else
                                    <span class="text-muted">No Expiration</span>
                                @endif
                            </td>

                            <td class="pe-4 text-end">
                                <a href="{{ route('inventory.edit', $item) }}" class="btn btn-sm btn-outline-info me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ===================== FLOATING CHATBOT ===================== --}}
<div class="floating-chatbot">
    <button type="button" id="chatbotToggle" class="chatbot-toggle">
        <i class="fas fa-comment-dots"></i>
    </button>

    <div id="chatbotBox" class="chatbot-box">
        <div class="chatbot-header">
            <div>
                <h6 class="mb-0 fw-bold">Inventory Assistant</h6>
                <small>Online</small>
            </div>

            <button type="button" id="chatbotClose" class="chatbot-close">
                &times;
            </button>
        </div>

        <div class="chatbot-body">
            <div class="chatbot-message bot">
                Hello! How can I help you today?
            </div>
        </div>

        <div class="chatbot-footer">
            <input type="text" placeholder="Type a message..." disabled>
            <button type="button" disabled>
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
    .floating-chatbot {
        position: fixed;
        right: 25px;
        bottom: 25px;
        z-index: 9999;
    }

    .chatbot-toggle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        background: #0d6efd;
        color: white;
        font-size: 24px;
        box-shadow: 0 8px 24px rgba(13, 110, 253, 0.4);
        transition: 0.2s ease;
    }

    .chatbot-toggle:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(13, 110, 253, 0.6);
    }

    .chatbot-box {
        display: none;
        width: 340px;
        height: 430px;
        margin-bottom: 15px;
        background: #111827;
        border: 1px solid #374151;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.45);
    }

    .chatbot-header {
        background: #1f2937;
        color: white;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #374151;
    }

    .chatbot-header small {
        color: #22c55e;
        font-size: 12px;
    }

    .chatbot-close {
        background: transparent;
        border: none;
        color: white;
        font-size: 24px;
        line-height: 1;
    }

    .chatbot-body {
        height: 300px;
        padding: 15px;
        background: #0f172a;
        overflow-y: auto;
    }

    .chatbot-message {
        max-width: 80%;
        padding: 10px 12px;
        border-radius: 14px;
        font-size: 14px;
        line-height: 1.4;
    }

    .chatbot-message.bot {
        background: #1f2937;
        color: #e5e7eb;
        border-bottom-left-radius: 4px;
    }

    .chatbot-footer {
        display: flex;
        gap: 8px;
        padding: 12px;
        background: #1f2937;
        border-top: 1px solid #374151;
    }

    .chatbot-footer input {
        flex: 1;
        border: 1px solid #374151;
        border-radius: 999px;
        padding: 9px 14px;
        background: #111827;
        color: white;
        outline: none;
    }

    .chatbot-footer input::placeholder {
        color: #9ca3af;
    }

    .chatbot-footer button {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        background: #0d6efd;
        color: white;
    }

    .chatbot-footer input:disabled,
    .chatbot-footer button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>

<script>
    let chatHistory = [];

    document.addEventListener('DOMContentLoaded', function () {
        const input   = document.querySelector('.chatbot-footer input');
        const sendBtn = document.querySelector('.chatbot-footer button');
        const toggle  = document.getElementById('chatbotToggle');
        const box     = document.getElementById('chatbotBox');
        const close   = document.getElementById('chatbotClose');

        // Enable input and button
        input.disabled   = false;
        sendBtn.disabled = false;

        // Toggle open/close
        toggle.addEventListener('click', function () {
            box.style.display = box.style.display === 'block' ? 'none' : 'block';
            if (box.style.display === 'block') input.focus();
        });

        close.addEventListener('click', function () {
            box.style.display = 'none';
        });

        // Send on button click or Enter key
        sendBtn.addEventListener('click', sendMessage);
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') sendMessage();
        });
    });

    function appendMessage(text, role) {
        const body = document.querySelector('.chatbot-body');
        const div  = document.createElement('div');

        div.classList.add('chatbot-message', role === 'user' ? 'user' : 'bot');
        div.style.marginBottom = '10px';
        div.style.whiteSpace   = 'pre-wrap';

        if (role === 'user') {
            div.style.marginLeft              = 'auto';
            div.style.background              = '#1d4ed8';
            div.style.color                   = 'white';
            div.style.borderBottomRightRadius = '4px';
        }

        div.textContent = text;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }

    function setLoading(isLoading) {
        const input   = document.querySelector('.chatbot-footer input');
        const sendBtn = document.querySelector('.chatbot-footer button');
        input.disabled   = isLoading;
        sendBtn.disabled = isLoading;
        sendBtn.innerHTML = isLoading
            ? '<i class="fas fa-circle-notch fa-spin"></i>'
            : '<i class="fas fa-paper-plane"></i>';
    }

    async function sendMessage() {
        const input   = document.querySelector('.chatbot-footer input');
        const message = input.value.trim();
        if (!message) return;

        input.value = '';
        appendMessage(message, 'user');
        setLoading(true);

        // Typing indicator
        const body   = document.querySelector('.chatbot-body');
        const typing = document.createElement('div');
        typing.id    = 'typingIndicator';
        typing.classList.add('chatbot-message', 'bot');
        typing.style.marginBottom = '10px';
        typing.style.color        = '#9ca3af';
        typing.innerHTML          = '<i class="fas fa-circle-notch fa-spin me-1"></i> Thinking...';
        body.appendChild(typing);
        body.scrollTop = body.scrollHeight;

        try {
            const response = await fetch('{{ route("chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ message, history: chatHistory }),
            });

            const data = await response.json();
            document.getElementById('typingIndicator')?.remove();

            if (data.error) {
                appendMessage('⚠️ ' + data.error, 'bot');
            } else {
                appendMessage(data.reply, 'bot');
                // Save to history for context
                chatHistory.push({ role: 'user',  text: message });
                chatHistory.push({ role: 'model', text: data.reply });
                // Keep only last 10 entries (5 exchanges)
                if (chatHistory.length > 10) chatHistory = chatHistory.slice(-10);
            }
        } catch (e) {
            document.getElementById('typingIndicator')?.remove();
            appendMessage('⚠️ Something went wrong. Please try again.', 'bot');
        } finally {
            setLoading(false);
            document.querySelector('.chatbot-footer input').focus();
        }
    }
</script>
{{-- ================================================================ --}}

@endsection
