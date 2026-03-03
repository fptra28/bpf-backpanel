@extends('layouts.admin')

@section('namaPage', 'Profile Website')

@section('main-content')
@if(session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

{{-- Card Konten --}}
<div id="cardContent" class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">Konten Profil</h5>
        @if($profile && $profile->content)
        <div class="d-flex gap-2" id="actionButtons">
            <button class="btn btn-primary mr-2" id="toggleFormButton">Edit Konten</button>
            <button class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal">Hapus
                Konten</button>
        </div>
        @else
        <div id="actionButtons">
            <button class="btn btn-primary" id="toggleFormButton">Tambah Konten</button>
        </div>
        @endif
    </div>
    <div class="card-body">
        <div id="profileContent">
            @if($profile && $profile->content)
            <div>{!! $profile->content !!}</div>
            @else
            <p>Tidak ada konten profil. Silakan tambah konten baru.</p>
            @endif
        </div>
    </div>
</div>

{{-- Card Form --}}
<div id="cardForm" class="card shadow mb-4" style="display: none;">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">
            <span>
                @if($profile && $profile->content)
                Edit
                @else
                Tambah
                @endif
            </span>
            <span>Konten Profil</span>
        </h5>
    </div>
    <div class="card-body">
        <form id="profileForm" action="{{ route('profileWeb.storeOrUpdate') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="content">Konten Profil</label>
                <textarea name="content" id="contentForm"
                    class="form-control tinymce-editor">{{ old('content', $profile->content ?? '') }}</textarea>
            </div>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" id="cancelButton">Batal</button>
                <button type="button" class="btn btn-primary" id="saveButton">
                    {{ $profile ? 'Simpan Perubahan' : 'Tambah Konten' }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Konfirmasi Edit --}}
<div class="modal fade" id="confirmEditModal" tabindex="-1" role="dialog" aria-labelledby="confirmEditModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin mengubah atau menambahkan konten profil?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmEditBtn">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Simpan --}}
<div class="modal fade" id="confirmSaveModal" tabindex="-1" role="dialog" aria-labelledby="confirmSaveModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Simpan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menyimpan perubahan pada konten profil?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="confirmSaveBtn">Ya, Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
@if($profile && $profile->id)
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus konten profil ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <form id="deleteProfileForm" action="{{ route('profileWeb.destroy', $profile->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- TinyMCE & Script --}}
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

    document.getElementById('toggleFormButton').addEventListener('click', function () {
        $('#confirmEditModal').modal('show');
    });

    document.getElementById('confirmEditBtn').addEventListener('click', function () {
        $('#confirmEditModal').modal('hide');
        document.getElementById('cardContent').style.display = 'none';
        document.getElementById('cardForm').style.display = 'block';
        document.getElementById('actionButtons').style.display = 'none';
    });

    document.getElementById('saveButton').addEventListener('click', function () {
        $('#confirmSaveModal').modal('show');
    });

    document.getElementById('confirmSaveBtn').addEventListener('click', function () {
        tinymce.triggerSave();
        document.getElementById('profileForm').submit();
    });

    document.getElementById('cancelButton').addEventListener('click', function () {
        document.getElementById('cardForm').style.display = 'none';
        document.getElementById('cardContent').style.display = 'block';
        document.getElementById('actionButtons').style.display = 'flex';
    });
</script>
@endsection
