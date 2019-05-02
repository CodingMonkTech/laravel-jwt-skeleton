@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">File upload to Digital Ocean</div>

                <div class="card-body">
                    <form id="tutorialsForm" >
                        <div class="input-group mb-3">
                          <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="profile-image">
                            <label class="custom-file-label" for="profile-image">Choose Image</label>
                          </div>
                          <div class="input-group-append">
                            <button class="btn btn-outline-secondary" id="upload" type="button">Upload</button>
                          </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
 
 $(document).on('click', '#upload', function(event) {

    $('#tutorialsForm').ajaxSubmit({
        url: '/tutorials/save-image-spaces',
        type: 'post',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {               
            if (res.status == "success") {                                        
                alert(res.message); 
            }
            
        },
        error: function(object,status,message) {
            var response=JSON.parse(object.responseText)
            
        }
    });     
 });   
  
</script>

@endsection