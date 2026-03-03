@extends('layouts.admin')

@section('namaPage', 'Edit Lowongan Kerja')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('karier.index') }}" class="btn btn-secondary mr-3">
                <i class="fa-solid fa-xmark"></i>
            </a>
            <h5 class="m-0 font-weight-bold">Form Edit Lowongan</h5>
        </div>
    </div>
    <div class="card-body">
        <form id="karierForm" action="{{ route('karier.update', $karier->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_kota">Kota <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_kota') is-invalid @enderror" id="nama_kota" name="nama_kota" value="{{ old('nama_kota', $karier->nama_kota) }}" required>
                        @error('nama_kota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="posisi">Posisi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('posisi') is-invalid @enderror" id="posisi" name="posisi" value="{{ old('posisi', $karier->posisi) }}" required>
                        @error('posisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email Penerima <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $karier->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="responsibilities">Responsibilities <span class="text-danger">*</span></label>
                <textarea class="form-control tinymce-editor @error('responsibilities') is-invalid @enderror" id="responsibilities" name="responsibilities">{{ old('responsibilities', $karier->responsibilities) }}</textarea>
                @error('responsibilities')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="qualification">Qualifications <span class="text-danger">*</span></label>
                <textarea class="form-control tinymce-editor @error('qualification') is-invalid @enderror" id="qualification" name="qualification">{{ old('qualification', $karier->qualification) }}</textarea>
                @error('qualification')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
                    Simpan Perubahan
                </button>
            </div>

            <!-- Modal Konfirmasi -->
            <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menyimpan perubahan Lowongan ini?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="confirmSubmit">Ya, Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/rijrac2uxn06a1q296snq7j1fi420fd29r3lc1o12yzq6fwv/tinymce/8/tinymce.min.js"
    referrerpolicy="origin" crossorigin="anonymous"></script>

<script>
    tinymce.init({
        selector: '.tinymce-editor',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        content_style: 'img{max-width:100%;height:auto;}',
        relative_urls: false,
        remove_script_host: false,
        convert_urls: false,
        document_base_url: '{{ url('/') }}/',
        image_class_list: [{
            title: 'Responsive',
            value: 'img-fluid'
        }, ],
        setup: (editor) => {
            const sync = () => editor.save();
            editor.on('change input undo redo', sync);
        },
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();

            xhr.open('POST', '{{ route('tinymce.upload') }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            xhr.withCredentials = true;

            xhr.upload.onprogress = (e) => {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = () => {
                if (xhr.status !== 200) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }

                let json;
                try {
                    json = JSON.parse(xhr.responseText);
                } catch (e) {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                if (!json || typeof json.location !== 'string') {
                    reject('Invalid response: ' + xhr.responseText);
                    return;
                }

                resolve(json.location);
            };

            xhr.onerror = () => {
                reject('Image upload failed due to a network error.');
            };

            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }),
    });

    // Submit form after confirmation
    document.getElementById('confirmSubmit').addEventListener('click', function() {
        document.getElementById('karierForm').submit();
    });
</script>
@endpush
