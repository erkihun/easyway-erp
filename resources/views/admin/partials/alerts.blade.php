@if(session('status'))
<div class="mb-1"><x-ui.alert type="success" :message="session('status')" /></div>
@endif

@if($errors->any())
<div class="mb-1"><x-ui.alert type="error" :message="$errors->first()" /></div>
@endif


