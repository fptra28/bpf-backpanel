@extends('layouts.admin')

@section('namaPage', 'Edit Produk JFX')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('jfx.index') }}" class="btn btn-secondary mr-3">
                <i class="fa-solid fa-xmark"></i>
            </a>
            <h5 class="m-0 font-weight-bold">Form Edit Produk JFX</h5>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('jfx.update', $produk->id) }}" method="POST" enctype="multipart/form-data"
            id="productForm">
            @csrf
            @method('PUT')
            <!-- Menambahkan method PUT untuk update -->
            <div class="form-group">
                <label for="image">Gambar Produk</label>
                <div class="mb-2">
                    <img src="{{ asset('img/produk/jfx/' . $produk->image) }}" alt="{{ $produk->name }}" width="150">
                </div>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                <small>Biarkan kosong jika tidak ingin mengubah gambar</small>
                @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">Nama Produk</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name', $produk->name) }}">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi"
                    rows="3">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="specs">Spesifikasi</label>
                <textarea class="form-control tinymce-editor @error('specs') is-invalid @enderror" id="specs"
                    name="specs" rows="8">{{ old('specs', $produk->specs) }}</textarea>
                @error('specs')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#confirmModal">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengubah produk ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmSubmit">Ya, Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- TinyMCE --}}
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

    // JavaScript untuk mengirim form setelah konfirmasi
    document.getElementById('confirmSubmit').addEventListener('click', function() {
        document.getElementById('productForm').submit();
    });
</script>
@endsection
