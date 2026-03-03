@extends('layouts.admin')

@section('namaPage', 'Tambah Berita')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('berita.index') }}" class="btn btn-secondary mr-3">
                <i class="fa-solid fa-xmark"></i>
            </a>
            <h5 class="m-0 font-weight-bold">Form Tambah Berita</h5>
        </div>
    </div>
    <div class="card-body">
        <form id="productForm" action="{{ route('berita.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Status Berita (Pindah ke Paling Atas) -->
            <div class="form-group">
                <label for="status">Status Berita</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="draft" {{ old('status')=='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status')=='published' ? 'selected' : '' }}>Published</option>
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex">
                <div class="form-group w-100 mr-1">
                    <label for="image">Gambar</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image"
                        required>
                    @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group w-100 ml-1">
                    <label for="kategori">Kategori</label>
                    <select class="form-control @error('kategori') is-invalid @enderror" id="kategori" name="kategori"
                        required>
                        <option value="Info & Kegiatan" {{ old('kategori')=='Info & Kegiatan' ? 'selected' : '' }}>Info
                            & Kegiatan</option>
                        <option value="Pengumuman" {{ old('kategori')=='Pengumuman' ? 'selected' : '' }}>Pengumuman
                        </option>
                    </select>
                    @error('kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="judul">Judul Berita</label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul"
                    value="{{ old('judul') }}" required>
                @error('judul')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="isi">Isi Berita</label>
                <textarea class="form-control tinymce-editor @error('isi') is-invalid @enderror" id="isi" name="isi"
                    rows="8" required>{{ old('isi') }}</textarea>
                @error('isi')
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menyimpan Berita ini?
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

    // Kirim form setelah klik konfirmasi
    document.getElementById('confirmSubmit').addEventListener('click', function() {
        document.getElementById('productForm').submit();
    });
</script>
@endsection
