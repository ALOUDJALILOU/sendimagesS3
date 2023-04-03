@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('message'))
            <div class="alert alert-success text-center col-md-6 mx-auto my-3" role="alert">
                {{ session('message') }}
            </div>
        @elseif (session('error'))
            <div class="alert alert-danger text-center col-md-6 mx-auto my-3" role="alert">
                {{ session('error') }}
            </div>
        @endif
        <div class="card col-md-8 mx-auto p-2">
            <h5 class="card-title text-center bg-back">Enregistrement d'une image sur AWS S3</h5>
            <div class="card-body">
                <form id="upload-form" action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row mb-3">
                        <label class="form-label">Choisir des images</label>
                        <input type="file" required name="images[]" class="form-control bg-back m-2" multiple>
                    </div>
                    <div class="row">
                        <div class="text-right">
                            <button type="submit" class="btn btn-success m-3">Envoyer les images</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="progress mb-3">
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                    aria-valuemax="100" style="width: 0%" id="progress-bar"></div>
            </div>
            <div id="progress-message">0%</div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(event) {
                event.preventDefault();
                var form_data = new FormData($('form')[0]);

                $.ajax({
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                var percent = Math.round((event.loaded / event.total) *
                                    100);
                                $('#progress-bar').attr('aria-valuenow', percent).css(
                                    'width', percent + '%').text(percent + '%');
                                $('#progress-message').text(percent + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    url: $('form').attr('action'),
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#progress-bar').removeClass(
                            'progress-bar-animated progress-bar-striped').addClass(
                            'bg-success').text('Téléchargement terminé');
                        $('#progress-message').html(
                            '<div class="alert alert-success text-center col-md-6 mx-auto my-3" role="alert">' +
                            response.message + '</div>');
                    }
                });
            });
        });
    </script>
@endsection
