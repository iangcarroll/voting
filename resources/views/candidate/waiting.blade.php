@extends("app")

@section("content")
<div class="blankslate">
  <h3>You need more nominations.</h3>
  <p>You cannot mark yourself as running until at least one person enters your Facebook ID <i>{{ Auth::user()->facebook_id }}</i> in the Nominate tab.</p>
</div>
@endsection
