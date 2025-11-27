{{-- @include('backend.admins.layouts.base-header') --}}
@include('backend.admins.layouts.base-styles')
@include('backend.admins.layouts.base-scripts')

<div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
        <div class="col-md-4"></div>
          <div class="col-md-4">
            <div class="card card-primary">
              <div class="card-header"><h4>Admin Login</h4></div>

              <div class="card-body">
                <form class="needs-validation" novalidate="" id="adminLoginForm">
                    @csrf

                  <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control" name="email" tabindex="1" autofocus value="{{ old('email') }}">
                    <div class="invalid-feedback">
                      Please fill in your email
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="d-block">
                    	<label for="password" class="control-label">Password</label>
                      {{-- <div class="float-right">
                        <a href="{{ route('admin.forget-password') }}" class="text-small">
                          {{__('admin.Forgot Password?')}}
                        </a>
                      </div> --}}
                    </div>
                    <input id="password" type="password" class="form-control" name="password" tabindex="2">
                    <div class="invalid-feedback">
                      please fill in your password
                    </div>
                  </div>

                  <div class="form-group">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember" {{ old('remember') ? 'checked' : '' }}>
                      <label class="custom-control-label" for="remember">Remember Me</label>
                    </div>
                  </div>

                  <div class="form-group">
                    <button id="adminLoginBtn" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        Login
                    </button>
                  </div>
                </form>

              </div>
            </div>
            <div class="simple-footer">
              Copyright &copy; {{ date('Y') }} Hospital Search By Mr Care
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

<script>
    (function($) {
    "use strict";
    $(document).ready(function () {
        $("#adminLoginForm").on('submit', function(e) {
            e.preventDefault();
            var btn = $("#adminLoginBtn");
            btn.html('Loading...').prop('disabled', true);

            $.ajax({
                url: "{{ route('admins.store-login') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    btn.html('Login').prop('disabled', false);
                    if(response.success) {
                        toastr.success(response.success);
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    }
                    if(response.error) {
                        toastr.error(response.error);
                    }
                },
                error: function(xhr) {
                    btn.html('Login').prop('disabled', false);
                    if(xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        if(errors.email) {
                            toastr.error(errors.email[0]);
                        }
                        if(errors.password) {
                            toastr.error(errors.password[0]);
                        }
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });

        // Enter key support
        $(document).on('keypress', '#email, #password', function (e) {
            if(e.which == 13) {
                e.preventDefault();
                $("#adminLoginForm").submit();
            }
        });
    });
    })(jQuery);
</script>

@include('backend.admins.layouts.base-footer')