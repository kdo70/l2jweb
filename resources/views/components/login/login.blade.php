<div class="warning" style="display: none;">
    <div class="warning-ico"></div>
    <div class="warning-content">
    </div>
    <div class="warning-control">
        <a class="modal-cancel-button" id="warning_close">
            Close
        </a>
    </div>
</div>


<div class="login">
    <form method="POST" class="login-form" action="{{ route('web.login') }}">
        @csrf
        <div class="login-input">
            <label for="login">ID</label>
            <input class="login-text-input" type="text" name="login" value="{{ old('login') }}">
        </div>
        <div class="login-input">
            <label for="password">PWD</label>
            <input class="login-text-input" type="password" name="password">
        </div>
        <div class="login-control">
            <button class="login-button" type="submit">
                Log in
            </button>
            <button class="login-button" type="reset" onclick="location.href='{{route('web.index')}}';">
                Exit
            </button>
        </div>
    </form>
</div>

