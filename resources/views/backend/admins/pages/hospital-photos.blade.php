@extends('backend.admins.layouts.base')

@push('title')
    <title>Hospital Photos Gallery | {{ env('APP_NAME') }}</title>
@endpush

@push('styles')
<style>
    .photo-card {
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
    }
    .photo-card:hover {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .photo-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 0.35rem 0.35rem 0 0;
    }
    .photo-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 5px;
    }
    .photo-badge {
        position: absolute;
        top: 10px;
        left: 10px;
    }
    .photo-caption {
        padding: 1rem;
        border-top: 1px solid #e3e6f0;
    }
    .sortable-handle {
        cursor: move;
    }
    .photo-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.35rem;
        padding: 2rem;
        text-align: center;
        background: #f8f9fc;
        transition: all 0.3s ease;
    }
    .photo-upload-area:hover {
        border-color: #4e73df;
        background: #eef2f7;
    }
    .photo-preview {
        max-width: 100px;
        max-height: 100px;
        margin: 5px;
        border-radius: 0.25rem;
    }
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    .primary-badge {
        background: linear-gradient(45deg, #667eea, #764ba2);
    }
</style>
@endpush

@section('page-content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Hospital Photos Gallery</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ route('admins.dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ route('admins.hospitals.index') }}">Hospitals</a></div>
                <div class="breadcrumb-item">Photos Gallery</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Photos Gallery for {{ $hospital->name }}</h4>
                            <div class="card-header-action">
                                <a href="{{ route('admins.hospitals.show', $hospital->id) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> View Hospital
                                </a>
                                <a href="{{ route('admins.hospitals.edit', $hospital->id) }}" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit Hospital
                                </a>
                                <a href="{{ route('admins.hospitals.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible show fade">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert">
                                            <span>×</span>
                                        </button>
                                        {{ session('success') }}
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Photos Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-upload"></i> Upload New Photos</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admins.hospitals.store-photos', $hospital->id) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                                                @csrf
                                                
                                                <div class="photo-upload-area" id="uploadArea">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                    <h5>Drag & Drop Photos Here</h5>
                                                    <p class="text-muted">or click to browse</p>
                                                    <input type="file" name="photos[]" id="photos" multiple 
                                                           accept="image/*" class="d-none" onchange="previewPhotos()">
                                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('photos').click()">
                                                        <i class="fas fa-folder-open"></i> Choose Photos
                                                    </button>
                                                    <small class="form-text text-muted d-block mt-2">
                                                        Maximum 5MB per photo. Supported formats: JPEG, PNG, JPG, GIF
                                                    </small>
                                                </div>

                                                <!-- Photo Previews -->
                                                <div id="photoPreviews" class="mt-3" style="display: none;">
                                                    <h6>Selected Photos:</h6>
                                                    <div id="previewContainer" class="d-flex flex-wrap"></div>
                                                    
                                                    <div class="mt-3">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-upload"></i> Upload Photos
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" onclick="clearPhotos()">
                                                            <i class="fas fa-times"></i> Clear All
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Photos Gallery -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>
                                                <i class="fas fa-images"></i> Photos Gallery 
                                                <span class="badge badge-primary">{{ $hospital->photos_count }}</span>
                                            </h5>
                                            <div class="card-header-action">
                                                <button type="button" class="btn btn-info btn-sm" id="saveOrderBtn" style="display: none;">
                                                    <i class="fas fa-save"></i> Save Order
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if($hospital->photos->count() > 0)
                                                <div class="gallery-grid" id="photosGrid">
                                                    @foreach($hospital->photos as $photo)
                                                    <div class="photo-card" data-id="{{ $photo->id }}">
                                                        <div class="sortable-handle" style="cursor: move; padding: 10px; background: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
                                                            <i class="fas fa-arrows-alt text-muted"></i>
                                                            <small class="text-muted ml-2">Drag to reorder</small>
                                                        </div>
                                                        
                                                        <img src="{{ asset($photo->photo_path) }}" 
                                                             alt="{{ $photo->caption ?? 'Hospital Photo' }}" 
                                                             class="photo-img" style="max-width: 200px"
                                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y4ZjlmYyIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwsIHNhbnMtc2VyaWYiIGZvbnQtc2l6ZT0iMTQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IiM2YzcyN2QiPkltYWdlIE5vdCBGb3VuZDwvdGV4dD48L3N2Zz4='">
                                                        
                                                        <div class="photo-actions">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-{{ $photo->is_primary ? 'success' : 'outline-success' }} set-primary-btn"
                                                                    data-photo-id="{{ $photo->id }}"
                                                                    title="{{ $photo->is_primary ? 'Primary Photo' : 'Set as Primary' }}"
                                                                    {{ $photo->is_primary ? 'disabled' : '' }}>
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-{{ $photo->status ? 'warning' : 'outline-warning' }} toggle-status-btn"
                                                                    data-photo-id="{{ $photo->id }}"
                                                                    title="{{ $photo->status ? 'Disable' : 'Enable' }}">
                                                                <i class="fas fa-{{ $photo->status ? 'eye' : 'eye-slash' }}"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-info edit-caption-btn"
                                                                    data-photo-id="{{ $photo->id }}"
                                                                    data-caption="{{ $photo->caption ?? '' }}"
                                                                    title="Edit Caption">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger delete-photo-btn"
                                                                    data-photo-id="{{ $photo->id }}"
                                                                    title="Delete Photo">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>

                                                        @if($photo->is_primary)
                                                        <div class="photo-badge">
                                                            <span class="badge badge-primary primary-badge">
                                                                <i class="fas fa-crown"></i> Primary
                                                            </span>
                                                        </div>
                                                        @endif

                                                        <div class="photo-caption">
                                                            @if($photo->caption)
                                                                <p class="mb-1 caption-text" id="caption-{{ $photo->id }}">{{ $photo->caption }}</p>
                                                            @else
                                                                <p class="mb-1 text-muted caption-text" id="caption-{{ $photo->id }}">No caption</p>
                                                            @endif
                                                            <small class="text-muted">
                                                                Uploaded: {{ $photo->created_at->format('M d, Y') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                                    <h5>No Photos Uploaded Yet</h5>
                                                    <p class="text-muted">Upload some photos to get started.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Edit Caption Modal -->
<div class="modal fade" id="editCaptionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Photo Caption</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="captionForm">
                    @csrf
                    <div class="form-group">
                        <label for="captionInput">Caption</label>
                        <input type="text" class="form-control" id="captionInput" 
                               placeholder="Enter photo caption" maxlength="255">
                        <small class="form-text text-muted">Maximum 255 characters</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCaptionBtn">Save Caption</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    let currentPhotoId = null;
    let sortable = null;

    $(document).ready(function() {
        // Initialize Sortable
        if (document.getElementById('photosGrid')) {
            sortable = new Sortable(document.getElementById('photosGrid'), {
                handle: '.sortable-handle',
                animation: 150,
                onUpdate: function() {
                    $('#saveOrderBtn').show();
                }
            });
        }

        // Save photo order
        $('#saveOrderBtn').click(function() {
            const photoOrder = [];
            $('#photosGrid .photo-card').each(function(index) {
                photoOrder.push($(this).data('id'));
            });

            $.ajax({
                url: "{{ route('admins.hospitals.update-photo-order', $hospital->id) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    photo_order: photoOrder
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $('#saveOrderBtn').hide();
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Error updating photo order.');
                }
            });
        });

        // Set primary photo
        $(document).on('click', '.set-primary-btn', function() {
            const photoId = $(this).data('photo-id');
            const $btn = $(this);

            $.ajax({
                url: "{{ route('admins.hospitals.set-primary-photo', [$hospital->id, ':photoId']) }}".replace(':photoId', photoId),
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        // Sabhi primary buttons ko reset karein
                        $('.set-primary-btn').removeClass('btn-success').addClass('btn-outline-success').prop('disabled', false);
                        // Current button ko primary banaein
                        $btn.removeClass('btn-outline-success').addClass('btn-success').prop('disabled', true);
                        
                        // Sabhi primary badges hide karein
                        $('.photo-badge').hide();
                        // Current photo ka badge show karein
                        $btn.closest('.photo-card').find('.photo-badge').show();
                        
                        showAlert('success', response.message);
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Error setting primary photo.');
                }
            });
        });

        // Toggle photo status
        $(document).on('click', '.toggle-status-btn', function() {
            const photoId = $(this).data('photo-id');
            const $btn = $(this);

            $.ajax({
                url: "{{ route('admins.hospitals.toggle-photo-status', [$hospital->id, ':photoId']) }}".replace(':photoId', photoId),
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        if (response.status) {
                            $btn.removeClass('btn-outline-warning').addClass('btn-warning')
                                .attr('title', 'Disable').find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                        } else {
                            $btn.removeClass('btn-warning').addClass('btn-outline-warning')
                                .attr('title', 'Enable').find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                        }
                        showAlert('success', response.message);
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Error updating photo status.');
                }
            });
        });

        // Edit caption
        $(document).on('click', '.edit-caption-btn', function() {
            currentPhotoId = $(this).data('photo-id');
            const currentCaption = $(this).data('caption');
            
            $('#captionInput').val(currentCaption);
            $('#editCaptionModal').modal('show');
        });

        // Save caption
        $('#saveCaptionBtn').click(function() {
            const caption = $('#captionInput').val();

            $.ajax({
                url: "{{ route('admins.hospitals.update-photo-caption', [$hospital->id, ':photoId']) }}".replace(':photoId', currentPhotoId),
                type: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                    caption: caption
                },
                success: function(response) {
                    if (response.success) {
                        const $captionElement = $('#caption-' + currentPhotoId);
                        if (caption) {
                            $captionElement.removeClass('text-muted').text(caption);
                        } else {
                            $captionElement.addClass('text-muted').text('No caption');
                        }
                        $('#editCaptionModal').modal('hide');
                        showAlert('success', response.message);
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Error updating caption.');
                }
            });
        });

        // Delete photo
        $(document).on('click', '.delete-photo-btn', function() {
            const photoId = $(this).data('photo-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This photo will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('admins.hospitals.delete-photo', [$hospital->id, ':photoId']) }}".replace(':photoId', photoId);
                }
            });
        });

        // Drag and drop functionality
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('photos');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.style.borderColor = '#4e73df';
            uploadArea.style.background = '#eef2f7';
        }

        function unhighlight() {
            uploadArea.style.borderColor = '#dee2e6';
            uploadArea.style.background = '#f8f9fc';
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            previewPhotos();
        }
    });

    function previewPhotos() {
        const files = document.getElementById('photos').files;
        const previewContainer = document.getElementById('previewContainer');
        const photoPreviews = document.getElementById('photoPreviews');
        
        previewContainer.innerHTML = '';
        
        if (files.length > 0) {
            photoPreviews.style.display = 'block';
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'photo-preview';
                    previewContainer.appendChild(img);
                }
                
                reader.readAsDataURL(file);
            }
        } else {
            photoPreviews.style.display = 'none';
        }
    }

    function clearPhotos() {
        document.getElementById('photos').value = '';
        document.getElementById('photoPreviews').style.display = 'none';
        document.getElementById('previewContainer').innerHTML = '';
    }

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible show fade">
                <div class="alert-body">
                    <button class="close" data-dismiss="alert">
                        <span>×</span>
                    </button>
                    ${message}
                </div>
            </div>
        `;
        
        // Remove existing alerts
        $('.alert-dismissible').remove();
        
        // Prepend new alert
        $('.card-body').prepend(alertHtml);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            $('.alert-dismissible').alert('close');
        }, 3000);
    }
</script>
@endsection

