    <?php
    use Backend\Modal\Auth;
    class LoginAPI {
        public function showLogin() {
            if (Auth::isLoggedIn()) {
                redirect('dashboard'); // if already logged in, redirect
            }
            return view('auth.login', ['title' => 'User Login']);
        }
    }